<?php
$configFilePathInstalled = '/home/rconfig/config/config.inc.php';
include($configFilePathInstalled);
include("/home/rconfig/classes/db2.class.php");
$array = array();

/* config.inc.php  file read check */
if (defined('DB_HOST') && defined('DB_USER')) {
    $array['configFileMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
} else {
    $array['configFileMsg'] = '<strong><font class="bad">Fail - Could not read config.inc.php</strong></font><br/>';
}

/* DB checks */
// now invoking DB2 class, beacuse if this works, then the DB, and config file is fully installed
$db2 = new db2();

if ($db2) {
    $array['dbReadMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';

    $db2->query("INSERT INTO categories (categoryName, status) VALUES ('testCat', 2)");
    $resultInsert = $db2->execute();
    if (!$resultInsert) {
        $array['dbWriteMsg'] = '<strong><font class="bad">Fail - Could not insert into Database </strong></font><br/>';
    }
    $db2->query("SELECT * FROM categories");
    $resultSelect = $db2->resultset();
    foreach ($resultSelect as $row) {
        if ($row['categoryName'] == 'testCat') {
            $dbWriteTest = 1;
        }
    }
    if ($dbWriteTest == 1) {
        $array['dbWriteMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
    } else {
        $array['dbWriteMsg'] = '<strong><font class="Bad">Could not write to Database</strong></font><br/>';
    }
    $db2->query("DELETE FROM categories WHERE categoryName = 'testCat'");
    $resultDelete = $db2->execute();
    if (!$resultDelete) {
        $array['dbWriteMsg'] = '<strong><font class="bad">Fail - Could not read from Database </strong></font><br/>';
    }
} else {
    $array['dbReadMsg'] = '<strong><font class="bad">Fail - Could not access Database </strong></font><br/>';
}

/* rConfig Application Directory file checks */

function dirRW($directory, $msgTitle) {
    $fileName = $directory . "testFile.txt";
    $text = 'someRandomText';
    $funcArr = array();
    if ($fileHandle = fopen($fileName, 'w')) {
        file_put_contents($fileName, $text);
        $funcArr[$msgTitle . 'FileWriteMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
    } else {
        $funcArr[$msgTitle . 'FileWriteMsg'] = '<strong><font class="bad">Fail - Could not write a file</strong></font><br/>';
    }
    if (file_get_contents($fileName) == $text) {
        $funcArr[$msgTitle . 'FileReadMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
    } else {
        $funcArr[$msgTitle . 'FileReadMsg'] = '<strong><font class="bad">Fail - Could not read from file ' . $fileName . '</strong></font><br/>';
    }
    fclose($fileHandle);
    unlink($fileName);
    return $funcArr;
}

foreach (dirRW($config_data_basedir, 'app') as $k => $v) {
    $array[$k] = $v;
}
foreach (dirRW($config_backup_basedir, 'backup') as $k => $v) {
    $array[$k] = $v;
}
foreach (dirRW($config_temp_dir, 'tmp') as $k => $v) {
    $array[$k] = $v;
}
echo json_encode($array);
