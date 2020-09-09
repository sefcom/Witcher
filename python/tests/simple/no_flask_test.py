#from flask import Flask, request
import sys
from time import sleep
from opcode import *

#app = Flask(__name__)

AAAA = 0xAAAAAAAA
BBBB = 0xBBBBBBBB
CCCC = 0xCCCCCCCC
counter = 0

print("this is a python script") # break 3474
#print("sleeping for 10 seconds.. 0x%X" % AAAA )

#time.sleep()   # break time_sleep
if len(sys.argv) > 1:
    if sys.argv[1] == "1":
        AAAA = BBBB
    else:
        AAAA = CCCC
    for cnt in range(0, int(sys.argv[1])):
        counter += cnt

print("done sleeping 0x%X... counter=%d" % (AAAA, counter))

print("exiting")



