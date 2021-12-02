#!/usr/bin/env -S python3 -m IPython
import requests
import json
import argparse
import os

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

docroot = env['DOCUMENT_ROOT']
script_file = env['SCRIPT_FILENAME']

if script_file.startswith(docroot):
    endpoint = script_file[len(docroot):].lstrip('/')
else:
    docroot, endpoint = script_file.split("/www/")
method = env['METHOD']
assert method.upper() in {'GET', 'POST'}
login_cookie = env['LOGIN_COOKIE']

with open(ARGS.crash_file, 'rb') as f:
    data = f.read().split(b'\0')
    
    if len(data) == 1:
        cookies = b''
        query_string = data[0] if method == 'GET' else b''
        post_data = data[0] if method == 'POST' else b''
    elif len(data) == 2:
        cookies = data[0]
        query_string = data[1] if method == 'GET' else b''
        post_data = data[1] if method == 'POST' else b''
    else:
        cookies, query_string, post_data = data[:3]


URL = f'http://localhost:{ARGS.port}/{endpoint}'
if query_string:
    URL += '?' + query_string.decode()

headers = {
    'Cookie': login_cookie.encode() + b'; ' + cookies
}

print(f"REQUEST: {URL}, headers: {headers}, payload: {repr(post_data)}")
response = requests.request(method, URL, data=post_data)
print(response.status_code)
print(repr(response.content))
print(response.text)
with open('out.html', 'wb') as f:
    f.write(response.content)
os.system('firefox out.html')
