import stat

from phuzzer.reporter import Reporter
from urllib.parse import urlparse, urlunparse
from datetime import datetime
from phuzzer import Phuzzer
import subprocess
import pathlib
import random
import shutil
import signal
import time
import json
import glob
import pwd
import sys
import os
import re

WITCH_FAIL = "[\033[31mWitcher\033[0m]"
WITCH_GO = "[\033[32mWitcher\033[0m]"

class Witcher():
    AFLR, AFLHR, WICH, WICR, WICHR, EXWIC, EXWICH, EXWICHR, DEV = "AFLR", "AFLHR", "WICH", "WICR", "WICHR", "EXWIC", "EXWICH", "EXWICHR", "DEV"
    CONFIGURATIONS = ["AFLR", "AFLHR", "WICH", "WICR", "WICHR", "EXWIC", "EXWICH", "EXWICHR", "DEV"]
    WORKING_DIR = os.path.join("/tmp", "output")



    def __init__(self, args):
        random.seed(90210)
        self.testloc = args.testloc # replaced BASETESTDIR
        self.testver = args.testver
        self.dictionary_fn = os.path.join(self.testloc, self.testver,"dict.txt")
        self.seed_path = os.path.join(self.testloc, self.testver, "input")
        self.work_dir = os.path.join(self.testloc, self.testver, "work")
        self.appdir = args.appdir

        path = pathlib.Path(self.seed_path)
        path.mkdir(parents=True, exist_ok=True)
        self.config_loc = os.path.join(self.testloc,args.config)
        if not os.path.isfile(self.config_loc):
            raise ValueError(f"The configuration does not exist at {self.config_loc}, a configuration file is required")

        self.jconfig = json.load(open(self.config_loc,"r"))
        self.fuzzer_target_binary = ""
        self.single_target = args.target
        self.use_reqr = False
        self.affinity = args.affinity

        self.no_fault_escalation = args.no_fault_escalation

        self.env = self.initialize_env()

        self.report_dir = "/results" if os.path.exists("/results") else os.path.join(self.testloc, self.testver)
        self.report_dir = os.path.join(self.report_dir,f"{self.jconfig['testname']}-{self.testver}")
        path = pathlib.Path(self.report_dir)
        path.mkdir(parents=True, exist_ok=True)

        self.fuzz_campaign_status_fn = os.path.join(self.report_dir, "fuzz_campaign_status.json")
        self.fuzz_campaign_status = None
        if os.path.exists(self.fuzz_campaign_status_fn):
            self.fuzz_campaign_status = json.load(open(self.fuzz_campaign_status_fn,"r"))

        self.request_data_fn = os.path.join(self.testloc,"request_data.json")
        self.request_data = json.load(open(self.request_data_fn,"r", encoding='latin-1'))

        self.cores = int(self.jconfig.get("cores", args.cores))
        self.timeout = self.jconfig.get("timeout", args.timeout)
        self.memory = self.jconfig.get("memory", args.memory)
        self.first_crash = self.jconfig.get("first_crash", args.first_crash)
        self.run_timeout = int(self.jconfig.get("run_timeout", 200))
        self.use_qemu = self.jconfig.get("use_qemu")
        self.server_cmd = self.jconfig.get("server_cmd", None)
        self.init_info_shm = self.jconfig.get("init_info_shm", None)
        self.war_path = self.jconfig.get("war_path",None)
        self.server_base_port = self.jconfig.get("server_base_port", 14000)

        self.server_env_vars = self.jconfig.get("server_env_vars", {})
        print(self.server_env_vars)
        self.binary_options = self.jconfig.get("binary_options").split(" ")
        self.server_up_msg = self.jconfig.get("server_up_msg")
        self.server_procs = []
        self.kill = False

        self.saved_seeds = set()

        if args.container_name:
            self.container_info = {'name': args.container_name}
        else:
            self.container_info = None

        self.create_war_filter()
        self.url_filter = args.url_filter


    def save_filesdata(self):
        json.dump(self.fuzz_campaign_status,open(self.fuzz_campaign_status_fn,"w"))

    def initialize_env(self):
        env = os.environ.copy()
        env["LD_LIBRARY_PATH"] = self.jconfig["ld_library_path"] if "ld_library_path" in self.jconfig else ""
        env["AFL_PRELOAD"] = self.jconfig["afl_preload"] if "afl_preload" in self.jconfig else ""
        env["DOCUMENT_ROOT"] = self.appdir
        if self.affinity is not None:
            env["AFL_SET_AFFINITY"] = self.affinity

        direct = self.jconfig.get("direct",{})
        if "mandatory_cookie" in direct:
            env["MANDATORY_COOKIE"] = direct["mandatory_cookie"]
        if "mandatory_get" in direct:
            env["MANDATORY_GET"] = direct["mandatory_get"]
        if "mandatory_post" in direct:
            env["MANDATORY_POST"] = direct["mandatory_post"]

        env["SERVER_NAME"] = env.get("SERVER_NAME","witcher")
        if not self.no_fault_escalation:
            env["STRICT"] = "1"
        self.use_reqr = True if "R" in self.testver else False
        env["AFL_PATH"] = self.jconfig.get("afl_path", "/afl")
        if "H" in self.testver:
            env["AFL_HTTP_DICT"] = "1"
        if self.testver == Witcher.AFLR or self.testver == Witcher.AFLHR:
            if "afl_inst_interpreter_binary" not in self.jconfig:
                raise ValueError("Configuration file is missing 'afl_inst_interpreter_binary'")
            self.fuzzer_target_binary = self.jconfig["afl_inst_interpreter_binary"]
            env["NO_WC_EXTRA"] = "1"
        else:
            if "wc_inst_interpreter_binary" not in self.jconfig:
                raise ValueError("Configuration file is missing 'wc_inst_interpreter_binary'")
            self.fuzzer_target_binary = self.jconfig["wc_inst_interpreter_binary"]
            if self.testver.startswith("WIC"):
                env["WC_INSTRUMENTATION"] = "1"
                env["NO_WC_EXTRA"] = "1"
            elif self.testver.startswith("EX"):
                env["WC_INSTRUMENTATION"] = "1"
        return env

    @staticmethod
    def find_path(urlpath, prior_rootpaths):
        fname = os.path.basename(urlpath)

        for rootpath in prior_rootpaths:
            tmppath = os.path.join(rootpath, urlpath)
            if os.path.exists(tmppath):
                return tmppath

        cmd = ["find", "/", "-path", "/p", "-prune", "-o", "-path", "/proc", "-prune",
               "-o", "-path", "/test", "-prune", "-o", "-path", "/etc", "-prune",
               "-o", "-path", "/var/log", "-prune", "-o", "-path", "/var/spool", "-prune",
               "-o", "-path", "/var/cache", "-prune",
               "-o", "-path", "/var/lib", "-prune", "-o", "-path", "/root", "-prune",
               "-o", "-name", fname]

        #print(f"Command = {' '.join(cmd)}")

        p = subprocess.Popen(cmd, stdout=subprocess.PIPE)
        results, _ = p.communicate()
        #print(f"RESULTS from find = {results}")
        for fpath in sorted(results.split(b'\n'), key=len):
            fpath = fpath.decode("latin-1")
            if fpath.find(urlpath) > -1:
                return fpath
        return ""


    def init_fuzz_campaign_status(self, trial_index):
        if self.fuzz_campaign_status is None:
            self.fuzz_campaign_status = []

        assert (trial_index <= len(self.fuzz_campaign_status))

        if len(self.fuzz_campaign_status) == trial_index:
            last_rootpath = set()
            fcnt = 0
            targets_added = {}
            start_time = datetime.now().strftime("%Y_%m_%d_%H_%M")
            self.fuzz_campaign_status.append({"trial_start": start_time, "trial_complete": False, "targets": []})
            trial = self.fuzz_campaign_status[trial_index]

            for reqkey, req in self.request_data["requestsFound"].items():

                if self.url_filter and re.search(self.url_filter, req["_url"]):
                    pass
                elif not self.url_filter:
                    pass
                else:
                    # did not match filter, will not add url
                    continue

                match_found = False

                is_soapaction = False
                if match_found:
                    url = urlparse(req["_url"])
                else:
                    if re.match(r"http://.*/[a-zA-Z0-9_\-\.]+\.(css|js|toff|woff|jpg|gif|png)\?[0-9a-zA-Z ]*", req["_url"]):
                        print(f"[*] Skipping {req['_url']} b/c static extension")
                        continue

                    if "_headers" in req and ("soapaction" in req["_headers"] or "SOAPACTION" in req["_headers"]):
                        retr_url = req["_headers"].get("soapaction", None)
                        if retr_url is None:
                            retr_url = req["_headers"].get("SOAPACTION", None)
                        url = urlparse(retr_url)
                        is_soapaction = True
                    else:
                        url = urlparse(req["_url"])

                    # if req["_method"].upper() == "GET":
                    #     if len(url.query) + len(req.get("_postData",[])) < 1 :
                    #         print(f"[*] Skipping {reqkey} b/c {url.query} is {len(url.query)} and less than 1")
                    #         continue

                    if url.path.endswith("/") and req["_url"].find("/?") > -1:
                        print(f"[*] Skipping {reqkey} b/c looks like dir listing")
                        continue

                    if req.get("response_status", 200) == 999:
                        print(f"[*] Skipping {reqkey} response status was set to 999")
                        continue


                    if req["_method"].upper() == "POST":
                        if len(url.query) + len(req.get("_postData",[])) < 1:
                            print(f"[*] Skipping {reqkey} b/c no post Data")
                            continue

                if self.container_info:
                    target_path = urlunparse(url)
                else:
                    if self.server_cmd:
                        url = url._replace(query="")
                        target_path = urlunparse(url)

                    else:
                        if self.jconfig.get("afl_inst_interpreter_binary", "").find("php-cgi") > -1:
                            url = urlparse(req["_url"])
                            urlpath = url.path
                            if urlpath.startswith("/"):
                                urlpath = urlpath[1:]

                            target_path = os.path.join(self.appdir, urlpath)
                            print(f"target_path={target_path}")
                            if not os.path.exists(target_path):
                                target_path = Witcher.find_path(urlpath, last_rootpath)
                                last_rootpath.add(target_path.replace(urlpath,""))

                            if url.path.find(".php") == -1 and not url.path.endswith("/"):
                                print(
                                    f"Skipping {url} because php-cgi being used to evaluate but request url is for non php item target_path={target_path}")
                                continue
                        else:
                            target_path = req['_url']


                method = req.get("_method", "GET").upper()
                if 400 <= req.get("response_status", 200) < 500:
                    print(f"[WC] Skipping {req['_url']} b/c of response status during crawling")
                    continue

                if target_path:
                    if target_path.find("HNAP1/Login") > -1:
                        continue
                else:
                    target_path = req["_url"]

                # if request has user input, this only checks if query params or post data is passed in
                if req["_url"].find("?") or req["_url"].find("&") or len(req["_postData"]) > 0:
                    print(f" Fuzzing #{fcnt} at '{target_path}'")
                    fcnt += 1
                    if not self.single_target or target_path.find(self.single_target) > -1:
                        if target_path in targets_added:
                            index = targets_added[target_path]
                            trial["targets"][index]["requests"].append(reqkey)
                            trial["targets"][index]["is_soapaction"] = is_soapaction
                            if method in trial["targets"][index]["methods"]:
                                trial["targets"][index]["methods"][method] += 1
                        else:
                            targets_added[target_path] = len(trial["targets"])
                            trial["targets"].append({"target_path": target_path, "requests": [reqkey],
                                                     "methods": {method: 1}, "is_soapaction": is_soapaction,
                                                     "last_completed_trial": -1, "last_completed_refuzz": -1})
                else:
                    print(f"Skipping {req['_url']} b/c no query or post data.")

            self.save_campaign_status()

    def save_campaign_status(self):

        json.dump(self.fuzz_campaign_status, open(self.fuzz_campaign_status_fn, "w"))

    def create_seeds(self, requests):
        seed_name_stub = os.path.join(self.seed_path,"seed-")
        seeds = []
        if len(requests) > 50:
            requests = requests[:10]
        for reqkey in requests:

            req = self.request_data["requestsFound"].get(reqkey,None)
            if req is None:
                print(f"[Witcher]\033[32m Did not find {reqkey} in request data. \033[0m")
                continue
                #req = self.request_data["requestsFound"].get(reqkey, None)

            strid = req["_id"]
            url = urlparse(req["_url"])

            cookie_data = req.get('_cookieData','').encode("utf-8")
            urlquery = url.query.encode("utf-8")
            post_data = req.get('_postData','').encode("utf-8")

            headers = req.get('_headers','')
            headers_out = ""
            for k,v in headers.items():
                if k.upper() == "SOAPACTION" or k.upper() == "HNAP_AUTH":
                    headers_out += f"{k}:{v}\n"

            strout = b"%s\x00%s\x00%s\x00%s" % (cookie_data, urlquery, post_data, headers_out.encode('utf-8'))
            if len(strout) > 3:
                seeds.append(strout)
        if len(seeds) == 0:
            seeds.append(b"cookie=flour\x00query=search\x00post=hole")
        return seeds

    def create_dictionary(self, target):
        dictionary_vars = []
        inputlist = self.request_data["inputSet"]
        if f"inputSet-{target}" in self.request_data:
            inputlist = inputlist + self.request_data[f"inputSet-{target}"]
        for inputvar in inputlist:
            if len(inputvar) > 127:
                continue
            if inputvar.find("&") == len(inputvar) -1:
                inputvar = inputvar[:-1]
            dictionary_vars.append(b"%s&" % inputvar.encode("utf-8"))
