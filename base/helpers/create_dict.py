#! /usr/bin/env python3
import bs4
import argparse
import glob
import os.path
import tempfile


def varformat(varname):
    varname = varname.lower()
    if varname.find("mac") > -1 and varname.find("add"):
        return "AABBCCDDEEFF"
    return "1"

def search(content, seedir):

    input_tags = bs4.BeautifulSoup(content,"lxml").findAll('input')
    these_names = set()
    for tag in input_tags:
        if tag.has_attr("name"):
            #if "[" in tag['name'] and "]" in tag['name']
            if "<?" not in tag['name'] and not ("[" in tag['name'] and "]" in tag['name']):
                these_names.add(tag['name'].replace("'","").replace("\n","").strip())


    if len(these_names) > 0:
        varsout = ""
        _, filepath = tempfile.mkstemp(prefix="seed_", dir=seedir)
        f = open(filepath, "w")
        f.write("A\x00")
        for name in these_names:
            varsout += f"{name}={varformat(name)}&"
        #print(f"varsout = {repr(varsout)}")
        f.write(f"{varsout}\x00{varsout}\x00")
        f.close()
    #print("-" * 50)

    return these_names
        # for field in form:
        #     print(f"field is {isinstance(field, bs4.element.NavigableString)} and {hasattr(field,'has_key')} ==> {field}")
        #     if isinstance(field, bs4.element.NavigableString):
        #         print (dir(field))
        #         print(field.find_next())
        #     print(field)
        #     if hasattr(field,"attrs"):
        #         if "name" in field.attrs:
        #             print(field.attrs["name"])


def create_seeds(basedir, filter):
    names = set()
    seedir = os.path.join(basedir, "input")
    dictfile = os.path.join(basedir, "dict.txt")
    for f in glob.glob(os.path.join(seedir, "seed_*")):
        os.remove(f)

    for fp in glob.iglob(os.path.join(basedir, "code", "**"), recursive=True):
        fpext = (os.path.splitext(os.path.basename(fp))[-1][1:])
        # if os.path.basename(fp) != "boardDataWW.php":
        #     continue
        if fpext in filter and os.path.isfile(fp):
            #print(fp)
            content = open(fp).read()
            names |= search(content, seedir)

    od = open(dictfile, "w")
    for name in names:
        od.write(f"{name}=\n")
    od.write("&\n=\n\x00\n")
    od.close()


def main():
    parser = argparse.ArgumentParser(description="CGI-Fuzz ")
    parser.add_argument('basedir', help="basedir of the web application")
    parser.add_argument('-f', '--filter', default=["php","html"], nargs="+", help="File extensions to filter on")

    args = parser.parse_args()

    create_seeds(args.basedir, args.filter)


if __name__ == "__main__":
    main()

