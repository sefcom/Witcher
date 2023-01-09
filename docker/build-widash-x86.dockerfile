FROM witcher/basebuild

RUN apt-get install -y gcc-multilib

COPY /Widash /Widash

RUN cd /Widash; ./autogen.sh && automake; bash ./x86-build.sh
