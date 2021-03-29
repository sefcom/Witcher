<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_healthcaare_conn = "localhost";
$database_healthcaare_conn = "das";
$username_healthcaare_conn = "das";
$password_healthcaare_conn = "das";
$healthcaare_conn = mysql_pconnect($hostname_healthcaare_conn, $username_healthcaare_conn, $password_healthcaare_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
?>
