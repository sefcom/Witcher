Creating a statically linked httpreqr
First you need a statically compiled lib curl.

1. `git clone https://github.com/curl/curl.git`

2. `autoconf`

3. `autoreconf -i`  # if having problems with configure completing successfully

4. `./configure --prefix=/tmp/curl-static-only-install --with-mbedtls --without-ssl --without-libssh2 --disable-dict --disable-file --disable-ftp --disable-gopher --disable-imap --disable-ldap --disable-ldaps --disable-pop3 --disable-rtsp --disable-smtp --disable-telnet --disable-tftp --disable-verbose --disable-shared --enable-static --disable-manual`

5. make -j$(nproc) && make install

Second, incorporate that lib into httpreqr

1. `g++ -o httpreqr.64 main.cc -static $(PKG_CONFIG_PATH=/tmp/curl-static-only-install/lib/pkgconfig pkg-config libcurl --libs)`



