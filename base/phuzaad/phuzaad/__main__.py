#!/usr/bin/env python

import argparse
import paramiko
import logging
import json
import os

log = logging.getLogger(__name__)
paramiko.common.logging.basicConfig(level=paramiko.common.DEBUG)

class server():
    def __init__(self, server_info):
        self._name = server_info["name"]
        self._ip = server_info["ip"]
        self._port = server_info["port"]
        self._username = server_info["username"]

        self._private_key = server_info["private_key"]
        if not os.path.exists(self._private_key):
            raise Exception(f"The private key does not exist at {self._private_key}")

        self.ssh = paramiko.SSHClient()

    def connect(self):

        try:
            self.ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
            key = paramiko.RSAKey.from_private_key_file(self._private_key)
            print(key.get_fingerprint())

            self.ssh.connect(self._ip, port=self._port, username=self._username, pkey=key, look_for_keys=False)
        except paramiko.SSHException as exp:
            log.exception(exp)
            print(f"ip={self._ip}")

    def ssh_cmd(self, cmd):
        try:
            chan = self.ssh.get_transport().open_session()

            log.info(f"{self._name} Running {cmd}")
            stdin, stdout, stderr = chan.exec_command(cmd)

            return {"stdin": stdin, "stdout":stdout, "stderr":stderr, "return_val": chan.recv_exit_status()}

        except paramiko.SSHException as sshError:
            log.error(f"ERROR while connecting to {self._name}, ip={self._ip}")
            log.error(sshError)
        return None



def main():
    parser = argparse.ArgumentParser(description="Phuzzer at a Distance")
    parser.add_argument('-c', '--config', help="Config file for servers", default="config.json")

    args = parser.parse_args()

    if os.path.isfile(os.path.join(os.getcwd(), args.config)):
        with open(args.config,"r") as jfile:
            jdata = json.load(jfile)
    else:
        print("ERROR: config file does not exist")

    servers = []
    for serv in jdata:
        servers.append(server(serv))

    servers[0].connect()
    print(servers[0].ssh_cmd("ls -la /"))


if __name__ == "__main__":
    main()
