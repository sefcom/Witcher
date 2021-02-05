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
import sys
import os

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
        self.request_data = json.load(open(self.request_data_fn,"r"))

        self.cores = int(self.jconfig.get("cores", args.cores))
        self.timeout = self.jconfig.get("timeout", args.timeout)
        self.memory = self.jconfig.get("memory", args.memory)
        self.first_crash = self.jconfig.get("first_crash", args.first_crash)
        self.run_timeout = int(self.jconfig.get("run_timeout", 200))
        self.use_qemu = self.jconfig.get("use_qemu")
        self.server_cmd = self.jconfig.get("server_cmd", None)
        self.init_info_shm = self.jconfig.get("init_info_shm", None)
        self.war_path = self.jconfig.get("war_path",None)
        self.server_base_port = 14000

        self.server_env_vars = self.jconfig.get("server_env_vars", {})
        print(self.server_env_vars)
        self.binary_options = self.jconfig.get("binary_options").split(" ")
        self.server_procs = []
        self.kill = False

        self.create_war_filter()

    def save_filesdata(self):
        json.dump(self.fuzz_campaign_status,open(self.fuzz_campaign_status_fn,"w"))

    def initialize_env(self):
        env = os.environ.copy()
        env["LD_LIBRARY_PATH"] = self.jconfig["ld_library_path"] if "ld_library_path" in self.jconfig else ""
        env["AFL_PRELOAD"] = self.jconfig["afl_preload"] if "afl_preload" in self.jconfig else ""
        env["DOCUMENT_ROOT"] = self.appdir
        if self.affinity is not None:
            env["AFL_SET_AFFINITY"] = self.affinity
        if "mandatory_cookie" in self.jconfig:
            env["MANDATORY_COOKIE"] = self.jconfig["mandatory_COOKIE"]
        if "mandatory_get" in self.jconfig:
            env["MANDATORY_GET"] = self.jconfig["mandatory_get"]
        if "mandatory_post" in self.jconfig:
            env["MANDATORY_POST"] = self.jconfig["mandatory_post"]

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
    def find_path(urlpath):
        fname = os.path.basename(urlpath)

        cmd = ["find", "/", "-path", "/p", "-prune", "-o", "-path", "/proc", "-prune",
               "-o", "-path", "/test", "-prune", "-o", "-path", "/etc", "-prune",
               "-o", "-path", "/var/log", "-prune", "-o", "-path", "/var/spool", "-prune",
               "-o", "-path", "/var/cache", "-prune",
               "-o", "-path", "/var/lib", "-prune", "-o", "-path", "/root", "-prune",
               "-o", "-name", fname]

        print(f"Command = {' '.join(cmd)}")

        results = subprocess.check_output(cmd)
        print(f"RESULTS from find = {results}")
        for fpath in sorted(results.split(b'\n'), key=len):
            fpath = fpath.decode("latin-1")
            if fpath.find(urlpath) > -1:
                return fpath
        raise Exception("Target path not found ")

    def init_fuzz_campaign_status(self, trial_index):
        if self.fuzz_campaign_status is None:
            self.fuzz_campaign_status = []

        assert (trial_index <= len(self.fuzz_campaign_status))

        if len(self.fuzz_campaign_status) == trial_index:

            targets_added = {}
            start_time = datetime.now().strftime("%Y_%m_%d_%H_%M")
            self.fuzz_campaign_status.append({"trial_start": start_time, "trial_complete": False, "targets": []})
            trial = self.fuzz_campaign_status[trial_index]
            for reqkey, req in self.request_data["requestsFound"].items():
                url = urlparse(req["_url"])
                if self.server_cmd:
                    url = url._replace(query="")
                    target_path = urlunparse(url)
                else:
                    url = urlparse(req["_url"])
                    urlpath = url.path
                    if urlpath.startswith("/"):
                        urlpath = urlpath[1:]
                        
                    target_path = os.path.join(self.appdir, urlpath)

                    if not os.path.exists(target_path):
                        target_path = Witcher.find_path(urlpath)
                print(f" THE PATH to use is :::::: > {target_path}")
                method = req.get("_method", "GET").upper()

                if not self.single_target or target_path.find(self.single_target) > -1:
                    if target_path in targets_added:
                        index = targets_added[target_path]
                        trial["targets"][index]["requests"].append(reqkey)
                        if method in trial["targets"][index]["methods"]:
                            trial["targets"][index]["methods"][method] += 1
                    else:
                        targets_added[target_path] = len(trial["targets"])
                        trial["targets"].append({"target_path": target_path, "requests": [reqkey],
                                                 "methods": {method: 1},
                                                 "last_completed_trial": -1, "last_completed_refuzz": -1})
            self.save_campaign_status()

    def save_campaign_status(self):
        json.dump(self.fuzz_campaign_status, open(self.fuzz_campaign_status_fn, "w"))

    def create_seeds(self, requests):
        seed_name_stub = os.path.join(self.seed_path,"seed-")
        seeds = []
        for reqkey in requests:
            req = self.request_data["requestsFound"][reqkey]
            strid = req["_id"]
            url = urlparse(req["_url"])
            print(f"url={url}")
            cookie_data = req.get('_cookieData','').encode("utf-8")
            urlquery = url.query.encode("utf-8")
            post_data = req.get('_postData','').encode("utf-8")

            strout = b"%s\x00%s\x00%s" % (cookie_data, urlquery, post_data)
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
            dictionary_vars.append(b"%s'(&" % inputvar.encode("utf-8"))
        print(f"Wrote out dictionary vars {len(inputlist)} totals bytes {len(dictionary_vars)} {dictionary_vars[0]}")

        #open(self.dictionary_fn,"w").write(dictionary_vars)
        return dictionary_vars

    def init_shared_memory(self):
        if self.init_info_shm:
            subprocess.check_call(self.init_info_shm.split(" "))
            print(f"Initalized Shared Memory using '{self.init_info_shm}'")

    def start_external_servers(self):
        if self.server_cmd is not None:
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
                outfile = open(f"/tmp/server_{increasing_port}.out","w")
                self.server_procs.append(subprocess.Popen(server_cmd, env=server_env_vars, stdout=outfile,
                                                          stderr=outfile, close_fds=True))
                increasing_port = increasing_port + 1

            print("Giving servers a chance to come up")
            time.sleep(10)
            print("Servers, should be up")

    def kill_servers(self):

        for server in self.server_procs:
            server.kill()

    def start_fuzzer(self, do_resume, target_path, method_map, dictionary_str, seeds):

        os.environ["method_map"] = method_map
        os.environ["SCRIPT_FILENAME"] = target_path
        if target_path.startswith("http"):
            binary_options = self.change_url_to_target(target_path)
            print(f"NEW BIN OPTS {binary_options}")
        else:
            binary_options = self.binary_options

        fuzzer = Phuzzer.phactory(phuzzer_type=Phuzzer.WITCHER_AFL, target=self.fuzzer_target_binary, target_opts=binary_options,
                                  work_dir=Witcher.WORKING_DIR, seeds=seeds, afl_count=self.cores,
                                  create_dictionary=False, timeout=self.timeout, memory=self.memory,
                                  run_timeout=self.run_timeout, dictionary=dictionary_str,
                                  use_qemu=self.use_qemu, resume=do_resume, login_json_fn=self.config_loc)

        reporter = Reporter(self.fuzzer_target_binary, self.report_dir, self.cores, self.first_crash, self.timeout,
                            fuzzer.work_dir)

        reporter.set_script_filename(target_path)

        fuzzer.start()
        reporter.start()
        print("Starting Reporter...")
        # Monitor phuzzer's execution
        try:
            crash_seen = False
            reporter.enable_printing()
            while True:
                
                if not crash_seen and fuzzer.found_crash():
                    # print ("\n[*] Crash found!")
                    crash_seen = True
                    reporter.set_crash_seen()
                    if self.first_crash:
                        break
                if fuzzer.timed_out():
                    reporter.set_timeout_seen()
                    print("\n[*] Timeout reached.")
                    break

                time.sleep(1)

        except KeyboardInterrupt:
            end_reason = "Keyboard Interrupt"
            print("\n[*] Aborting wait. Ctrl-C again for KeyboardInterrupt.")
            self.kill = True

        except Exception as e:
            end_reason = "Exception occurred"
            print("\n[*] Unknown exception received (%s). Terminating fuzzer." % e)
            self.kill = True
            raise
        finally:
            print("[*] Terminating fuzzer.")
            self.kill_servers()
            reporter.stop()
            fuzzer.stop()

            if self.kill:
                exit(199)

    def results_target_dir(self, trial_index, target_path):
        encoded_path = target_path.replace(self.appdir + '/', '').replace('/', '+')
        targets_dir = f"tr{trial_index}_{encoded_path}"
        results_dir = os.path.join(self.report_dir, targets_dir)
        return results_dir

    def copy_fuzzer_output_to_results(self, trial_index, target_path):

        dst = self.results_target_dir(trial_index, target_path)

        print(f"Copy from {Witcher.WORKING_DIR} to dst={dst}")
        if os.path.exists(dst):
            shutil.rmtree(dst)
        shutil.copytree(Witcher.WORKING_DIR, dst)

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




    def start_fuzz_campaign(self):
        _environ_backup = os.environ.copy()
        try:
            os.environ.clear()
            os.environ.update(self.env)

            nbr_trials = int(self.jconfig.get("number_of_trials", "1"))
            nbr_refuzzes = int(self.jconfig.get("number_of_refuzzes", "1"))

            for trial_index in range(0, nbr_trials):
                self.init_shared_memory()
                self.start_external_servers()

                print(f"TRIAL INDEX = {trial_index}")
                self.init_fuzz_campaign_status(trial_index)
                trial = self.fuzz_campaign_status[trial_index]
                targets = trial["targets"].copy()
                print(f"Trial_start = {trial['trial_start']}")

                if self.jconfig["script_random_order"] == 1:
                    random.shuffle(targets)

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

                        do_resume = refuzz_index > 0

                        if do_resume:
                            self.copy_fuzzer_results_to_output(trial_index, target['target_path'])

                        print(f"FUZZING {target['target_path']} Trial={trial_index}, Refuzz={refuzz_index} last_completed_refuzz={target['last_completed_refuzz']}")
                        seeds = self.create_seeds(target["requests"])
                        dictionary_str = self.create_dictionary(target)

                        method_map = self.build_methd_map(target["methods"])
                        self.start_fuzzer(do_resume, target["target_path"], method_map, dictionary_str, seeds)
                        target["last_completed_trial"] = trial_index
                        target["last_completed_refuzz"] = refuzz_index

                        self.copy_fuzzer_output_to_results(trial_index, target['target_path'])
                        self.save_campaign_status()
                        sys.stdout.flush()
                        time.sleep(1)
                self.kill_servers()

        except Exception as exp:
            import traceback
            traceback.print_exc()
            self.kill_servers()
        finally:
            os.environ.clear()
            os.environ.update(_environ_backup)
            # kill supervisor to shutdown container, if its parent is supervisord (pid == 1)
            if os.getppid() == 1:
                try:
                    os.kill(1, signal.SIGQUIT)
                except Exception as e:
                    print('Could not kill supervisor: ' + e + '\n')










