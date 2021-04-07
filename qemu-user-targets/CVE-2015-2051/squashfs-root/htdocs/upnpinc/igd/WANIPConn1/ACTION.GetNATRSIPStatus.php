<NewRSIPAvailable>0</NewRSIPAvailable>
<NewNATEnabled><?
if (query("/runtime/device/layout")=="router") echo "1";
else echo "0";
?></NewNATEnabled>
