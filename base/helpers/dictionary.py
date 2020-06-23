#! /usr/bin/env python3

import sys
import string

def hexescape(s):
    '''
    perform hex escaping on a raw string s
    '''

    out = []
    acceptable = (string.ascii_letters + string.digits + " .").encode()
    for c in s:
        if c not in acceptable:
            out.append("\\x%02x" % c)
        else:
            out.append(chr(c))

    return ''.join(out)

if len(sys.argv) < 3:
    print("ERROR, must provide to and from dictionary")
    sys.exit(78)

dictionary = open(sys.argv[1],"rb").read().split(b"\n")
with open(sys.argv[2], "w") as df:
    for i,s in enumerate(set(dictionary)):
        if len(s) == 0:
            continue
        s_val = hexescape(s)
        df.write("string_%d=\"%s\"" % (i, s_val) + "\n")
