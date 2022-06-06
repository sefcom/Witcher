<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "mysql";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

$sql = "SELECT user FROM user";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo $row['user']."\n";
}

echo "Ran Query\nExecuting broken query\n";

$sql = "SELECT user FROM user WHERE user = (;--";
$result = $conn->query($sql);

echo "Query Run\n";

while ($row = $result->fetch_assoc()) {
    echo $row['user']."\n";
}


echo "Page completed\n";

?>
