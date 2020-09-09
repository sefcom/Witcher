<?php
include('./config.php');
// nv == necessary value to reach vul
// vul == vulnerable variable

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Testing Trace</title>
	</head>
	<body>
	<?php
        echo "Starting EVAL <BR>\n";
        if($_GET['nv1'] == "YYYY") {
                $ret=mysqli_query($con,"select * from user where id='");
                echo "<BR>MADE it to query with `". $vul1 . "`\n<BR>";
        }  else {
            echo "<BR>NO Match nv1 \n<BR>";
        }
    ?>
	</body>
</html>
