
from flask import Flask, request
from sys import *
from subprocess import Popen
import os.path
from time import sleep
import mysql.connector

print(api_version)

print(os.path.isfile('/etc/passwd'))

sleep(.01)

app = Flask(__name__)



@app.route("/", methods=['GET', 'POST'])
def home():
    print(f"\tinside app: form={request.form}")
    print(f"\tinside app: args={request.args.get('A')}")
    print(f"\tinside app: args={request.cookies}")
    if request.args.get("A") == "1":
        print("\n\t\tMORE STUFF\n")

    if request.args.get("A") == "2":
        print("\n\t\tDIFFERENT STUFF\n")
    if request.args.get("A") == "3":
        print("\n\t\tDIFFERENT STUFF3\n")
        #Popen(["ls'"], shell=True)
    if request.args.get("A") == "4":
        print("\n\t\tDIFFERENT STUFF4\n")
        #os.system("ls '")

    if request.args.get("A") == "5":
        print("\n\t\tDB error??")
        mydb = mysql.connector.connect(host="127.0.0.1", user="root")
        mydb.cursor().execute("SELECT '")
        mydb.close()

    if request.args.get("A") and request.args.get("A").startswith("c"):
        if request.args.get("A") == "calli":
            print("\n\t\tDIFFERENT STUFF\n")
            #os.system("ls '")

    return "Hello, World!"


@app.route("/post")
def homepost():

    return "Hello, POST!"



if __name__ == "__main__":
    from timeit import default_timer

    start_time = default_timer()

    app.run(debug=True, port=8371)

    end_time = default_timer()

    print("time diff == %f" % (end_time-start_time))
