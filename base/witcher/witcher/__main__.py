
from .witcher import Witcher
import argparse
import pathlib
import os


def parm_or_env(envname, argname, argdesc, parser):
    if envname in os.environ:
        parser.add_argument(f'--{argname}', help=argdesc, default=os.environ.get(envname))
    else:
        parser.add_argument(argname, help=argdesc)


def main():

    parser = argparse.ArgumentParser(description="Witcher fuzzer interface for web applications")
    parm_or_env("WC_TESTLOC", "testloc","the directory path with data about what's to be fuzzed ", parser)
    parm_or_env("WC_TESTVER", "testver", "Shortcut abbreviation for which configuration to use.", parser)
    parser.add_argument("--config", help="Name of config file for fuzzing session default is witcher_config.json",default="witcher_config.json")
    parser.add_argument("--appdir", help="Config file for fuzzing session", default="/app")
    parser.add_argument("--no-run","--no_run", action="store_true", default=(os.environ.get("WC_NO_RUN", "0")!="0"))
    parser.add_argument("-c","--cores", help="Config file for fuzzing session", default=int(os.environ.get("WC_CORES", "-1")))
    parser.add_argument("-t", "--timeout", help="Timeout for entire fuzzing session", type=float,
                        default=float(os.environ.get("WC_TIMEOUT", "3600")))
    parser.add_argument("--target", help="a single file to fuzz", type=str, default=None)
    parser.add_argument("-C", "--first_crash","--first", help="Run till firstcrash or timeout, config file has precendence", action="store_true", default=False )

    parser.add_argument("-A", "--affinity", help="CPU affinity starting core", type=str, default=None)
    parser.add_argument("-M", "--memory", help="Memory limit to pass to AFL (MB, or use k, M, G, T suffixes), config file has precendence", type=str, default="8G")

    parser.add_argument("--start_over", help="CPU affinity starting core", action="store_true", default=False )

    args = parser.parse_args()

    if args.no_run:
        print("NO Run option chosen, exiting witcher.")
        exit(0)


    if not os.path.exists(args.testloc):
        raise ValueError("The test location directory must exist, please supply an existing directory via WC_TESTLOC "
                         "envrionment variable or testloc positional argument.")
    args.testver = args.testver.upper()
    if args.testver not in Witcher.CONFIGURATIONS:
        print("The test verion (parameter or WC_TESTVER) must be one of the following AFLR, AFLHR, WICH, WICR, WICHR, EXWIC, EXWICH, EXWICHR, DEV")

    witcher = Witcher(args)
    witcher.start_fuzz_campaign()


    print("args", args)

if __name__ == "__main__":
    main()