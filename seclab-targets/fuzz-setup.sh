#! /bin/bash
set -e 

uid=$(id -u)
if [[ $uid -eq 0 ]]; then
   echo "should run as wc not root"
   exit 99
fi
cd /p/Witcher/base/

# current version of Widash is breaking patchelf-wrapper install, for now, just copying after the fact.
pip install archr

cd phuzzer
pip install -e .
cd ../witcher
pip install -e .

cd ..
sudo cp /p/Witcher/base/wclibs/libcgiwrapper.so /wclibs
sudo cp /p/Widash/archbuilds/dash.64 /bin/dash