#            dictionary_vars.append(b"%s'(&" % inputvar.encode("utf-8"))
        print(f"Wrote out dictionary vars {len(inputlist)} totals bytes {len(dictionary_vars)} {dictionary_vars[0]}")

        #open(self.dictionary_fn,"w").write(dictionary_vars)
        return dictionary_vars

    def init_shared_memory(self):
        if self.init_info_shm:
            subprocess.check_call(self.init_info_shm.split(" "))
            print(f"Initalized Shared Memory using '{self.init_info_shm}'")

    def start_external_servers(self):
        print(f"cmd={self.server_cmd}")

        if self.server_cmd is not None and len(self.server_cmd) > 1:
            print("Starting up servers")
            increasing_port = self.server_base_port

            for icnt in range(0, self.cores):
                server_cmd = []
                for cmd in self.server_cmd:
                    cmd = cmd.replace("@@PORT@@", str(self.server_base_port))
                    cmd = cmd.replace("@@PORT_INCREMENT@@", str(increasing_port))

                    server_cmd.append(cmd)

                server_env_vars = os.environ.copy()

                for envkey, envval in self.server_env_vars.items():
                    if "@@PORT_INCREMENT@@" in envval:
                        envval = envval.replace("@@PORT_INCREMENT@@", str(increasing_port))
                    server_env_vars[envkey] = envval
                print(f"CMD = {' '.join(server_cmd)}")
                #print(f"SERVER_ENV_VARS={server_env_vars}")
                logfpath = f"/tmp/server_{increasing_port}.out"
                outfile = open(logfpath,"w")

                proc_info = {"server_cmd":server_cmd, "logfile": logfpath, "port":increasing_port, "attempts":0,
                             "up":False, "env": server_env_vars}

                proc_info["proc"] = subprocess.Popen(server_cmd, env=server_env_vars, stdout=outfile,
                                                     stderr=outfile, close_fds=True)
                #print(f"Starting up {proc_info}")
                self.server_procs.append(proc_info)
                increasing_port = increasing_port + 1

            wait_cnt = 0
            all_servers_up = False
            time.sleep(2)
            while not all_servers_up:
                all_servers_up = True
                for si in self.server_procs:
                    if si["attempts"] > 3:
                        print("Error trying to bring up servers, exiting...")
                        exit(99)
                    p = si["proc"]
                    if si["up"]:
                        continue
                    if p.poll() is None:  # process is still running
                        if os.path.exists(si["logfile"]):
                            with open(si["logfile"], "r") as lf:
                                data = lf.read()
                                if data.find(self.server_up_msg) > -1:
                                    si["up"] = True
                    else: # process is stopped
                        if not si["up"]:
                            print(f"DOING: pkill -P {p.pid}")
                            os.system(f"pkill -P {p.pid}")
                            print(f"DOING: pkill -9 -f {si['port']}")
                            os.system(f"pkill -9 -f {si['port']}")
                            print("attempting to bring up again.")
                            outfile = open(si["logfile"], "a")
                            si["proc"] = subprocess.Popen(si["server_cmd"], env=si["env"], stdout=outfile, stderr=outfile, close_fds=True)
                        else:
                            assert(not si["up"])

                    all_servers_up = all_servers_up and si["up"]

                if wait_cnt > 120:
                    print("Error, waited for too long, exiting")
                    exit(98)
                if not all_servers_up:
                    print("All the servers are not up, sleeping and will try again")
                    time.sleep(2)
                wait_cnt += 1

            if len(self.server_up_msg) == 0:
                print("Giving servers a chance to come up")
                time.sleep(10)

            print("Servers, should be up")

    def kill_servers(self):
        print("Bringing down external servers")

        for si in self.server_procs:
            p = si["proc"]
            if p:
                print(f"\tDOING: pkill -P {p.pid}")
                os.system(f"pkill -P {p.pid}")
            if p and p.poll() is None:
                try:
                    p.kill()
                except Exception as ex:
                    print(f"ERROR with bringing down {ex}")
            print(f"\tDOING: pkill -9 -f {si['port']}")
            os.system(f"pkill -f {si['port']}")
            os.system(f"pkill -9 -f 'port={si['port']}'") # just to be sure!

        self.server_procs = []



    def start_fuzzer(self, do_resume, target_path, method_map, dictionary_str, seeds):

        os.environ["method_map"] = method_map
        os.environ["SCRIPT_FILENAME"] = target_path

        with open("/tmp/start_test.dat","w") as wf:
            wf.write("Trace me if you can, little one.")

        if target_path.startswith("http"):
            binary_options = self.change_url_to_target(target_path)
            print(f"NEW BIN OPTS {binary_options}")
        else:
            binary_options = self.binary_options

        fuzzer = Phuzzer.phactory(phuzzer_type=Phuzzer.WITCHER_AFL, target=self.fuzzer_target_binary, target_opts=binary_options,
                                  work_dir=self.work_dir, seeds=seeds, afl_count=self.cores,
                                  create_dictionary=False, timeout=self.timeout, memory=self.memory,
                                  run_timeout=self.run_timeout, dictionary=dictionary_str,
                                  use_qemu=self.use_qemu, resume=do_resume, login_json_fn=self.config_loc,
                                  base_port=self.server_base_port, container_info=self.container_info, fault_escalation=not self.no_fault_escalation)

        def chown_files():
            # by default, AFL creates all files and dirs with permissions of 700
            # as a result, unless running witcher as root, it cannot access the files unless they are
            # owned by the current user, which is what this is meant to do. It runs in reporter,
            if self.container_info:

                fuzzer.chown_container_files(pwd.getpwuid( os.getuid() ).pw_uid)

        start_results = {"totalfail": False, "timeout": False }
        reporter = Reporter(self.fuzzer_target_binary, self.report_dir, self.cores, self.first_crash, self.timeout,
                            fuzzer.work_dir, chown_files=chown_files)

        reporter.set_script_filename(target_path)

        fuzzer.start()

        reporter.start()
        print("Starting Reporter...")
        # Monitor phuzzer's execution
        try:
            crash_seen = False
            reporter.enable_printing()
            verified_start = False
            run_time = 0

            while True:
                if not verified_start:
                    chown_files()
                    start_results = fuzzer.startup_status()
                    totalcnt = start_results["totalcnt"]
                    successcnt = start_results["successcnt"]
                    forkfailcnt = start_results["forkfail"]
                    failedseeds = start_results['failedseeds']
                    weakseeds = start_results['weakseeds']
                    logfilesize = start_results['logfilesize']
                    reporter.set_startup_values(successcnt, len(failedseeds), len(weakseeds), logfilesize)
                    if forkfailcnt >= 1:
                        print(f"[*]\033[31mError at least 1 instance failed to communicate with fork server \033[0m")
                        import ipdb
                        ipdb.set_trace()
                        raise Exception("Fork server handshake failure count too high")

                    if successcnt + len(start_results['failedseeds']) == self.cores or (run_time > 120 and logfilesize > 0) or run_time > 300:
                        verified_start = True
                        success_percent = (float(successcnt) / float(totalcnt)) * 100 if totalcnt > 0 else 0
                        if success_percent < 80:
                            print(f"[*] Error less than 80% ({successcnt}/{totalcnt} = {success_percent:3.2f})of the fuzzers started up successfully please investigate")
                            start_results["totalfail"] = True

                            break
                        else:
                            start_results["totalfail"] = False
                    else:
                        start_results["totalfail"] = False

                if not crash_seen and fuzzer.found_crash():
                    chown_files()
                    # print ("\n[*] Crash found!")
                    crash_seen = True
                    reporter.set_crash_seen()
                    if self.first_crash:
                        break
                if fuzzer.timed_out():
                    reporter.set_timeout_seen()
                    start_results["timeout"] = True
                    print("\n[*] Timeout reached.")
                    break
                run_time += 1
                time.sleep(1)

        except KeyboardInterrupt:
            end_reason = "Keyboard Interrupt"
            print("\n[*] Aborting wait. Ctrl-C again for KeyboardInterrupt.")
            self.kill = True

        except Exception as e:
            import traceback
            traceback.print_exc()
            end_reason = "Exception occurred"
            print("\n[*] Unknown exception received (%s). Terminating fuzzer." % e)
            self.kill = True
            raise
        finally:
            print("[*] Terminating fuzzer.")
            chown_files()
            reporter.stop()
            fuzzer.stop()
            os.system("rm -f /tmp/start_test.dat")
            if self.kill:
                exit(199)
        return start_results


    def results_target_dir(self, trial_index, target_path):
        encoded_path = target_path.replace(self.appdir + '/', '').replace('/', '+')
        targets_dir = f"tr{trial_index}_{encoded_path}"
        results_dir = os.path.join(self.report_dir, targets_dir)
        return results_dir

    def fix_perms_in_dir(self, tdir):
        if not os.path.exists(tdir):
            print(f"Target dir {tdir} does not exist.")
            return

        # this is only a problem for qemu-user targets running in a docker container
        if self.container_info:
            perm_id = pwd.getpwuid( os.getuid() ).pw_uid

            perm_cmd = f"cd {tdir}/.. && /bin/chown {perm_id}:{perm_id} -R . && find . -type d -exec chmod +rx {{}} \; " \
                       f"&& find . -type f -exec chmod +r {{}} \;"

            volume = f"{tdir}:{tdir}"
            perm_cmd = ["docker", "run", "--rm", "-v", volume, "ubuntu:20.04", "/bin/bash", "-c", perm_cmd]

            subprocess.check_output(perm_cmd)


    # it uses this method b/c with qemu-user running as root, AFL creates unreadble file permissions
    def docker_copy(self, from_dir, to_dir):
        if not os.path.exists(from_dir):
            print(f"From dir {from_dir} does not exist, cannot copy.")
            return

        os.makedirs(to_dir, exist_ok=True)

        from_volume = f"{from_dir}:/from"
        to_volume = f"{to_dir}:/to"
        cp_cmd = ["docker", "run", "--rm", "-v", from_volume,"-v", to_volume, "ubuntu:20.04",
                  "/bin/cp", "-a", "/from/.", "/to"]

        subprocess.check_output(cp_cmd)

        # just in case a rouge file gets created between last permission set and the copy, make sure all the files in
        # in the to directory have acceptable permissions
        self.fix_perms_in_dir(to_dir)

    def copy_fuzzer_output_to_results(self, trial_index, target_path):
        if self.container_info:
            self.fix_perms_in_dir(self.work_dir)

        dst = self.results_target_dir(trial_index, target_path)

        print(f"Copy from {self.work_dir} to dst={dst}")

        if os.path.exists(dst):
            shutil.rmtree(dst)

        if self.container_info:
            self.docker_copy(self.work_dir, dst)
        else:
            try:
                shutil.copytree(self.work_dir, dst)
            except:
                time.sleep(10)
                try:
                    shutil.copytree(self.work_dir, dst)
                except:
                    print("\033[31mError couldn't copy results \033[0m\n")

    def copy_fuzzer_results_to_output(self, trial_index, target_path):

        src = self.results_target_dir(trial_index, target_path)
        print(f"Copy from src-{src} to {Witcher.WORKING_DIR}")
        if os.path.exists(Witcher.WORKING_DIR):
            shutil.rmtree(Witcher.WORKING_DIR)
        shutil.copytree(src, Witcher.WORKING_DIR)

    def build_methd_map(self, methods):
        tot = sum(methods.values())
        outlist = []

        for k, v in sorted(methods.items(), key=lambda item:item[1]):
            cnt = max(int(round(v / tot * 16)), 1)
            for _ in range(0, cnt):
                outlist.append(k)

        if len(outlist) < 16:
            outlist = outlist[:16 - len(outlist)]

        outlist = outlist[:-1] if len(outlist) > 16 else outlist

        return ",".join(outlist)

    def target_contains_skiplist_value(self, target_path):
        for skipper in self.jconfig["script_skip_list"]:
            if target_path.find(skipper) > -1:
                return True
        return False

    def change_url_to_target(self, target):
        url = urlparse(target)
        netloc = url.netloc

        if ":" in netloc:
            netloc = netloc[0: netloc.find(":")]
        netloc = f"{netloc}:@@PORT_INCREMENT@@"
        url = url._replace(netloc=netloc)
        strurl=urlunparse(url)
        out_opts = []

        for cmdopt in self.binary_options:
            out_opts.append(cmdopt.replace("@@url@@", strurl))
        return out_opts


    def create_war_filter(self):
        if self.war_path:
            filelist = subprocess.check_output(["jar","-tf",self.war_path])
            filelist = filelist.decode().split("\n")
            print(filelist)
            with open("/dev/shm/javafilters.dat", "w") as jfilters:
                for f in filelist:
                    if f.endswith(".class"):
                        classfn = f.replace("WEB-INF/", "").replace("classes/","").replace("/",".").replace(".class","")
                        jfilters.write(classfn + "\n")
                        print(classfn)
                    if f.endswith(".jsp"):
                        jspclassfn = f.replace(".jsp","_jsp").replace("/",".")
                        jspclassfn = f"org.apache.jsp.{jspclassfn}"
                        jfilters.write(jspclassfn + "\n")
                        print(jspclassfn)
        # for dirpath in glob.iglob(os.path.join(TOMCAT_PATH, "webapps")):
        #     with open("/dev/shm/javafilters.dat", "w") as jfilters:
        #         if os.path.isdir(dirpath):
        #             appdirname = os.path.basename(dirpath)
        #             classpath = os.path.join(dirpath, "WEB-INF", "classes")
        #             for webfile in glob.iglob(classpath + "/*.class", recursive=True):
        #                 if os.path.isfile(webfile):
        #                     class_fn = webfile.replace(classpath, "")
        #                     class_fn = class_fn.replace(".class","")
        #                     class_fn = class_fn.replace("/",".")
        #                     jfilters.write(class_fn + "\n")
        #                     print(class_fn)
        #             workpath = os.path.join(TOMCAT_PATH, "work","Catalina","localhost", appdirname)
        #             for webfile in glob.iglob(workpath + "/*.class", recursive=True):
        #                 if os.path.isfile(webfile):
        #                     class_fn = webfile.replace(classpath, "")
        #                     class_fn = class_fn.replace(".class", "")
        #                     class_fn = class_fn.replace("/", ".")
        #                     print(class_fn)
        #                     jfilters.write(class_fn + "\n")

    def save_crashing_seed(self, seedpath: str, url_path: str) -> None:
        """
        Saves a seed that AFL reported as crashing

        """
        if seedpath+url_path in self.saved_seeds:
            print(f"{WITCH_GO} Not saving for {url_path} {seedpath}")
            return

        encoded_url_path = url_path.replace(self.appdir + '/', '').replace('/', '+')

        crash_file_dpath = os.path.join(self.report_dir, 'seed-crashes')
        os.makedirs(crash_file_dpath, exist_ok=True)

        fid = len(glob.glob(f"{crash_file_dpath}id*"))

        crash_fname = os.path.join(crash_file_dpath, f"id:{fid:06},{encoded_url_path},src:{os.path.basename(seedpath)},crash")
        crash_fname = os.path.realpath(crash_fname)
        print(f"[Witcher] Saved potential crashing input seed at {os.path.basename(crash_fname)}")
        shutil.copyfile(seedpath, crash_fname)

        fuzz_scr_fpath = os.path.join(self.work_dir, "fuzz-0.sh")
        with open(fuzz_scr_fpath, "r") as rf:
            scr = rf.read()

        cat_str = f'cat "$SCRIPT_DIR/{os.path.basename(crash_fname)}"'

        out_scr = ""
        for line in scr.split("\n"):
            if line.find("afl-fuzz") > -1:
                out_scr += """SCRIPT_DIR="$(cd "$(dirname $0)" > /dev/null && pwd)" \n"""
                args = line.split(" ")

                out_args = [f"{os.path.dirname(args[0])}/afl-showmap", "-o", f"/tmp/map-{os.path.basename(seedpath)}"]
                argindex = 1
                while argindex < len(args):
                    arg = args[argindex]
                    if arg == "-i" or arg == "-o" or arg == "-x" or arg == "-M":
                        argindex += 2
                    else:
                        out_args.append(arg)
                        argindex += 1
                out_scr += cat_str + " | " + " ".join(out_args) + "\n"

            else:
                out_scr += line + "\n"

        exec_fpath = f"{crash_fname}.sh"
        with open(exec_fpath, "w") as wf:
            wf.write(out_scr)

        os.chmod(exec_fpath, stat.S_IRWXU | stat.S_IRWXG | stat.S_IWOTH | stat.S_IROTH)


    def start_fuzz_campaign(self):
        _environ_backup = os.environ.copy()
        try:
            os.environ.clear()
            os.environ.update(self.env)

            nbr_trials = int(self.jconfig.get("number_of_trials", "1"))
            nbr_refuzzes = int(self.jconfig.get("number_of_refuzzes", "1"))

            for trial_index in range(0, nbr_trials):
                self.init_shared_memory()

                print(f"TRIAL INDEX = {trial_index}")
                self.init_fuzz_campaign_status(trial_index)
                trial = self.fuzz_campaign_status[trial_index]
                targets = trial["targets"].copy()
                print(f"Trial start = {trial['trial_start']}")

                if self.jconfig["script_random_order"] == 1:
                    random.shuffle(targets)

                self.start_external_servers()

                for refuzz_index in range(0, nbr_refuzzes):
                    if self.jconfig["script_random_order"] == 2:
                        random.shuffle(targets)
                    target_start = self.jconfig.get("script_start_index", 0)
                    target_end = self.jconfig.get("script_end_index", len(targets))

                    for target in targets[target_start: target_end]:

                        if self.single_target and target['target_path'].find(self.single_target) == -1: # if using single target and not in target name then skip
                            continue
                        if self.target_contains_skiplist_value(target['target_path']):
                            print("SKIPPING B/C in SKIPLIST")
                            continue
                        if trial_index < target["last_completed_trial"] or (trial_index == target["last_completed_trial"] and refuzz_index <= target["last_completed_refuzz"] ):
                            print(f"Skipping {target['target_path']} Trial={trial_index}, Refuzz={refuzz_index} last_completed_refuzz={target['last_completed_refuzz']}")
                            continue

                        regex = re.compile(r"(?P<prefix>http://)([0-9\.]+)(?P<postfix>.*)")

                        target_url = target['target_path']
                        result_storage_pathname = target_url

                        do_resume = refuzz_index > 0

                        # if soapaction, then go to url of first request if exists else default

                        if target['is_soapaction']:
                            if len(target['requests']) > 0 :
                                req0 = target['requests'][0]

                                trequest = self.request_data['requestsFound'][req0]
                                target_url = trequest["_url"]
                                soap_urlstr = None
                                if "soapaction" in trequest["_headers"]:
                                    soap_urlstr = trequest["_headers"]["soapaction"]
                                elif "SOAPACTION" in trequest["_headers"]:
                                    soap_urlstr = trequest["_headers"]["SOAPACTION"]

                                if soap_urlstr:
                                    soap_urlstr = soap_urlstr.replace('"', "")
                                    result_storage_pathname = urlparse(soap_urlstr).path
                            else:
                                target_url = "http://127.0.0.1/HNAP1"

                        urlmatch = regex.match(target_url)
                        if urlmatch:
                            if self.container_info:
                                target_url = regex.sub(r'\g<prefix>127.0.0.1\g<postfix>', target_url)
                            if not target["is_soapaction"]:
                                result_storage_pathname = urlparse(target_url).path

                        print(f"FUZZING \033[33m{target['target_path']}\033[0m Trial={trial_index}, Refuzz={refuzz_index} last_completed_refuzz={target['last_completed_refuzz']} result_path={result_storage_pathname}")

                        if do_resume:
                            self.copy_fuzzer_results_to_output(trial_index, result_storage_pathname)

                        seeds = self.create_seeds(target["requests"])
                        dictionary_str = self.create_dictionary(target)

                        method_map = self.build_methd_map(target["methods"])
                        start_results = self.start_fuzzer(do_resume, target_url, method_map, dictionary_str, seeds)
                        #return {"successcnt":success, "totalcnt":totallogs, "testfailed":testfailed, "failedseeds": failedseeds}
                        # if startup fails (in other words there's more fuzzers that failed to come up than successful ones.

                        while len(seeds) > 0 and (start_results.get("totalfail", True)):
                            failed_seeds = start_results.get("failedseeds", [])
                            weak_seeds = start_results.get("weakseeds", [])
                            print(f"Startup info {start_results} {weak_seeds} {failed_seeds}")
                            if failed_seeds or weak_seeds:
                                print(f"{WITCH_FAIL} {len(failed_seeds)} seeds caused a failure and {len(weak_seeds)} resulted in known execution path ")
                                seeds_to_scan = set()
                                seeds_to_scan |= failed_seeds
                                seeds_to_scan |= weak_seeds
                                for fn in seeds_to_scan:
                                    seedpath = f"{self.work_dir}/initial_seeds/{fn}"

                                    if os.path.exists(seedpath):

                                        self.save_crashing_seed(seedpath, result_storage_pathname)
                                        self.saved_seeds.add(seedpath+result_storage_pathname)

                                        with open(seedpath,"rb") as rf:
                                            filedata = rf.read()
                                        rep_regex = rb"[\x01-\x19'\x7f-\xff]"

                                        if re.match(rep_regex, filedata):
                                            print(f"[Witcher] seed has odd characters, replacing with all with 'a'")
                                            filedata = re.sub(rep_regex, repl=b"a", string=filedata)
                                            with open(seedpath, "wb") as wf:
                                                wf.write(filedata)
                                        else:
                                            print(f"[Witcher] No odd characters, deleting seed")
                                            os.remove(seedpath)
                                seeds = []
                                for fn in glob.iglob(f"{self.work_dir}/initial_seeds/*"):
                                    with open(fn,"rb") as rf:
                                        seeds.append(rf.read())
                            else:
                                print("\033[36mCould not find any failed or weak seeds, so removing last seed")
                                seeds.remove(seeds[len(seeds)-1])

                            print(f"\033[33mAttempting to fuzz again {target['target_path']}\033[0m with {len(seeds)} seeds and {start_results}")
                            start_results = self.start_fuzzer(do_resume, target_url, method_map, dictionary_str, seeds)

                        if start_results.get("totalfail", True):
                            print(f"EXITING while but total fail still True with {start_results}")

                        if start_results.get("timeout", False):
                            target["last_completed_trial"] = trial_index
                            target["last_completed_refuzz"] = refuzz_index
                        else:
                            print(f"\033[31mFailed to FUZZ {target['target_path']}\033[0m")

                        #os.system(f"sudo chown etrickel:etrickel {self.work_dir}/. -R")

                        self.copy_fuzzer_output_to_results(trial_index, result_storage_pathname)
                        self.save_campaign_status()
                        sys.stdout.flush()
                        time.sleep(1)
                        self.kill_servers()
                        print("Sleeping a few and then will start up external servers ")
                        time.sleep(10)

                        self.fix_perms_in_dir(self.work_dir) # extra precaution for perms, I'm tired of these exceptions coming at the end of the loop!

                        self.start_external_servers()
                self.kill_servers()

        except Exception as exp:
            import traceback
            traceback.print_exc()

        finally:
            self.kill_servers()
            os.environ.clear()
            os.environ.update(_environ_backup)
            # kill supervisor to shutdown container, if its parent is supervisord (pid == 1)
            if os.getppid() == 1:
                try:
                    os.kill(1, signal.SIGQUIT)
                except Exception as e:
                    print('Could not kill supervisor: ' + e + '\n')










