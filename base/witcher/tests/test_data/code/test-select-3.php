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
	    // needs to keep nv1; add nv2 and nv3; and keep vul1
	    echo "Starting EVAL <BR>\n";
	    if($_GET['sel1'] == "s-val1a") {
		    if($_GET['sel2'] == "s-val2b") {
		        if($_GET['sel3'] == "s-val3c") {
                    $vul1=$_GET['vul1'];
                    $ret=mysqli_query($con,"select * from user where id=0'");
                    echo "<BR>MADE it to query with `". $vul1 . "`\n<BR>";
                } else {
                    echo "<BR>NO Match sel1\n<BR>";
                }
            } else {
                echo "<BR>NO Match sel2\n<BR>";
            }
        }  else {
            echo "<BR>NO Match sel3\n<BR>";
        }
    ?>
	</body>
</html>
