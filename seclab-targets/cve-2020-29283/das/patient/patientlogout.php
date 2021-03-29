<?php
session_start();

if(!isset($_SESSION['patientSession']))
{
 header("Location: patientdashboard.php");
}
else if(isset($_SESSION['patientSession'])!="")
{
 header("Location: ../index.php");
}

if(isset($_GET['logout']))
{
 session_destroy();
 unset($_SESSION['patientSession']);
 header("Location: ../index.php");
}
?>