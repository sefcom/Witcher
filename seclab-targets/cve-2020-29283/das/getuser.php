<?php

include_once 'assets/conn/dbconnect.php';
$q = $_GET['q'];
// echo $q;
$res = mysqli_query($con,"SELECT * FROM doctorschedule WHERE scheduleDate='$q'");



if (!$res) {
die("Error running $sql: " . mysqli_error());
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <!-- <link href="assets/css/bootstrap.min.css" rel="stylesheet"> -->
    
</head>
<body>
     <?php 

        if (mysqli_num_rows($res)==0) {

            echo "<div class='alert alert-danger' role='alert'>Doctor is not available at the moment. Please try again later.</div>";
                
            } else {
             echo "   <table class='table table-hover'>";
        echo " <thead>";
            echo " <tr>";
                echo " <th>Day</th>";
                echo " <th>Date</th>";
               echo "  <th>Start</th>";
               echo "  <th>End</th>";
                echo " <th>Availability</th>";
            echo " </tr>";
       echo "  </thead>";
       echo "  <tbody>";

         while($row = mysqli_fetch_array($res)) { 

            ?>

            <tr>
                <?php

                // $avail=null;
                if ($row['bookAvail']!='available') {
                $avail="danger";
                } else {
                $avail="primary";
                
            }
                echo "<td>" . $row['scheduleDay'] . "</td>";
                echo "<td>" . $row['scheduleDate'] . "</td>";
                echo "<td>" . $row['startTime'] . "</td>";
                echo "<td>" . $row['endTime'] . "</td>";
                echo "<td> <span class='label label-".$avail."'>". $row['bookAvail'] ."</span></td>";
                ?>
            </tr>
        <?php
    }
}
    ?>
        </tbody>
    </body>
</html>