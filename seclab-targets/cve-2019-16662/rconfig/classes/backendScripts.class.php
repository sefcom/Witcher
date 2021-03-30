<?php
/**
 * This class serves to reduce repeating code in the rconfig/lib/* scripts folder
 * @author stephen stack
 */

class backendScripts {
    protected $conn;
    
    // setup construct with db2 passed from script 
    public function __construct($db2){
         $this->conn = $db2;   
    }
    
    // get & set time
    public function getTime(){
        $this->conn->query("SELECT timeZone FROM settings");
        $result = $this->conn->resultsetCols();
        $timeZone = $result[0];
        return date_default_timezone_set($timeZone);
    }
    
    public function startTime(){
        // script startTime
        $startTime = date('h:i:s A');
        $date = date('Ymd');
        $time_start = microtime(true);
        return array('startTime' => $startTime, 'date' => $date, 'time_start' => $time_start);
    }
    
    public function endTime($time_start){
        // script startTime
        $endTime = date('h:i:s A');
        $time_end = microtime(true);
        $time = round($time_end - $time_start) . " Seconds";
        return array('endTime' => $endTime, 'time_end' => $time_end, 'time' => $time);
    }

    public function errorId($log, $errorType){
        $errorText = $errorType." not Set - unable to run script";
        $error = $errorText . "\n";
        $log->Fatal("Error: " . $errorText . " (File: " . $_SERVER['PHP_SELF'] . ")");
        return $error;
        die();
    } 
    
    public function debugOnOff($db2, $argv){
        // get debugging switch from DB - 1 = on, 0 = off
        $db2->query("SELECT commandDebug, commandDebugLocation FROM settings");
        $debugRes = $db2->resultset();
        if ($argv == true) {
            $debugOutputArray = true;
        } else {
            $debugOutputArray = false;
        }
        return array('debugOnOff'=>$debugRes[0]['commandDebug'], 'debugPath'=>$debugRes[0]['commandDebugLocation'], 'cliDebugOutput' => false, 'cliDebugOutput' => $debugOutputArray);
    }
    
    public function invokationCheck($log, $tid, $php_sapi_name, $serverTerm=null, $pageName){
        // set $resetPerms to 0 as this will be be used later to NOT reset permissions on /home/rconfig/*
        $resetPerms = 0;
        if ($php_sapi_name == 'cli') {
            // check if script was run manually using the $_SERVER['TERM'] global
            if ($serverTerm != NULL) {
                $log->Info("The " . $pageName . " script was run from a manual invocation on a shell with Task ID:" . $tid . ""); // logg to file
                $alert = "The " . $pageName . " script was run from a manual invocation on a shell with Task ID:" . $tid . "\r\n";
                // set this var so that later we can reset permissions on the /home/rconfig/data dir. Running the script from a shell causes perms to be set as root otherwise
                $resetPerms = 1;
            } else {
                $log->Info("The " . $pageName . " script was run from crontab ID:" . $tid . ""); // logg to file
                $alert =  "The script was run from the crontab entry with Task ID:" . $tid . "\r\n";
            }
        } else {
            $log->Info("The " . $pageName . " script was run from a webserver, or something else"); // logg to file
            $alert =  "The script was run from a webserver, or something else with Task ID:" . $tid . "\r\n";
        }
        return array('resetPerms'=>$resetPerms, 'alert'=>$alert);
    }

    public function resetPerms($log, $resetPerms=0){
        if ($resetPerms = 1) {
            $dataDir = '/home/rconfig/data/';
            shell_exec('chown -R apache ' . $dataDir);
            $log->Info("The owner permisions for directory " . $dataDir . " were reset to owner apache because script was run interactively"); // log to file
            $resetAlert = "The owner permisions for directory " . $dataDir . " were reset to owner apache because script was run interactively\r\n";
            return array('resetAlert'=>$resetAlert);
        }
    }

    public function reportMailer($db2, $log, $title, $config_reports_basedir, $reportDirectory, $reportFilename, $taskname){
        require("/home/rconfig/classes/phpmailer/class.phpmailer.php");
        $db2->query("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");
        $resultSelSmtp = $db2->resultset();
        $smtpServerAddr = $resultSelSmtp[0]['smtpServerAddr'];
        $smtpFromAddr = $resultSelSmtp[0]['smtpFromAddr'];
        $smtpRecipientAddr = $resultSelSmtp[0]['smtpRecipientAddr'];
        if ($resultSelSmtp[0]['smtpAuth'] == 1) {
            $smtpAuth = $resultSelSmtp[0]['smtpAuth'];
            $smtpAuthUser = $resultSelSmtp[0]['smtpAuthUser'];
            $smtpAuthPass = $resultSelSmtp[0]['smtpAuthPass'];
        }
        $mail = new PHPMailer();
        $report = $config_reports_basedir . $reportDirectory . "/" . $reportFilename;
        $body = file_get_contents($report);
        $mail->IsSMTP(); // telling the class to use SMTP
        if ($resultSelSmtp[0]['smtpAuth'] == 1) {
            $mail->SMTPAuth = true; // enable SMTP authentication
            $mail->Username = $smtpAuthUser; // SMTP account username	
            $mail->Password = $smtpAuthPass; // SMTP account password
        }
        $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
        $mail->Host = $smtpServerAddr; // sets the SMTP server
        $mail->Port = 25; // set the SMTP port for the GMAIL server
        $mail->SetFrom($smtpFromAddr, $smtpFromAddr);
        $mail->Subject = "rConfig Report - " . $taskname;
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($body);
        $smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);
        foreach ($smtpRecipientAddresses as $emailAddr) {
            $mail->AddAddress($emailAddr);
        }
        if (!$mail->Send()) {
            $log->Fatal('Fatal: ' . $title . ' Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
        } else {
            $log->Info('Info: ' . $title . ' Email Report sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
        }
        // Clear all addresses and attachments for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();
    }     
    
    public function finalAlert($log, $pageName) {
        $text = "Failure: Unable to get Device information from Database Command (File: " . $pageName;
        $log->Fatal($text);
        return $text;
    }
}
