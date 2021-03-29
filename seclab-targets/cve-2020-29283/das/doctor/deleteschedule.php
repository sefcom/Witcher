<?php
include_once '../assets/conn/dbconnect.php';
// Get the variables.
$id = $_POST['id'];
// echo $appid;

$delete = mysqli_query($con,"DELETE FROM doctorschedule WHERE scheduleId=$id");
// if(isset($delete)) {
//    echo "YES";
// } else {
//    echo "NO";
// }



?>

