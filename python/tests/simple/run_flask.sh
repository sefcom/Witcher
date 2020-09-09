#!/usr/bin/env bash

source /home/wc/.virtualenvs/pywitch/bin/activate
##workon pywhich
echo "** FLASK_RUN_PORT=$FLASK_RUN_PORT"
echo "** FLASK_APP=$FLASK_APP"
echo "** STRICT=$STRICT"
echo "** VIRTUAL_ENV=$VIRTUAL_ENV"
echo "** SCRIPT_FILENAME=$SCRIPT_FILENAME"
/home/wc/.virtualenvs/pywitch/bin/python3  /home/wc/.virtualenvs/pywitch/bin/flask run


