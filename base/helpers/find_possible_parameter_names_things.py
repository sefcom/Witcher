#! /usr/bin/env python3

import os
import json
import functools
import sys
import re
import string
import json

def all_in(charset, s):
    cs = set(charset)
    s = s.decode('ascii')
    return all(c in charset for c in s)
confidence_boost_chars = [ functools.partial(all_in, string.ascii_lowercase + '_'),
                           functools.partial(all_in, string.ascii_lowercase + string.ascii_uppercase + '_'),
                           functools.partial(all_in, string.ascii_lowercase + string.digits + '_'),
                           functools.partial(all_in, string.ascii_lowercase + string.digits + '_'),
                           functools.partial(all_in, string.ascii_lowercase + string.ascii_uppercase + string.digits + '_')
                         ]

#with open('burp-parameter-names.txt', 'r') as f:
#    BURP_PARAMS = set(f.read().strip().split('\n'))

#def confidence_boost_burp(s):
#    return s in BURP_PARAMS

def classify_confidence(s):
    total = 0
    l = []
    for num, f in enumerate(confidence_boost_chars):
        if f(s):
            total += 1
            l.append(f"confidence_boost_chars{num}")
    return total, l

with open(sys.argv[1], 'rb') as f:
    binary = f.read()
    results = [(classify_confidence(s), s) for s in set(re.findall(b'[0-9a-zA-Z._!~*\'()-]+', binary))]

#    with open(sys.argv[1] + '.get_params', 'w') as f:
#        for c, s in sorted(results):
#            f.write('{},{}\n'.format(c, s))

    min_conf=int(sys.argv[2])

    with open(sys.argv[3],"r") as jf:
        jdata = json.load(jf)

    for i, (c, s) in enumerate(results):
        #print(c[0], min_conf)
        if c[0] < min_conf:
            continue
        s = s.decode('ascii')
        key = f"inputSet-{os.path.basename(sys.argv[1])}"
        if key not in jdata:
            jdata[key] = set()
        else:
            jdata[key] = set(jdata[key])
        jdata[key].add(s)
        #print(f"Adding {s}")
        jdata[key] = list(jdata[key])
    with open(sys.argv[3], 'w+') as jf:
        json.dump(jdata, jf)
