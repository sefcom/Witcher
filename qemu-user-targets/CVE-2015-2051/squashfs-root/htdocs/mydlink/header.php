<?
if($AUTHORIZED_GROUP>=0)
{
	echo "HTTP/1.1 200 OK\n";
	echo "Content-Type: text/html; charset=utf-8\n";
}
else
{
	exit;
}
?>
