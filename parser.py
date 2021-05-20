#!/usr/bin/env python
#!/usr/bin/env -S python3 -m IPython
import requests
import json
import argparse
import os
from urllib.parse import urlparse

parser = argparse.ArgumentParser()
parser.add_argument('-f', '--fuzzer-cmd', default='fuzzer-master.cmd')
parser.add_argument('-p', '--port', default=8080, type=int)
parser.add_argument('crash_file')
ARGS = parser.parse_args()

with open(ARGS.fuzzer_cmd) as f:
    lines = f.read().strip().split('\n')
    cmd = lines[0]
    assert not lines[1].strip()
    env = {}
    for l in lines[2:]:
        name = l[:l.index("=")]
        val = l[l.index("=")+1:]
        env[name] = val

script_file = env['SCRIPT_FILENAME']

if script_file.startswith('http://'):
    parsed = urlparse(script_file)
    endpoint = parsed.path.lstrip('/')
else:
    assert script_file.startswith('/')
    docroot = env['DOCUMENT_ROOT']
    if script_file.startswith(docroot):
        endpoint = script_file[len(docroot):].lstrip('/')
    else:
        _, endpoint = script_file.split("/www/")

method = env['METHOD']
assert method.upper() in {'GET', 'POST'}
login_cookie = env.get('LOGIN_COOKIE', '')

with open(ARGS.crash_file, 'rb') as f:
    data = f.read().split(b'\0')
    print(data)    
    if len(data) == 1:
        cookies = data[0]
        query_string = b''
        post_data = b''
    elif len(data) == 2:
        cookies = data[0]
        query_string = data[1]
        post_data = b''
    else:
        cookies, query_string, post_data = data[:3]


URL = f'http://localhost:{ARGS.port}/{endpoint}?'.encode() + query_string
print("URL: " + repr(URL))
print("Params")
for k in query_string.split(b"&"):
    print("\t" + repr(k))
print("Cookies: ")
print("\t" + repr(login_cookie.encode() + cookies))
print("Payload")
for k in post_data.split(b'&'):
    print('\t' + repr(k))
