HTTP/1.1 200 OK
Content-Type: text/html; charset=utf-8
<?
include "/htdocs/phplib/xnode.php";
include "/htdocs/webinc/config.php";
?>
<? 
echo "model=".query("/runtime/device/modelname")."\n";
echo "version=".query("/runtime/device/firmwareversion")."\n";
$mac=query("/runtime/devdata/lanmac");
echo "macaddr=".toupper($mac)."\n";
?>
