#!/bin/sh
echo "In $0" > /dev/console

model=`xmldbc -g /runtime/device/modelname`
hw="`xmldbc -g /runtime/devdata/hwver | sed 's/[^a-zA-Z]//g' | tr '[a-z]' '[A-Z]' | cut -c 1`x"
mac=`xmldbc -g /runtime/devdata/lanmac`
msg=""

fwcheckparameter="`xmldbc -g /device/fwcheckparameter`"
if [ "$fwcheckparameter" != "" ]; then
region=$fwcheckparameter
else
region="Default"
fi

mac=`echo $mac | cut -f1 -d":"``echo $mac | cut -f2 -d":"``echo $mac | cut -f3 -d":"``echo $mac | cut -f4 -d":"``echo $mac | cut -f5 -d":"``echo $mac | cut -f6 -d":"`

# echo "hw=$hw"
# echo "mac=$mac"

# Generate the contents that is going to POST to dlink firmware server.
tmp_file="/var/fwnotify.txt"
rm $tmp_file 2> /dev/null

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>" >> $tmp_file
echo "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"  xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope\">" >> $tmp_file
echo "<soap:Body>" >> $tmp_file
echo "<funPushNotification xmlns=\"http://wrpd.dlink.com/\">" >> $tmp_file

echo "<strModel>$model</strModel>" >> $tmp_file
echo "<strHW>$hw</strHW>" >> $tmp_file
echo "<strRegion>$region</strRegion>" >> $tmp_file
echo "<strMac>$mac</strMac>" >> $tmp_file
echo "<strMsg>$msg</strMsg>" >> $tmp_file

echo "</funPushNotification>" >> $tmp_file
echo "</soap:Body>" >> $tmp_file
echo "</soap:Envelope>" >> $tmp_file

post_url="http://wrpd.dlink.com/router/firmware/WebService.aspx";
urlget10 post $post_url $tmp_file  > /dev/null

rm $tmp_file

exit 0
