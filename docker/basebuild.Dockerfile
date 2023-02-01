
FROM ubuntu:bionic
LABEL maintainer="erik.trickel@asu.edu"

# Use the fastest APT repo
#COPY ./files/sources.list.with_mirrors /etc/apt/sources.list
RUN dpkg --add-architecture i386 && apt-get update

ENV DEBIAN_FRONTEND noninteractive


# Install apt-fast to speed things up
RUN apt-get update --fix-missing && apt-get install -y aria2 curl wget virtualenvwrapper git && \
    #APT-FAST installation
    /bin/bash -c "$(curl -sL https://git.io/vokNn) " && \
    apt-fast update && apt-fast -y upgrade && apt-fast update --fix-missing
RUN    apt-fast install -y build-essential  \
                        #Libraries
                        libxml2-dev libxslt1-dev libffi-dev cmake libreadline-dev \
                        libtool debootstrap debian-archive-keyring libglib2.0-dev libpixman-1-dev \
                        libssl-dev qtdeclarative5-dev libcapnp-dev libtool-bin \
                        libcurl4-nss-dev libpng-dev libgmp-dev \
                        # x86 Libraries
                        libc6:i386 libgcc1:i386 libstdc++6:i386 libtinfo5:i386 zlib1g:i386 \
                        #python 3
                        python3-pip \
                        #Utils
                        sudo automake  net-tools netcat  \
                        ccache make g++-multilib pkg-config coreutils rsyslog \
                        manpages-dev ninja-build capnproto  software-properties-common zip unzip pwgen \
                        libxss1 bison flex \
			            gawk cvs ncurses-dev

COPY /httpreqr /httpreqr
RUN cd /httpreqr && make 

COPY wclibs /wclibs
RUN cd /wclibs && \
    gcc -c -Wall -fpic db_fault_escalator.c && \
    gcc -shared -o lib_db_fault_escalator.so db_fault_escalator.o -ldl && \
    rm -f /wclibs/libcgiwrapper.so && \
    ln -s /wclibs/lib_db_fault_escalator.so /wclibs/libcgiwrapper.so && \
    ln -s /wclibs/lib_db_fault_escalator.so /lib/libcgiwrapper.so

#COPY --from=puppeteer1337/build-widash-x86 /Widash/archbuilds/dash /crashing_dash
COPY /Widash /Widash 
RUN cd /Widash; ./autogen.sh && automake; bash ./x86-build.sh




