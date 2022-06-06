#!/usr/bin/env bash

if [[ "$1" == "--ppc" ]]; then
    arch="ppc"
    rm  cgiwrapper2.o lib${arch}cgiwrapper.so
    GCC=/usr/bin/powerpc-linux-gnu-gcc
    export LIBRARY_PATH=/wclibs
    #PARAMS="-Xlinker -rpath=/wclibslib -nostdinc -I/p/webcam/cgi/lib "
    PARAMS=""
    ${GCC} ${PARAMS} -c -Wall -fpic cgiwrapper2.c && ${GCC} ${PARAMS} -shared -o lib${arch}cgiwrapper.so cgiwrapper2.o
    if [[ $? -eq 0 ]]; then
        echo "BUILDING PPC VERSION  /lib/libtestcgiwrapper.so !!!!!!!!!"
        sudo cp lib${arch}cgiwrapper.so /lib/lib${arch}cgiwrapper.so && echo 'SUCCESS!!'
    else
        echo "COMPIULE TO PPC FAILED"
    fi
elif [[ "$1" == "--arm" ]]; then
    arch="arm"
    rm  cgiwrapper2.o lib${arch}cgiwrapper.so
    GCC=/usr/bin/arm-linux-gnueabi-gcc
    export LIBRARY_PATH=/wclibs
    #PARAMS="-Xlinker -rpath=/wclibslib -nostdinc -I/p/webcam/cgi/lib "
    PARAMS=""
    ${GCC} ${PARAMS} -c -Wall -fpic cgiwrapper2.c && ${GCC} ${PARAMS} -shared -o lib${arch}cgiwrapper.so cgiwrapper2.o
    if [[ $? -eq 0 ]]; then
        echo "BUILDING ${arch} VERSION  /lib/libtestcgiwrapper.so !!!!!!!!!"
        sudo cp lib${arch}cgiwrapper.so /lib/lib${arch}cgiwrapper.so && echo 'SUCCESS!!'
    else
        echo "COMPILE TO $arch FAILED"
    fi
elif [[ "$1" == "--test" ]]; then
    rm  cgiwrapper2.o libtestcgiwrapper.so
    GCC=/usr/bin/arm-linux-gnueabi-gcc
    export LIBRARY_PATH=/p/webcam/cgi/lib/
    PARAMS="-Xlinker -rpath=/p/webcam/cgi/lib -nostdinc -I/p/webcam/cgi/lib "
    ${GCC} ${PARAMS} -c -Wall -fpic cgiwrapper2.c && ${GCC} ${PARAMS} -shared -o libtestcgiwrapper.so cgiwrapper2.o  && sudo cp libtestcgiwrapper.so /lib/libtestcgiwrapper.so && echo 'SUCCESS!!'
    if [[ $? -eq 0 ]]; then
        echo "BUILDING TEST VERSION  /lib/libtestcgiwrapper.so !!!!!!!!!"
        echo "BUILDING TEST VERSION /lib/libtestcgiwrapper.so!!!!!!!!!"
        echo "BUILDING TEST VERSION /lib/libtestcgiwrapper.so!!!!!!!!!"
        echo "BUILDING TEST VERSION /lib/libtestcgiwrapper.so!!!!!!!!!"
    fi
elif [[ "$1" == "--clang" ]]; then
    clang -c -Wall -fpic cgiwrapper.c && clang -shared -o libclangcgiwrapper.so cgiwrapper.o -ldl && sudo cp libclangcgiwrapper.so /lib/libclangcgiwrapper.so && echo 'SUCCESS!!'
    if [[ $? -eq 0 ]]; then
        echo "BUILDING CLANG VERSION  /lib/libclangcgiwrapper.so !!!!!!!!!"
        echo "BUILDING CLANG VERSION /lib/libclangcgiwrapper.so!!!!!!!!!"
    fi

else
    gcc -c -Wall -fpic cgiwrapper.c && gcc -shared -o libcgiwrapper.so cgiwrapper.o -ldl && sudo cp libcgiwrapper.so /lib/libcgiwrapper.so && echo 'SUCCESS!!'
fi