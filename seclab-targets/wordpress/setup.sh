#! /bin/bash

pip install archr ipdb

cd /p/Witcher/base/witcher
pip install -e .
cd ../phuzzer
pip install -e .


cd /phpsrc/ext/
rm -f xdebug

git clone git://github.com/xdebug/xdebug.git

cd xdebug
sudo phpize
sudo ./configure --enable-xdebug
sudo make -j $(nproc)
sudo make install 

phpini_fpath=$(php -i |egrep "Loaded Configuration File.*php.ini"|cut -d ">" -f2|cut -d " " -f2)

sudo su -c "printf '\nzend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/xdebug.so\nauto_prepend_file=/enable_cc.php\n\n' >> $phpini_fpath"

mv /httpreqr /httpreqr_dir
sudo cp /p/Witcher/base/httpreqr/httpreqr.64 /httpreqr
sudo cp /p/Widash/archbuilds/dash /bin/dash 

echo "RUN >>> python -m witcher $(pwd) WICHR ; "



