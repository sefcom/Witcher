#! /usr/bin/env python3
import argparse
import json
import requests
import urllib.parse

class BurpBuff():
    def __init__(self, args):
        self.httprequests_fpath = args.httprequests_fpath

        self.proxy_dict = {"http": args.burpproxy}
        self.requestsFound = {}
        self.target_ip = args.target_ip

    def load_jdata(self):
        with open(self.httprequests_fpath, "r") as rf:
            self.requestsFound = json.load(rf).get("requestsFound", {})

    def go(self):

        self.load_jdata()

        for key, req in self.requestsFound.items():
            url = urllib.parse.urlparse(req.get("_urlstr", ""))
            url = url._replace(netloc=self.target_ip)

            headers = req.get("_headers", {})
            post_data = req.get("_postData","")

            urlstr = urllib.parse.urlunparse(url)

            if not url.path.endswith(".php"):
                continue
            print(url.geturl())
            if req.get("_method","GET").upper() == "GET":
                response = requests.get(urlstr, headers=headers, proxies=self.proxy_dict)
            else:
                response = requests.post(urlstr, headers=headers, data=post_data, proxies=self.proxy_dict)










def main():

    parser = argparse.ArgumentParser(description="Witcher fuzzer interface for web applications")
    parser.add_argument("httprequests_fpath", help="The file fuzz file with the requests")
    parser.add_argument("--burpproxy", help="The file fuzz file with the requests", default="127.0.0.1:8080")
    parser.add_argument("--target-ip","--target_ip", help="The file fuzz file with the requests", default="127.0.0.1")
    args = parser.parse_args()


    bb = BurpBuff(args)

    bb.go()


if __name__ == "__main__":
    main()