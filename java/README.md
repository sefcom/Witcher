# Witcher Java

#Build JDK
```
cd jdksrc
bash ./configure -disable-warnings-as-errors

CONF="linux-x86_64-normal-server-release" make JOBS=50 
```

## Testing in container
Create Start Tomcat Server alias:
```
alias run0='STRICT=1 LD_PRELOAD=/lib/libsqlcatch.so PORT=14000 JAVA_HOME=/jdk SHOW_WITCH=1 JDK_JAVA_OPTIONS=-XX:+WitcherInstrumentation /opt/tomcat/port_14000/bin/catalina.sh run'
```

Send URL to server using AFL's showmap
```
time (/httpreqr/httpreqr --initmemory && printf "\x00b=/++\x00" | /p/afl/afl-showmap -m 1G -o /tmp/mapfile /p/Witcher/base/httpreqr/httpreqr --url http://localhost:14000/JavaWitcherHello/ServeMe) && pkill -f port_14000; cp /tmp/mapfile  map5.log
```

Run Witcher using /p 
```
rm -fr /p/Witcher/java/tests/WICHR/; python -m witcher /p/Witcher/java/tests/ WICHR -t 10000
```

#httpreqr
```
cd /p/Witcher/base/httpreqr
g++ main.cc -g -o httpreqr -lcurl

```

#sqlcatch library
```
cd /p/Witcher/base/wclibs 

gcc -c -Wall -fpic sqlcatch.c && gcc -shared -o libsqlcatch.so sqlcatch.o -ldl && sudo cp libsqlcatch.so /wclibs/libsqlcatch.so && sudo cp libsqlcatch.so  /lib/libsqlcatch.so && echo 'SUCCESS!!'

```

https://en.wikipedia.org/wiki/Java_bytecode_instruction_listings