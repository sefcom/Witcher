import unittest
import subprocess
import signal
import glob
import sys
import os

DEFAULT_CMD = ["python", "-m", "witcher", "/test", "EXWICHR"]
DEFUALT_CMD_CRASH = DEFAULT_CMD + ["-C"]

class WitcherTest(unittest.TestCase):
    cwd = os.path.dirname(os.path.realpath(__file__))
    run_dir = os.path.join(cwd, "..")
    procs = {}

    def setUp(self) -> None:
        with open('/tmp/unitest_out_popen.log', "a+") as outfile:
            subprocess.check_call([os.path.join(WitcherTest.cwd, "test_data/setup/dosetup.sh")], stdout=outfile)

    def tearDown(self) -> None:
        tbd = []
        for pname, p in WitcherTest.procs.items():
            try:
                if p is not None:
                    if p.poll == None: # p.subprocess is alive
                        p.send_signal(signal.SIGINT)
                        p.wait()
                        p.terminate()
                tbd.append(pname)
            except Exception:
                pass
        for pname in tbd:
            del WitcherTest.procs[pname]

    def start_fuzzer(self, name, cmd=None, timeout=120, outfile_fn='/tmp/unitest_out_popen.log'):
        cmd = DEFAULT_CMD if cmd is None else cmd

        with open(outfile_fn, "a+") as outfile:
            p = subprocess.Popen(cmd, cwd=self.run_dir, stdout=outfile)

        self.procs[name] = p
        try:
            p.wait(timeout=timeout)

        except Exception:
            pass

        p.send_signal(signal.SIGINT)
        p.wait()

    def test_fuzz_startup(self):
        tname = "test_fuzz_startup"
        print(f"[+] {tname} started")
        out_fn = '/tmp/fuzzers-startup.log'
        with open(out_fn,"w") as fp:
            fp.write("")
        self.start_fuzzer(tname, DEFUALT_CMD_CRASH, timeout=5, outfile_fn=out_fn )

        with open(out_fn, "rb") as fp:
            results = fp.read()

        self.assertTrue(results.find(b"fuzzers running until first crash or timeout") > -1, f"Fuzzers are not running results = \n{results}\n\n")
        self.assertTrue(results.find(b"3 fuzzers running") > -1, "Running different than 3 fuzzers")

        for f in glob.glob("/tmp/output/fuzzer-*.log"):
            with open(f, "rb") as fp:
                logdata = fp.read()
            self.assertTrue(logdata.find(b"All set and ready to roll") > -1, f"Errror no all set in {f}")

        print(f"[+] {tname} completed successfully")


    def test_afuzz_znext(self):
        """
        Tests roll to next script, so default 30 sec timeout, wait 45 secs, and see if we have the results of last one
        and new one in the /tmp/output
        :return:
        """
        tname = "test_fuzz_znext"
        print(f"[+] {tname} started")

        self.start_fuzzer(tname, DEFUALT_CMD_CRASH, timeout=45)

        #results = open('/tmp/unitest_out_popen.log', "rb").read()

        for f in glob.glob("/results/unittests-EXWICHR/tr?_test+test-findvar-1.php/fuzzer-*.log"):
            with open(f, "rb") as fp:
                logdata = fp.read()
            self.assertTrue(logdata.find(b"All set and ready to roll") > -1, f"Errror session completed and copied {f}")

        for f in glob.glob("/tmp/output/fuzzer-*.log"):
            with open(f, "rb") as fp:
                logdata = fp.read()
            self.assertTrue(logdata.find(b"All set and ready to roll") > -1, f"Errror no all set in {f}")

        print(f"[+] {tname} completed successfully")


    def test_fuzz_first_crash(self):
        """
        Tests to see if can get multiple crashes over 3 x 30sec runs
        :return:
        """
        import json
        tname = "test_fuzz_first_crash"
        print(f"[+] {tname} started")
        with open("/test/witcher_config.json","r") as jfp:
            jdata = json.load(jfp)
        jdata["timeout"] = 30
        with open("/test/witcher_config.json", "w") as jfp:
            json.dump(jdata, jfp)

        cmd = DEFUALT_CMD_CRASH + ["--target","test-2.php"]
        self.start_fuzzer(tname, cmd, timeout=120)

        # results = open('/tmp/unitest_out_popen.log', "rb").read()
        crashfiles = glob.glob("/results/unittests-EXWICHR/tr?_test+test-2.php/fuzzer-*/crashes/id*")
        crashcnt = len(crashfiles)

        self.assertTrue(crashcnt >= 1, f"Problem with number of crashes found cnt= {crashcnt}, dirlist={crashfiles}")
        fuzzlogs = glob.glob("/results/unittests-EXWICHR/tr?_test+test-2.php/fuzzer-1.log")
        runcnt = len(fuzzlogs)
        self.assertTrue(runcnt==1, f"Problem with number of directories created runcnt = {runcnt}, dirlist={fuzzlogs}")

        print(f"[+] {tname} completed successfully")

    def test_fuzz_sub_indexing_and_skip(self):
        """
        Tests the multiple trials
        :return:
        """
        import json
        tname = "fuzz_sub_indexing_and_skipper"
        print(f"[+] {tname} started")
        with open("/test/witcher_config.json","r") as jfp:
            jdata = json.load(jfp)

        jdata["number_of_refuzzes"] = 1
        jdata["number_of_trials"] = 1
        jdata["timeout"] = 5
        jdata["script_random_order"] = 0
        jdata["script_start_index"] = 2
        jdata["script_end_index"] = 5
        jdata["script_skip_list"] = ["test-5.php"]

        with open("/test/witcher_config.json", "w") as jfp:
            json.dump(jdata, jfp)

        self.start_fuzzer(tname, DEFUALT_CMD_CRASH, timeout=200)

        with open("/results/unittests-EXWICHR/fuzz_campaign_status.json","r") as jfp:
            jconfig = json.load(jfp)

        for target in jconfig[0]["targets"][jdata["script_start_index"]:jdata["script_end_index"]]:
            skipper_found = False
            for skipper in jdata["script_skip_list"]:
                if target["target_path"].find(skipper) > -1:
                    print(f"skipping {target['target_path']}")
                    skipper_found = True
                    break
            if skipper_found:
                continue
            encoded_path = target["target_path"].replace("/app/","").replace("/", "+")
            results_dirs = glob.glob("/results/unittests-EXWICHR/tr0*")
            full_path = f"/results/unittests-EXWICHR/tr0_{encoded_path}"
            self.assertTrue(os.path.isdir(full_path), f"Directory with {full_path} does not exist in {results_dirs}")

        print(f"\n[+] {tname} completed successfully\n")

        sys.stdout.flush()

    def test_fuzz_ztrials(self):
        """
        Tests the multiple trials
        :return:
        """
        import json
        tname = "fuzz_trials"
        print(f"[+] {tname} started")
        with open("/test/witcher_config.json","r") as jfp:
            jdata = json.load(jfp)

        jdata["number_of_trials"] = 3
        jdata["timeout"] = 20
        with open("/test/witcher_config.json", "w") as jfp:
            json.dump(jdata, jfp)

        cmd = DEFAULT_CMD + ["--target", "test-2.php"]
        self.start_fuzzer(tname, cmd, timeout=200)

        # results = open('/tmp/unitest_out_popen.log', "rb").read()
        crash_inputs = glob.glob("/results/unittests-EXWICHR/tr?_test+test-2.php/fuzzer-*/crashes/id*")
        crashcnt = len(crash_inputs)
        print(f"Problem with number of crashes created runcnt = {crashcnt}, dirlist={crash_inputs}")

        fuzzlogs = glob.glob("/results/unittests-EXWICHR/tr?_test+test-2.php/fuzzer-1.log")
        runcnt = len(fuzzlogs)
        print(f"fuzzlogs = {fuzzlogs}, runcnt={runcnt}")
        self.assertTrue(runcnt == 3, f"Problem with number of directories created runcnt = {runcnt}, dirlist={fuzzlogs}")

        print(f"[+] {tname} completed successfully")

        sys.stdout.flush()


if __name__ == '__main__':
    with open('/tmp/unitest_out_popen.log', "wb") as outfile:
        outfile.write(b"")
    try:
        unittest.main(failfast=True)
    except KeyboardInterrupt:
        end_reason = "Keyboard Interrupt"
        print("\n[*] Aborting wait unittest...")

    except Exception as e:
        end_reason = "Exception occurred"
        print("\n[*] Unknown exception received (%s). Terminating fuzzer." % e)

        raise



