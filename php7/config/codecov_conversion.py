#! /usr/bin/env python3
import argparse
import glob
from time import sleep
import os
import subprocess
import re
import json
import datetime
import signal
import traceback

def handler(signum, frame):
    print("Ctrl-c was pressed. Exiting...")
    exit(14)

def process_codecov(fn, script_name, cur_ccdata):

    with open(fn, "r") as rf:
        new_ccdata = json.load(rf)

    changed = False
    # cycle through scripts in new input
    for new_scriptname, new_scriptcc in new_ccdata.items():
        cur_scriptcc = cur_ccdata.get(new_scriptname, {})
        for new_lineno, new_ccval in new_scriptcc.items():
            cur_ccval = cur_scriptcc.get(new_lineno, -3)
            #if current value does not exist -3 OR is -1 or -2 and new_ccval > 0 then replace with new value and mark to save
            if cur_ccval == -3 or (cur_ccval < 0 < new_ccval):
                cur_scriptcc[new_lineno] = new_ccval
                cur_ccdata[new_scriptname] = cur_scriptcc
                changed = True

    if changed:
        save_cc(script_name, cur_ccdata)

    return cur_ccdata


def save_cc(script_name, cur_ccdata):
    cc_fpath = os.path.join("/tmp", "coverages", script_name + ".cc.json")

    with open(cc_fpath+".new", "w") as wf:
        json.dump(cur_ccdata, wf)

    os.system(f"cp {cc_fpath}.new {cc_fpath}")



def load_cc(script_name):
    cc_fpath = os.path.join("/tmp", "coverages", script_name + ".cc.json")
    if os.path.exists(cc_fpath):
        with open(cc_fpath, "r") as rf:
            ccdata = json.load(rf)
        return ccdata
    return {}


def save_execs(execs):
    execs_fpath = "/tmp/coverages/execs.json"

    with open(execs_fpath, "w") as wf:
        json.dump(execs, wf)


def load_execs():
    execs_fpath = "/tmp/coverages/execs.json"
    if os.path.exists(execs_fpath):
        with open(execs_fpath, "r") as rf:
            ccdata = json.load(rf)
        return ccdata

    now = datetime.datetime.now()
    return {"start_time": now.strftime('%Y-%m-%d %H:%M:%S'), "last_time": now.strftime('%Y-%m-%d %H:%M:%S'), "execs": 0}

def main():
    parser = argparse.ArgumentParser(description="Convert code covs from memory to codecovs file")
    #parser.add_argument('-c', '--config', help="Config file for servers", default="config.json")

    args = parser.parse_args()
    memdir = "/dev/shm/coverages"
    fn_regex = re.compile(r"(\+.*?)_[1-5][0-9]+\.cc\.json")

    execs = load_execs()
    while True:
        try:
            if not os.path.exists(memdir):
                print(f"{memdir} does not exist, waiting for it to be born.")
                sleep(10)
                continue
            base = ""
            last_script = ""
            filelist = glob.glob(memdir + "/*.cc.json")
            filelist.sort()
            cur_ccjson = {}
            for fn in filelist:
                try:

                    nowstr = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                    execs["execs"] = execs["execs"] + 1
                    execs["last_time"] = nowstr

                    match = fn_regex.match(os.path.basename(fn))

                    if match:
                        print(f"processing {fn}")
                        cur_script = match.group(1)
                    else:
                        print(f"no match found on {fn}")
                        continue

                    # are we still working from same URL?
                    if cur_script != last_script:
                        if os.path.exists(base):
                            os.remove(base)
                        base = fn
                        last_script = cur_script
                        cur_ccjson = load_cc(cur_script)
                        if cur_script not in execs:
                            execs[cur_script] = {"last_time": nowstr, "execs": 1, "create_ts": nowstr}
                        cur_ccjson = process_codecov(fn, cur_script, cur_ccjson)

                    else:

                        exec_inc = execs[cur_script]["execs"] + 1
                        execs[cur_script] = {"last_time": nowstr, "execs": exec_inc }
                        p = subprocess.run(["/usr/bin/diff", base, fn], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
                        if p.returncode == 0:
                            os.remove(fn)
                        else:
                            cur_ccjson = process_codecov(fn, cur_script, cur_ccjson)
                            os.remove(fn)
                except Exception:
                    traceback.print_exc()
            save_cc(last_script, cur_ccjson)
            if base.strip != "" and os.path.exists(base):
                os.remove(base)

            save_execs(execs)

            print("sleeping....")
            os.system("rm -f /tmp/coverages/*.cc.json.new")
            sleep(8)

        except Exception as ex:

            traceback.print_exc()



if __name__ == "__main__":
    signal.signal(signal.SIGINT, handler)
    main()




