
FROM witcher/php7

### for webfuzz
RUN add-apt-repository ppa:deadsnakes/ppa && apt-get update
RUN apt-get update && apt-get install -y python3.8 python3.8-dev firefox
RUN add-apt-repository -y ppa:ondrej/php && apt-get update && apt-get install -y php7.4

RUN curl https://bootstrap.pypa.io/get-pip.py -o get-pip.py | python3.8 - ; python3.8 get-pip.py

RUN python3.8 -m pip install bs4 typed-argument-parser jsonpickle jsonschema esprima browsermob-proxy selenium haralyzer psutil requests && python3.8 -m pip install --upgrade requests && python3.8 -m pip install --ignore-installed six
RUN python3.8 -m pip install aiohttp==3.7.2 argparse==1.4.0 asyncio==3.4.3 pathlib==1.0.1 psutil==5.7.3 \
    jsonschema==3.2.0 selenium==3.141.0 bs4==0.0.1 lxml==4.6.1 mock==4.0.2 browsermob-proxy==0.8.0 jsonpickle==1.4.1 \
    pyfiglet==0.7 termcolor==1.1.0 pytest==6.1.2 kids-cache==0.0.7 typed-argument-parser==1.6.1 typing==3.7.4.3 \
    pytest-asyncio==0.14.0 html5lib==1.1 esprima==4.0.1 haralyzer==1.9.0

RUN apt-get install -y openjdk-8-jdk

RUN mkdir /var/instr && chmod 777 /var/instr

CMD /usr/bin/supervisord
