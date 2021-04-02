import archr
import os
import re
import logging
import subprocess
import socket
import tarfile
import time
import traceback

from signal import SIGTERM, SIGKILL
logging.basicConfig(format="%(levelname)s: %(message)s", level=logging.INFO)
l = logging.getLogger(__name__)

class Wrunner:
    MAX_ATTEMPTS = 60

    def __init__(self, target_image, ports=[80],loadlatest=False):
        self.target_name = target_image
        self.cmd = ""
        self.iid = None
        self.in_container_work_dir = ""
        self.using_inited_container = False
        self.loadlatest = loadlatest
        self.running_cmds = []


        self.docker = True
        self.qs_proc = None
        self.local_shell_log_fpath = ""
        self.ports = ports
        self.target = None
        self.ip = None
        self.process = None
        self.shellwrapper_fpath = os.path.join("/tmp", "shell.log")

        if os.path.exists(self.shellwrapper_fpath):
            os.remove(self.shellwrapper_fpath)

    def __exit__(self, exc, value, tb):
        try:
            self.kill()
        except Exception as ex:
            l.error(ex)
        return self

    def __enter__(self):
        return self

    def get_ip(self):
        """
        Extracts the IP from archr using container
        TODO: should use archr's ipv4_address() method but must test first
        @rtype: object
        """
        for cnt in range(0, 5):
            if self.ip is None:
                self.ip = self.target.container.attrs.get("NetworkSettings", {}).get("IPAddress", None)

            if self.ip is None or len(self.ip) < 5:
                networks = self.target.container.attrs.get("NetworkSettings", {}).get("Networks")
                l.info(networks)
                for n, v in networks.items():
                    self.ip = v.get("IPAddress", None)
                    if self.ip is not None and len(self.ip) > 5:
                        break
            if self.ip is None:
                time.sleep(1)
            else:
                break

        return self.ip

    def get_interface(self):
        """
        Get's the network iterface for the container or Crucible's tap
        :rtype: basestring
        """
        if self.docker:
            return "eth0"
        else:
            return f"tap{self.get_iid()}_0"

    def _inject_fault_tosser_lib(self, use32bit):
        """
        Creates the fault_tosser inside the current container using the version stored in hacrs/build-fault-tosser
        :rtype: str
        """
        if use32bit:
            fault_tosser_fpath = os.path.join("/tmp", "lib_fault_tosser.so.32")
        else:
            fault_tosser_fpath = os.path.join("/tmp", "lib_fault_tosser.so")

        if not os.path.exists(fault_tosser_fpath):
            l.info("Getting lib_fault_tosser")
            t: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget("hacrs/build-fault-tosser")

            t.build()

            t.start(labels=[f"frunner-qs"], )

            t.retrieve_into("/Witcher/base/fault_tosser/lib_fault_tosser.so", "/tmp")
            t.retrieve_into("/Witcher/base/fault_tosser/lib_fault_tosser.so.32", "/tmp")
            t.stop()

            assert (os.path.exists(fault_tosser_fpath))

        return fault_tosser_fpath

    def _inject_widash(self, use32bit):
        """
        Creates the widash binaries inside the current container using the version stored in hacrs/build-widash-x86
        :rtype: str
        """
        if use32bit:
            widash_fpath = os.path.join("/tmp", "dash.32")
        else:
            widash_fpath = os.path.join("/tmp", "dash")

        if not os.path.exists(widash_fpath):
            l.info("Getting the wiii little dash-ies")
            t: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget("hacrs/build-widash-x86")

            t.build()

            t.start(labels=[f"frunner-widash-gets"], )

            t.retrieve_into("/Widash/archbuilds/dash", "/tmp")
            t.retrieve_into("/Widash/archbuilds/dash.32", "/tmp")

            t.stop()

            assert (os.path.exists(widash_fpath))

        return widash_fpath

    def _container_setup(self):
        """
        Adds the additional configuration required for docker containers
        :rtype: None
        """
        cdir = os.path.dirname(os.path.abspath(__file__))
        cfg_tar_fpath = os.path.join("/tmp", "witcher_setup.tar")

        use32bit = self.is_docker_32bit()

        fault_tosser_fpath = self._inject_fault_tosser_lib(use32bit)
        widash_fpath = self._inject_widash(use32bit)

        fault_tosser_filename = os.path.basename(fault_tosser_fpath)
        widash_filename = os.path.basename(widash_fpath)

        l.info(f"{fault_tosser_fpath=}, {widash_fpath}")

        ldpreload_fpath = os.path.join("/tmp", "ld.so.preload")
        bash_script_fpath = os.path.join("/tmp", "disable_ssl.sh")
        foriegn_witcher_fpath = os.path.join("/tmp", "foreign_witcher.env")

        with open(foriegn_witcher_fpath, "w") as wfp:
            wfp.write(f"STRICT=1\n")

        with open(ldpreload_fpath, "w") as wfp:
            wfp.write(f"/lib/{fault_tosser_filename}")

        with open(bash_script_fpath, "w") as wfp:
            bash_code = """#! /bin/bash
            find / -name "postgres*.conf" -exec sed -i 's/ssl = on/ssl = off/g' {} \;            
            """
            wfp.write(bash_code)

        with tarfile.open(cfg_tar_fpath, "w") as tar:
            tar.add(fault_tosser_fpath, arcname=fault_tosser_filename)
            tar.add(widash_fpath, arcname=os.path.basename(widash_filename))
            tar.add(ldpreload_fpath, arcname=os.path.basename(ldpreload_fpath))
            tar.add(foriegn_witcher_fpath, arcname="witcher.env")
            # tar.add(bash_script_fpath, arcname=os.path.basename(bash_script_fpath))

        install_proc = None
        try:
            p = self.target.run_command(["ls", "/usr/bin/wget"])
            p.wait()
            if p.returncode != 0:
                l.info("WGET is missing, will attempt to install")
                p = self.target.run_command(["apt-get", "update"])
                stdout, stderr = p.communicate()
                if stderr:
                    l.error(stdout)
                    l.error(stderr)

                install_proc = self.target.run_command(["apt-get", "install", "-y", "wget"])

        except Exception:
            l.error("\033[31mWGET missing and could not install it")
            import traceback
            traceback.print_exc()
            l.error("\033[0m")
            return

        self.target.inject_tarball("/tmp/witcher_setup.tar", cfg_tar_fpath)

        # self.docker_check_call(["tar", "-C", "/tmp", "-xvf", "/tmp/witcher_setup.tar"])#

        # self.docker_check_call(["chmod", "+x", "/tmp/sqlcatcher.tar/disable_ssl.sh"])

        # self.assert_no_fail(["/tmp/sqlcatcher.tar/disable_ssl.sh"])
        self.docker_check_call(["mv", "/bin/sh", f"/bin/bbs"])
        self.docker_check_call(["ln", "-s", "/bin/dash", f"/bin/sh"])

        self.docker_check_call(
            ["cp", "-a", f"/tmp/witcher_setup.tar/{fault_tosser_filename}", f"/lib/{fault_tosser_filename}"])
        l.info(f"COPY CMD = /tmp/witcher_setup.tar/{widash_filename}, /bin/{widash_filename.replace('.32', '')}")
        self.docker_check_call(
            ["cp", f"/tmp/witcher_setup.tar/{widash_filename}", f"/bin/{widash_filename.replace('.32', '')}"])

        self.docker_check_call(["cp", "-a", "/tmp/witcher_setup.tar/ld.so.preload", "/etc/ld.so.preload"])

        self.docker_check_call(["cp", "-a", "/tmp/witcher_setup.tar/witcher.env", "/tmp/witcher.env"])

        self.docker_check_call(["touch", "/tmp/shell.log", "/tmp/sqlcmds.log", "/tmp/sqlerrors.log", "/tmp/s"])

        p = self.target.run_command(
            ["chown", "root:root", "/tmp/shell.log", "/tmp/sqlcmds.log", "/tmp/sqlerrors.log", "/tmp/s",
             "/tmp/witcher.env"])
        p.wait()  # ignore if chown does not exist on target

        self.docker_check_call(["chmod", "666", "/tmp/shell.log", "/tmp/sqlcmds.log", "/tmp/sqlerrors.log", "/tmp/s"])
        self.docker_check_call(["touch", "/tmp/do_witcher_log.env"])

        if install_proc is not None:
            stdout, stderr = install_proc.communicate()
            if stderr:
                l.error(stdout)
                l.error(stderr)
            if install_proc.returncode == 0:

                l.info("\033[33mWGET missing but was able to install it.\033[0m")
            else:
                l.error("\033[31mFAILED to install WGET .\033[0m")

        l.info("\033[32mCompleted fault tosser setup\033[0m")

    def is_docker_32bit(self):
        p = self.target.run_command(["ls", "/lib/"])
        stdout, _ = p.communicate()
        for f in stdout.split(b"\n"):
            if f.startswith(b"lib") and f.endswith(b"so"):
                f = f.decode('latin-1')
                self.target.retrieve_into(f"/lib/{f}", "/tmp")
                lib_fpath = f"/tmp/{f}"
                p = subprocess.Popen(["/usr/bin/file", lib_fpath], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
                stdout, stderr = p.communicate()
                l.info(f"Running file on {lib_fpath} with results of {stdout=}")
                l.info(stderr)
                break
        use32bit = False
        if stdout.find(b"80386") > -1:
            use32bit = True
            l.info("\033[36mUSING 32 bit binary\033[0m")
        return use32bit

    def save_sessions(self, sessinfo):

        for n, v in sessinfo:
            p = self.target.run_command(["find", "/", "-name", f"*{v}*"])
            results, _ = p.communicate()

            if len(results) > 0:
                for r in results.split(b"\n"):
                    if len(r) == 0:
                        continue
                    r = r.decode("latin-1")
                    new_name = r.replace("/", "__SLASH__")
                    l.info(f"\033[35m {r=}")
                    self.docker_check_call(["mkdir", "-p", "/root/sessions"])
                    self.docker_check_call(["cp", r, f"/root/sessions/{new_name}"])

    def command_works(self, testcmd, timeout=5):
        if self.docker:

            l.info(f"Received {testcmd=} now testing in docker")
            if isinstance(testcmd, list):
                p = self.target.run_command(testcmd)
            else:
                outcmd = ["/bin/sh", "-c", testcmd]
                l.info(f"{outcmd=}")
                p = self.target.run_command(outcmd)
            try:
                l.info(f"command sent, waiting for results")
                stdout, stderr = p.communicate(timeout=timeout)
                if p.returncode != 0:
                    l.error(stdout)
                    l.error(stderr)
                    l.error("Comannd failed!!!")
                else:
                    l.info(f"{testcmd} WORKED!!!!")
                return p.returncode == 0
            except TimeoutError as te:
                l.info(f"Command {testcmd} exeeded {timeout} sec timeout")
                return False
            finally:
                p.terminate()
                p.wait()
        else:
            return True  # not supported yet

    def restore_sessions(self):

        sessfn_regex = re.compile(r"^(.*?_)([A-Za-z0-9_\-]{20,48})")
        p = self.target.run_command(["find", "/root/sessions", "-type", "f"])
        results, _ = p.communicate()
        if len(results) > 0:
            l.info(f"{results=}")
            for r in results.split(b"\n"):
                if len(r) == 0:
                    continue
                r = r.decode("latin-1")

                orig_key_fpath = os.path.basename(r).replace("__SLASH__", "/")
                session_path = os.path.dirname(orig_key_fpath)
                self.docker_check_call(["mkdir", "-p", session_path])
                self.docker_check_call(["cp", r, orig_key_fpath])
                self.docker_check_call(["chmod", "666", orig_key_fpath])
                fixedname = os.path.basename(orig_key_fpath)
                res = re.search(sessfn_regex, fixedname)
                if res:
                    fixedname = res.group(1) + "deadc0de10deadc0de20dead26"
                    fixed_sess_fpath = os.path.join(session_path, fixedname)
                    self.docker_check_call(["cp", r, fixed_sess_fpath])
                    self.docker_check_call(["chmod", "666", fixed_sess_fpath])

    def clear_shelllog_history(self):
        self.docker_check_call(["mv", self.shellwrapper_fpath, "/tmp/last_shell.log"])

        self.docker_check_call(["touch", self.shellwrapper_fpath])

        if self.docker:
            self.docker_check_call(["mv", "/tmp/sqlcmds.log", "/tmp/last_sqlcmds.log"])
            self.docker_check_call(["touch", "/tmp/sqlcmds.log"])
            self.docker_check_call(
                ["chmod", "666", "/tmp/shell.log", "/tmp/sqlcmds.log", "/tmp/sqlerrors.log", "/tmp/s"])

    def docker_check_call(self, cmd):
        p = self.target.run_command(cmd)
        stdout, stderr = p.communicate()
        if p.returncode != 0:
            l.error(f"Command sent = {' '.join(cmd)}")
            l.error(f"{stdout=}")
            l.error(f"\033[31m{stderr=}\033[0m")
            raise Exception("Error command failed to run successfully against docker container")
        return p.returncode

    def extract_shell_log_to_local(self):
        """
        Extracts the shell.log file where either dash or qemu system creates a log of exec commands.
        However, the qemu system method does not catch all execs, so the additional logs are also required.
        TODO: combine all the command log methods into a single one
        :rtype: str
        """
        did = self.target.container.attrs["Id"][:6]
        local_shell_log_dir = os.path.join("/tmp", f"shell_{did}")
        self.local_shell_log_fpath = ""
        try:
            self.target.retrieve_into(self.shellwrapper_fpath, local_shell_log_dir)
            self.local_shell_log_fpath = os.path.join(local_shell_log_dir, os.path.basename(self.shellwrapper_fpath))
        except Exception as ex:
            import traceback
            traceback.print_exc()

            self.local_shell_log_fpath = ""

        return self.local_shell_log_fpath

    def extract_shell_log(self):
        """
        Extracts the shell.log and returns the contents while also deleting the temprary file
        :rtype: object
        """
        tmp_fpath = self.extract_shell_log_to_local()
        with open(tmp_fpath, "rb") as rf:
            out = rf.read().decode("latin-1")

        os.remove(tmp_fpath)

        return out


    def _start_docker_target(self):
        """
        Starts up a target that runs inside a docker container using archr
        """
        start_entrypoint = None
        start_cmd = None
        has_mysql = False
        try:
            if not self.loadlatest:
                try:
                    temp_archr: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget(self.target_name)
                    temp_archr.build()
                    temp_cfg = temp_archr.image.attrs['Config']
                    start_entrypoint = temp_cfg.get('Entrypoint', None)
                    start_cmd = temp_cfg.get('Cmd', None)

                    l.info(f"Using start command of {start_cmd=} and {start_entrypoint=}")
                    temp_archr.stop()

                    t: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget(
                        self.target_name + ":apps_inited")
                    t.build()
                    cfg = t.image.attrs['Config']

                    l.info("Found Modified image")

                    t.start(labels=[f"frunner"], )
                    self.target = t

                    self.using_inited_container = True
                    self.restore_sessions()

                except Exception as ex2:  # catching docker.errors.ImageNotFound Error for pre_execed version
                    l.info("Docker image not found, loading original image")

            if not self.using_inited_container or self.loadlatest:
                l.info("Using Original image ")
                t: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget(self.target_name, pull=True)
                t.volumes["/p"] = {'bind': "/p", 'mode': 'rw'}
                t.build()
                cfg = t.image.attrs['Config']
                start_entrypoint = cfg.get('Entrypoint', None)
                start_cmd = cfg.get('Cmd', None)
                t.start(labels=[f"frunner"], )
                self.target = t
                self._container_setup()

            l.info("Started container, gathering intel")

            # t.volumes["/execlog"] = {'bind': "/p/Witcher/execlog/", 'mode': 'rw'}
            # t.target_env = {"LD_PRELOAD","/execlog/libexeclog.so"}

            self.get_ip()

            cdir = os.path.dirname(os.path.abspath(__file__))
            if start_entrypoint:
                if isinstance(start_entrypoint, str):
                    start_entrypoint = start_entrypoint.split(" ")
                l.info(f"Entrypoint command = {' '.join(start_entrypoint)}")
                self.running_cmds.append(self.target.run_command(start_entrypoint))

            if start_cmd:
                if isinstance(start_cmd, str):
                    start_cmd = start_cmd.split(" ")
                l.info(f"Start command = {' '.join(start_cmd)}")
                self.running_cmds.append(self.target.run_command(start_cmd))

            # (should be) harmless attempt to bring mysql up if entrypoint can't seem to do it.
            time.sleep(1)
            self.running_cmds.append(self.target.run_command(["/usr/bin/mysqld_safe"]))

            l.info(f"Started archr target, Found IP={self.ip}, ports={self.ports}")

        except Exception as ex:
            l.error(f"Failed to start archr target {self.target_name} \033[31m")
            traceback.print_exc()
            l.error(ex)
            l.error("\033[0m")

    def wait_for_ports(self):
        """
        Waits for ports to come up in target using nmap, if ports are found then returns True else returns False
        when MAX_ATTEMPTS is reached
        :rtype: bool
        """
        portfails = {}
        for p in self.ports:
            portfails[p] = True
        l.info(portfails)
        attempts = 0
        while True:
            for p in self.ports:

                if portfails[p]:
                    l.info(f"p = {p}")
                    ptrn = rf"{p}/(tcp|udp).{{1,5}}open.*"
                    reg = re.compile(ptrn)
                    nmap_cmd = ["nmap", "-sU", "-sS", f"-p{p}", self.get_ip()]
                    if os.getuid() != 0:
                        nmap_cmd.insert(1, "--privileged")

                    l.info(f"nmap_cmd={nmap_cmd}")

                    output = subprocess.check_output(nmap_cmd)
                    output = output.decode("latin-1")

                    regfa = None
                    for o in output.split("\n"):
                        regfa = reg.match(o)
                        if regfa:
                            break
                    if regfa:
                        protocol = regfa.group(1)
                    else:
                        continue
                    socket_type = socket.SOCK_STREAM if protocol == "tcp" else socket.SOCK_DGRAM
                    with socket.socket(socket.AF_INET, socket_type) as sock:
                        sock.settimeout(2)
                        intp = p
                        if isinstance(p, str):
                            intp = int(p)
                        result = sock.connect_ex((self.ip, intp))
                        if result == 0:
                            portfails[p] = False
                            continue
                        l.info(f"Result = {result} to {p} using TCP {self.ports}")
            l.info(f"portfails. = {portfails}")
            if any(portfails.values()):  # if any of the ports are still failing then try again
                pass
            else:
                break

            attempts += 1
            if attempts >= Wrunner.MAX_ATTEMPTS:
                return False

            l.info(f"#{attempts + 1} ports(s) {[p for p, v in portfails.items() if v]} are not available")
            time.sleep(2)
        l.info("ports should be up ")
        # time.sleep(2) # slight pause for station identification, ports were reporting up but not allowing next connection
        return True

    def start(self):
        """
        Starts up the targets and then waits for the expected network ports to become accessible
        :rtype: object
        """
        self._start_docker_target()

        return self.wait_for_ports()

    def apps_inited(self):
        if self.docker:
            self.apps_inited_in_docker()
        else:
            return self.apps_inited_in_qemu()

    def get_iid(self):
        if self.iid is None:
            self.iid = os.path.basename(os.path.dirname(self.cmd[0]))
        return self.iid

    def apps_inited_in_docker(self):
        try:
            t: archr.targets.DockerImageTarget = archr.targets.DockerImageTarget(f"{self.target_name}:apps_inited")
            t.build()
            return True
        except Exception as ex2:
            return False



    def save(self):
        if self.using_inited_container:
            pass
        else:
            self.target.save(repository=self.target_name, tag=f"apps_inited")


    def get_sql_queries(self):
        outarr = []
        if not self.docker:
            return outarr

        catproc = self.target.run_command(["cat", "/tmp/sqlcmds.log"])

        stdout, _ = catproc.communicate()
        stdout = stdout.decode("latin-1")
        sqlcmd_regex = r"SEND [>]+.{1,3}ERROR:(.*?)END SEND [<]+"

        queries = re.findall(sqlcmd_regex, stdout, flags=re.DOTALL)
        queries = set(queries)
        queries = list(queries)
        queries = sorted(queries, key=lambda x: ('///////' in x, 'SELECT' in x.upper(), x))

        return queries

    def get_addl_commands(self):
        """
        Additional commands are commands retrieved directly from the qemu system logs, which comes from the
        version of Widash running inside the VM. This was necessary b/c sometimes the instrumentation in qemu
        system is not reporting all execs.
        :return:
        """
        outarr = []
        if self.docker:
            return outarr
        log_dir = os.path.join(self.in_container_work_dir, "logs")
        rproc = self.target.run_command(["ls", "-t", log_dir])
        stdout, stderr = rproc.communicate()
        if stderr:
            l.error(f"\033[31mERROR: {stderr}\033[0m")
        logfiles = stdout.decode("latin-1").split("\n")
        if len(logfiles) == 0:
            return outarr
        latest_log = os.path.join(log_dir, logfiles[0])
        try:
            cmd = ["/bin/grep", "^cmd,", latest_log]
            rproc = self.target.run_command(cmd)
            stdout, stderr = rproc.communicate()
            if stderr:
                l.error(f"\033[31mERROR: {stderr}\033[0m")
            outarr = stdout.decode('latin-1').split("\n")
        except subprocess.CalledProcessError as cpe:
            l.error(f"Error with grep of log, {cpe}")

        return outarr

    def kill(self):

        if self.local_shell_log_fpath and os.path.exists(self.local_shell_log_fpath):
            os.remove(self.local_shell_log_fpath)
        for p in self.running_cmds:
            p.terminate()
            p.wait()

        if self.qs_proc:
            l.info(f"Frunner attempting to kill qemu-system docker exec ")
            self.qs_proc.kill()
            self.qs_proc.wait()

        if self.target:
            l.info(f"Frunner attempting to kill docker container ")
            self.target.stop()


if __name__ == "__main__":
    wr = Wrunner("eljonharlicaj/rce_sar2html", loadlatest=True)
    wr.start()

    import ipdb

    print("ipdb.set_trace()")
    print("done")


