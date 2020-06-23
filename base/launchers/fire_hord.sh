#! /bin/bash

if [[ -z ${1} ]]; then   
    echo "Must supply starting cores in first param, try again "
    exit 129
fi 

eval_ver=$2
if [[ -z ${2} ]]; then   
    echo "Must supply eval version for second param"
    exit 123
fi 

if [[ -z ${3} ]]; then
    appundertest="openemr"
else
    appundertest=$3
fi

mkdir -p /p/webcam/results24/$eval_ver
chmod 777 /p/webcam/results24/$eval_ver
sudo chown ubuntu:erik /p/webcam/results24/$eval_ver
core_start=$1;

filestart=$(echo $eval_ver|cut -d "_" -f3)

#sed "s/head -20/tail -n +${filestart} | head -n 20/g" -i /p/webcam/php7/tests/openemr/test_data.json

cd /p/webcam/php7/tests/${appundertest}
docker build -t webcam/php7/${appundertest} .

WC_LIST_TESTVERS=" WICHR EXWICHR ";  # EXWICH out for now
for nm in $(echo $WC_LIST_TESTVERS|tr ' ' '\n'); do 
    /p/webcam/docker/startdocker.sh php7 ${appundertest} $nm --cpu-start $core_start --results-at /p/webcam/results24/$eval_ver --skip-build ;
    core_start=$(( $core_start + 8 )); 
done  
     


 