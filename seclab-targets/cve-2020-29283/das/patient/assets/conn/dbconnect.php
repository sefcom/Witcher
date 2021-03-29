<?php
if(!mysql_connect("localhost","das","das"))
{
     die('oops connection problem ! --> '.mysql_error());
}
if(!mysql_select_db("das"))
{
     die('oops database selection problem ! --> '.mysql_error());
}
?>
