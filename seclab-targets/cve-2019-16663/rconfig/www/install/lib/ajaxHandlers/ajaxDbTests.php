<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

// various DB checks during the install process
if(isset($_POST['server'])){$server = $_POST['server'];}
if(isset($_POST['port'])){$port = $_POST['port'];}
if(isset($_POST['dbName'])){$dbName = $_POST['dbName'];}
if(isset($_POST['dbUsername'])){$dbUsername = $_POST['dbUsername'];}
if(isset($_POST['dbPassword'])){$dbPassword = $_POST['dbPassword'];}

$sqlHost = $server . ":" . $port;
$array = array();

// chech server connectivity
$handle = fsockopen($server, $port);

if ($handle) {
    $array['connTest'] = '<strong><font class="good">Pass</strong> </font>';
} else {
    $array['connTest'] = '<strong><font class="bad">Fail - Cannot connect to ' . $server . ':' . $port . '</strong></font>';
}
fclose($handle);

// check Username/Password 
try {
        $conn = new PDO("mysql:host=$server", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAME'utf8'");
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link = true; 
    }
catch(PDOException $e)
    {
        $link = false; 
    }
if ($link) {
    $array['credTest'] = '<strong><font class="good">Pass</strong></font>';
} else {
    $array['credTest'] = '<strong><font class="bad">Fail -  Could not connect to Database Server. Check your settings!</strong></font>';
}

//check if DB exists
if(isset($dbName)){
    $dsn = 'mysql:host=' . $server . ';dbname=' . $dbName . ';port=' . $port;
    // Set options
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    //Create a new PDO instance
    try {
        $conn = new PDO($dsn, $dbUsername, $dbPassword, $options);
		$stmt = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$dbName."'");
		$db_selected = $stmt->fetchColumn();

    }
    // Catch any errors
    catch (PDOException $e) {
        $sqlError = $e->getMessage();

    }    

	if ($db_selected == 1) {
		$array['dbTest'] = '<strong><font class="bad">Fail - Database already installed</strong></font>';
	} elseif ($db_selected == 0) {
		$array['dbTest'] = '<strong><font class="good">Pass - '.$dbName.' not in use</strong></font>';
	}
   
} else {
    $array['dbTest'] = '<strong><font class="bad">Fail - Database Name was not entered</strong></font>';
}
if($sqlError && $e->getCode() != '1049') {// here we expect the Count query above to fail, as a zero value should be returned. But we still want other errors to appear if needed. 
	$array['dbTest'] = '<strong><font class="bad">Fail - '.$sqlError.'</strong></font>';	
}
$conn = null;
echo json_encode($array);