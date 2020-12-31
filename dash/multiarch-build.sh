#!/usr/bin/env bash
set -x
mkdir -p archbuilds

export CC=/usr/bin/mips-linux-gnu-gcc
OUTEXT=mipseb
if ! ./configure --host ${CC} --enable-static; then
    printf "\033[32mCONFIGURE FAILED on $CC\033[0m"
    exit 81
fi
make clean
if ! make -j$(nproc); then
    printf "\033[32mBUILD FAILED on $CC\033[0m"
    exit 91
fi
cp src/dash archbuilds/dash.${OUTEXT}

export CC=/usr/bin/mipsel-linux-gnu-gcc
OUTEXT=mipsel
if ! ./configure --host ${CC} --enable-static; then
    printf "\033[32mCONFIGURE FAILED on $CC\033[0m"
    exit 82
fi
make clean
if ! make -j$(nproc); then
    printf "\033[32mBUILD FAILED on $CC\033[0m"
    exit 92
fi
cp src/dash archbuilds/dash.${OUTEXT}

export CC=/usr/bin/arm-linux-gnueabi-gcc
OUTEXT=armel
if ! ./configure --host ${CC} --enable-static; then
    printf "\033[32mCONFIGURE FAILED on $CC\033[0m"
    exit 83
fi
make clean
if ! make -j$(nproc); then
    printf "\033[32mBUILD FAILED on $CC\033[0m"
    exit 93
fi
cp src/dash archbuilds/dash.${OUTEXT}

unset CC
unset OUTEXT

