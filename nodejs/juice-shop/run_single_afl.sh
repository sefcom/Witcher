#!/usr/bin/env bash
cd app
STRICT=true METHOD=POST AFL_BASE=/tmp/output2 PORT=5144 NODE_BASE_URL="/" /p/afl-out/afl-fuzz -t 20000 -m 2048 -i ../input -o /tmp/output2 -- /p/webcam/javascript/node/node --single_threaded app
cd -
