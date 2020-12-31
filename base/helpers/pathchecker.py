#!/usr/bin/env python
from __future__ import print_function, absolute_import, division

import logging

from errno import EINVAL
from stat import S_IFDIR, S_IFREG
from time import time
import inspect
from fuse import FUSE, FuseOSError, Operations, LoggingMixIn, fuse_get_context

import signal
import os


class CrashFS(LoggingMixIn, Operations):
    'Example filesystem to demonstrate fuse_get_context()'

    # these should need some kind of open first, so should be impossible
    read = None
    readdir = None

    def go_die(self, args, kwargs, errno):
        uid, gid, pid = fuse_get_context()
        fname = inspect.stack()[1][3]
        call_str = f'{fname}(' + ', '.join(map(repr, args))
        if kwargs:
            call_str += ', ' + ', '.join(f'{k}={repr(v)}' for k, v in kwargs.items())
        call_str += ')'
        print(f"Killing uid={uid} gid={gid} pid={pid}: {call_str}")
        os.kill(pid, signal.SIGSEGV)
        raise FuseOSError(errno)

    def getattr(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def getxattr(self, *args, **kwargs):                self.go_die(args, kwargs, EINVAL)

    def listxattr(self, *args, **kwargs):               self.go_die(args, kwargs, EINVAL)

    def create(self, *args, **kwargs):                  self.go_die(args, kwargs, EINVAL)

    def access(self, *args, **kwargs):                  self.go_die(args, kwargs, EINVAL)

    def flush(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)

    def open(self, *args, **kwargs):                    self.go_die(args, kwargs, EINVAL)

    def opendir(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def release(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def releasedir(self, *args, **kwargs):              self.go_die(args, kwargs, EINVAL)

    def statfs(self, *args, **kwargs):                  self.go_die(args, kwargs, EINVAL)

    def chmod(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)

    def chown(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)

    def mkdir(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)

    def read(self, *args, **kwargs):                    self.go_die(args, kwargs, EINVAL)

    def readdir(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def readlink(self, *args, **kwargs):                self.go_die(args, kwargs, EINVAL)

    def removexattr(self, *args, **kwargs):             self.go_die(args, kwargs, EINVAL)

    def rename(self, *args, **kwargs):                  self.go_die(args, kwargs, EINVAL)

    def rmdir(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)

    def setxattr(self, *args, **kwargs):                self.go_die(args, kwargs, EINVAL)

    def symlink(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def truncate(self, *args, **kwargs):                self.go_die(args, kwargs, EINVAL)

    def unlink(self, *args, **kwargs):                  self.go_die(args, kwargs, EINVAL)

    def utimens(self, *args, **kwargs):                 self.go_die(args, kwargs, EINVAL)

    def write(self, *args, **kwargs):                   self.go_die(args, kwargs, EINVAL)


if __name__ == '__main__':
    import argparse

    parser = argparse.ArgumentParser()
    parser.add_argument('mount')
    args = parser.parse_args()

    logging.basicConfig(level=logging.DEBUG)
    fuse = FUSE(CrashFS(), args.mount, foreground=True, ro=True, allow_other=True)
