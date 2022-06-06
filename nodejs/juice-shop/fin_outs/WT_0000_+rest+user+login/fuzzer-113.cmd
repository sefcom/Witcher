/p/afl/afl-fuzz -i /tmp/output/initial_seeds -o /tmp/output/ -m 8G -S fuzzer-113 -x /tmp/output/dict.txt -t 20000+ -- /p/webcam/javascript/node/node app --single-threaded
