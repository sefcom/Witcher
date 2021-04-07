HTTP/1.1 200 OK

<?
include "/htdocs/phplib/xnode.php";

if($AUTHORIZED_GROUP < 0)
{
	echo '<html><body><div>Authetication Fail!</div></body></html>';
}
else
{
	include "/htdocs/webinc/version.php";
}
?>
