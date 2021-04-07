<?
include "/htdocs/phplib/xnode.php";
$def_file = "/var/default_wifi.xml";

$image_sign = fread("", "/etc/config/image_sign");
$image_sign = strip($image_sign);

?>

<<?=$image_sign?>>
	<runtime>
		<default>
<?	
	foreach("/phyinf")
	{
		if (query("type")== "wifi")
		{
			echo '\t\t\t<phyinf>\n';
			echo dump(4, "/phyinf:".$InDeX);
			echo '\t\t\t</phyinf>\n';
		}
	}
?>

			<wifi>
<? echo dump(4, "/wifi");?>
			</wifi>
		</default> 
	</runtime>

</<?=$image_sign?>> 
