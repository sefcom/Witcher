<?php
// requires - full path required
require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
require("/home/rconfig/classes/reportTemplate.class.php");
require_once("/home/rconfig/config/config.inc.php");
require_once("/home/rconfig/config/functions.inc.php");
// declare DB Class
$db2 = new db2();
//setup backend scripts Class
$backendScripts = new backendScripts($db2);
// get & set time for the script
$backendScripts->getTime();
// declare Logging Class
$log = ADLog::getInstance();
$log->logDir = $config_app_basedir . "logs/";
// script startTime and use extract to convert keys into variables for the script
extract($backendScripts->startTime());
// get ID from argv input
/// if statement to check first argument in phpcli script - otherwise the script will not run under phpcli - similar to PHP getopt()
// script will exit with Error if not TID is sent
if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
    // Get/Set Task ID - as sent from cronjob when this script is called and is stored in DB.nodes table also
    $tid = $_GET['id']; // set the Task ID
} else {
    echo $backendScripts->errorId($log, 'Task ID');
}

// get task details from DB
$db2->query("SELECT * FROM tasks WHERE id = :tid AND status = '1'");
$db2->bind(':tid', $tid);
$taskRow = $db2->resultset();
$command = str_replace(' ', '', $taskRow[0]['catCommand']);
$taskname = $taskRow[0]['taskname'];
$rid = $taskRow[0]['complianceId'];
$complianceReportId = $taskRow[0]['complianceId'];
// create connection report file
$reportFilename = 'complianceReport' . $date . '.html';
$reportDirectory = 'complianceReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$reportFail = '<font color="red">Fail</font>';
$reportPass = '<font color="green">Success</font>';
// get base_encoded images for checkmarks later on
$greenCheck = file_get_contents('/home/rconfig/www/images/tick_32.png.base');
$redCross = file_get_contents('/home/rconfig/www/images/redCross.png.base');
// get polices for given $rid from DB
$policies = array();
$db2->query("SELECT polId FROM complianceReportPolTbl WHERE reportId = :rid");
$db2->bind(':rid', $rid);
$policyResult = $db2->resultset();
foreach ($policyResult as $row) {
    $policies[$row['polId']] = $row['polId'];
}
// Get active nodes for a given task ID
// Query to retireve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1");
$getNodesSql = $db2->resultset();
if (!empty($getNodesSql)) {
    // push rows to $devices array
    $devices = array();
    foreach ($getNodesSql as $row) {
        array_push($devices, $row);
    }
    // loop over retrieved devices
    foreach ($devices as $device) {
        $deviceId = $device['id'];
        $db2->query("SELECT * FROM configs WHERE deviceId = $deviceId AND configFilename LIKE '%$command%' ORDER BY configDate DESC LIMIT 1");
        $db2->bind(':deviceId', $deviceId);
        $pathResultLatest = $db2->resultset();
        // append device name to report		
        $report->eachComplianceDataRowDeviceName($device['deviceName']); // log deviceName to report
        // continue for the foreach if one of the files is not available as this compliance will be invalid		
        if (empty($pathResultLatest)) {
            echo 'continue invoked for ' . $device['deviceName'];
            continue;
        }
        $pathResult_a = $pathResultLatest[0]['configLocation'];
        $filenameResult_a = $pathResultLatest[0]['configFilename'];
        $configFile = $pathResult_a . '/' . $filenameResult_a;
        $tableRow = ""; // set tableRow for later use and avoid  Undefined variable errors
        foreach ($policies as $k => $v) {
            $db2->query("SELECT e.elemId, cpe.elementName, cpe.singleParam1, cpe.singleLine1
                            FROM compliancePolElemTbl as e
                            LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
                            WHERE polId = $v
                            ORDER BY elementName ASC");
            $elemsRes = $db2->resultset();
            // get policynames for report output
            $db2->query("SELECT policyName FROM compliancePolicies WHERE id = :v");
            $db2->bind(':v', $v);
            $policyNameRes = $db2->resultset();
            foreach ($policyNameRes as $row) {
                $policyName = $row['policyName'];
            }
            // print policy name header to report
            $report->eachComplianceDataRowPolicyName($policyName, $configFile);
            foreach ($elemsRes as $row) {
                $elems[] = array(
                    'elemId' => $row['elemId'],
                    'elementName' => $row['elementName'],
                    'singleParam1' => $row['singleParam1'],
                    'singleLine1' => $row['singleLine1']
                );
            }
            // file to array
            $fileArr = file($configFile);
            foreach ($elems as $kElem => $vElem) {
                $string = $vElem['singleLine1'];
                // set pattern Based on selected parameter where 
                // 1 = equals
                // 2 = contains

                if ($vElem['singleParam1'] == 1) {
                    $pattern = "/^$string\$/m";
                    $symbol = '='; // not used yet
                } else if ($vElem['singleParam1'] == 2) {
                    $pattern = "/.*$string.*/i";
                    $symbol = '~'; // not used yet
                }
                foreach ($fileArr as $k => $line) {
                    if (regexpMatch($pattern, $line) == 1) {
                        $result = 1;
                        $tableRow .= "
                                        <tr class=\"even indentRow\" style=\"float:left; width:800px;\">
                                                <td>yes
                                                </td>	
                                                <td> - " . $vElem['elementName'] . "</td>							
                                        </tr>
                                        ";
                        break; // Check if result = 1 (next to check this becuase of possible multiline match failing) break the foreach for first match
                    } else {
                        $result = 0;
                    }
                }
                // if not matched the output in the negative
                if ($result !== 1) {
                    $tableRow .= "
                                    <tr class=\"even indentRow\" style=\"float:left; width:800px;\">
                                            <td>no
                                            </td>
                                            <td> - " . $vElem['elementName'] . "</td>							
                                    </tr>
                                    ";
                }
            }
            // unset elems array for next iteration
            unset($elems);
            // send data output to the report
            $report->eachComplianceData($tableRow);
            // unset tableRow data for next iteration
            $tableRow = "";
        }
        // close table row tags	
        $report->endComplianceData();
    } // END - // loop over retrieved devices
    // script endTime
    extract($backendScripts->endTime($time_start));
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    $report->footer();
    // Check if mailConnectionReport value is set to 1 and send email
    if ($taskRow[0]['mailConnectionReport'] == '1') {
        $backendScripts->reportMailer($db2, $log, $title, $config_reports_basedir, $reportDirectory, $reportFilename, $taskname);
    }
} else {
    echo $backendScripts->finalAlert($log, $_SERVER['PHP_SELF']);
}