import glob
import json

for f in glob.iglob("node_modules/**/package.json", recursive=True):

    with open(f, "r") as jf:
        jdata = json.load(jf)
    if "_requested" in jdata:
        del jdata["_requested"]
    if "repository" in jdata:
        del jdata["repository"]
    with open(f, "w") as jf:
        json.dump(jdata, jf)