#!/usr/bin/env python3
import re
import sys
import json
import argparse
import glob
import tqdm
import time
import os.path
import logging
import tempfile
import subprocess


class CodeCov():
    def __init__(self, args):

        self.base = os.path.dirname(os.path.realpath(__file__))

        self.fuzz_dirs = []
        self.crawl_dirs = []
        self.burp_dirs = []
        self.burpplus_dirs = []

        self.crawl = args.crawl
        self.burp = args.burp
        self.burpplus = args.burpplus
        dirs = next(os.walk(self.base))[1]
        for d in dirs:
            exp_fuzz_dpath = os.path.join(self.base, d, "wcov")
            if os.path.exists(exp_fuzz_dpath):
                self.fuzz_dirs.append(exp_fuzz_dpath)

            if self.crawl:
                exp_crawl_dpath = os.path.join(self.base, d, "ccov")
                if os.path.exists(exp_crawl_dpath):
                    self.crawl_dirs.append(exp_crawl_dpath)

            if self.burp:
                exp_burp_dpath = os.path.join(self.base, d, "burp","coverages")
                if os.path.exists(exp_burp_dpath):
                    self.burp_dirs.append(exp_burp_dpath)

            if self.burpplus:
                exp_burpplus_dpath = os.path.join(self.base, d, "burpplus", "coverages")
                if os.path.exists(exp_burpplus_dpath):
                    self.burpplus_dirs.append(exp_burpplus_dpath)


    def start_running(self):

        if self.burp:
            print(f"\033[34mBurp CC Values\033[0m")
            self.calc_for_dirs(self.burp_dirs, True )
            return

        if self.burpplus:
            print(f"\033[34mBurpPlus CC Values\033[0m")
            self.calc_for_dirs(self.burpplus_dirs, True )
            return

        if self.crawl:
            print(f"\033[34mCrawled CC Values\033[0m")
            self.calc_for_dirs(self.crawl_dirs)

        print(f"\033[34mFuzzed CC Values\033[0m")
        self.calc_for_dirs(self.fuzz_dirs)


    def calc_for_dirs(self, dirs, use_deeper_dir=False):

        for stage_dir in sorted(dirs):
            jout = self.merge_cc(stage_dir)
            covered_cnt, missed_cnt, total_cnt = self.calc_coverage(jout)
            dirname = os.path.basename(os.path.dirname(stage_dir))
            if use_deeper_dir:
                dirname = os.path.basename(os.path.dirname(os.path.dirname(stage_dir)))
            print(f"{dirname}, {covered_cnt}, {missed_cnt}, {total_cnt}")


    def calc_coverage(self, jout):
        covered_cnt = 0
        missed_cnt = 0
        total_cnt = 0
        for path, codecovs in jout.items():
            for line, cov_val in codecovs.items():
                if cov_val > 0:
                    covered_cnt += 1
                else:
                    missed_cnt += 1
                total_cnt += 1
        return covered_cnt, missed_cnt, total_cnt

    def merge_cc(self, stage_dir):
        jout = {}

        for jp in glob.glob(stage_dir + "/*cc.json"):

            with open(jp, "r") as rf:
                jdata = json.load(rf)
            for path, codecovs in jdata.items():
                if path == "/enable_cc.php":
                    continue
                if path not in jout:
                    jout[path] = {}
                for line, cov_val in codecovs.items():
                    if jout[path].get(line, -3) < 0:
                        jout[path][line] = cov_val
        return jout


def main():
    parser = argparse.ArgumentParser(description="Run code coverage for a set of inputs")
    #parser.add_argument('expdir', help="basedir of the results, where the openemr-EXWICHR directory lies")
    parser.add_argument('--burp', help="calculate for burp ", action="store_true", default=False)
    parser.add_argument('--burpplus', help="calculate for burpplus ", action="store_true", default=False)
    parser.add_argument('--crawl', help="calculate for crawl stage", action="store_true", default=False)

    parser.add_argument('-o', '--output', default="./code_coverage.json", help="Where to put the json output file, default is /results/code_cov.json")

    args = parser.parse_args()

    cc = CodeCov(args)
    cc.start_running()



if __name__ == "__main__":
    main()

