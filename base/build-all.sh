#!/usr/bin/env bash


cd /p/Witcher/docker
docker build -t witcher .
if [[ $? -ne 0 ]]; then
    echo "Failed building base Witcher docker images   "   
    exit 1
fi

cd /p/Witcher/php7
docker build -t witcher/php7 .

if [[ $? -ne 0 ]]; then
    echo "Failed building WC:php docker images"
    exit 2
fi


cd /p/Witcher/php7/tests/openemr
docker build -t witcher/php7/openemr .
if [[ $? -ne 0 ]]; then
    echo "Failed building base WC:php-openemr docker images"
    exit 3
fi


cd /p/Witcher/php7/tests/microtests
docker build -t witcher/php/microtests .
if [[ $? -ne 0 ]]; then
    echo "Failed building base WC:php-microtests docker images"
    exit 3
fi



cd /p/Witcher/cgi/tests
docker build -t witcher/cgi .

if [[ $? -ne 0 ]]; then
    echo "Failed building WC:cgi docker images"
    exit 2
fi

