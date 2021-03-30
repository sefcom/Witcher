<?php
require_once("/home/rconfig/config/config.inc.php");
require_once("/home/rconfig/config/functions.inc.php");
require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
// SEE BOTTOM OF SCRIPT FOR CORE FUNCTIONALITY
    
    /**
     * procDebugOnOff - Change the debug value in the settings table to 1 or 0 to turn
      device output debugging to on or off respectively
     */
    function procDebugOnOff() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if ($_GET['debugOnOff'] == '1') {
            $status = "On";
            $debugOnOff = $_GET['debugOnOff'];
        } else {
            $status = "Off";
            $debugOnOff = 0;
        }

        $db2->query("UPDATE `settings` SET `commandDebug` = :debugOnOff");
        $db2->bind(':debugOnOff', $debugOnOff);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<font color='red'>Debugging status changed successfully to " . $status . "</font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update debugSetting in DB for ajaxSettingsProcess.php:" . $queryResult);
        }
        echo json_encode($response);
    }

    /**
     * phpLoggingOnOff - Change the php logging value in the settings tbl to 1 or 0 to turn
      php logging to on or off respectively
     */
    function phpLoggingOnOff() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");

        $db2 = new db2();
        $log = ADLog::getInstance();

        if ($_GET['phpLoggingOnOff'] == '1') {
            $status = "On";
            $phpLoggingOnOff = $_GET['phpLoggingOnOff'];
        } else {
            $status = "Off";
            $phpLoggingOnOff = 0;
        }

        $db2->query("UPDATE `settings` SET `phpErrorLogging` = :phpLoggingOnOff");
        $db2->bind(':phpLoggingOnOff', $phpLoggingOnOff);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<font color='red'>PHP Error Logging status changed successfully to " . $status . "</font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update phpErrorLogging in DB for ajaxSettingsProcess.php:" . $queryResult);
        }
        echo json_encode($response);
    }

    /**
     * procDeviceTimeout - Change the device connection timeout value
     */
    function procDeviceTimeout() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['deviceToutVal'])) {
            $timeout = $_GET['deviceToutVal'];
        }
        $db2->query("UPDATE `settings` SET `deviceConnectionTimout` = :deviceToutVal");
        $db2->bind(':deviceToutVal', $timeout);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Device Connection Timeout changed successfully to " . $timeout . " Seconds</font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update deviceConnectionTimout in DB for ajaxSettingsProcess.php:" . $queryResult);
        }
        echo json_encode($response);
    }

    /**
     * procPageTimeout - Change the webpage timeout value
     */
    function procPageTimeout() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['pageTimeoutVal'])) {
            $timeout = $_GET['pageTimeoutVal'];
        }
        $db2->query("UPDATE `settings` SET `pageTimeout` = :pageTimeoutVal");
        $db2->bind(':pageTimeoutVal', $timeout);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Webpage Timeout changed successfully to " . $timeout . " Seconds</font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update procPageTimeout in DB for ajaxSettingsProcess.php:" . $queryResult);
        }
        echo json_encode($response);
    }

    /**
     * procTimeZoneChange - Change the server timezone
     */
    function procTimeZoneChange() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");

        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['timeZoneChange'])) {
            $timeZone = $_GET['timeZoneChange'];
        }

        $db2->query("UPDATE `settings` SET `timeZone` = :timeZone");
        $db2->bind(':timeZone', $timeZone);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Timezone changed successfully to " . $timeZone . "</font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update Timezone in DB for ajaxSettingsProcess.php:" . $queryResult);
        }

        echo json_encode($response);
    }

    
    /**
     * enableLDAPAuth - Enable LDAP Authentication & pass settings
     */
    function enableLDAPAuth() {

        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");

        $db2 = new db2();
        $log = ADLog::getInstance();

        $enableLDAPAuth = $_POST['enableLDAPAuth'];
        $ldap_host = $_POST['ldap_host'];
        $ldap_dn = $_POST['ldap_dn'];
        $ldap_user_group = $_POST['ldap_user_group'];
        $ldap_admin_group = $_POST['ldap_admin_group'];
        $ldap_usr_dom = $_POST['ldap_usr_dom'];

        $db2->query("UPDATE `settings` SET `ldapServer` = :ldapServer, `ldap_host` = :ldap_host, `ldap_dn` = :ldap_dn, `ldap_user_group` = :ldap_user_group, `ldap_admin_group` = :ldap_admin_group, `ldap_usr_dom` = :ldap_usr_dom");
        $db2->bind(':ldapServer', $enableLDAPAuth);
        $db2->bind(':ldap_host', $ldap_host);
        $db2->bind(':ldap_dn', $ldap_dn);
        $db2->bind(':ldap_user_group', $ldap_user_group);
        $db2->bind(':ldap_admin_group', $ldap_admin_group);
        $db2->bind(':ldap_usr_dom', $ldap_usr_dom);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>LDAP Settings Updated </font>";
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update LDAP in DB for ajaxSettingsProcess.php:" . $queryResult);
        }

        echo json_encode($response);
    }

    
    /**
     * getDebugStatus - Change the device debug status
     */
    function getDebugStatus() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");

        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['getDebugStatus'])) {
            $db2->query("SELECT commandDebug FROM settings");
            $result = $db2->single();
            $status = $result['commandDebug'];
            /* Update successful */
            if ($status == '1') {
                $response = "<font color='red'><strong>Debugging Status: </strong>On<br/></font>";
            }
            /* Update failed */ else if ($status == '0') {
                $response = "<font color='green'><strong>Debugging Status: </strong>Off</font>";
            }

            echo json_encode($response);
        }
    }

    /**
     * getTimeZoneStatus - Change the device connection timeout value
     */
    function getTimeZone() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");

        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['getTimeZone'])) {
            $db2->query("SELECT timeZone FROM settings");
            $result = $db2->single();
            $timeZone = $result['timeZone'];
            /* Update successful */
            if (!empty($timeZone)) {
                $response = $timeZone;
            }
            /* Update failed */ else if (empty($timeZone)) {
                $response = "";
            }
            echo json_encode($response);
        }
    }

    /**
     * procDeviceTimeout - Change the device connection timeout value
     */
    function getPhpLoggingStatus() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['getPhpLoggingStatus'])) {
            $db2->query("SELECT phpErrorLogging FROM settings");
            $result = $db2->single();
            $status = $result['phpErrorLogging'];
            /* Update successful */
            if ($status == '1') {
                $response = "<font color='red'><strong>PHP Error Logging Status: </strong>On<br/></font>";
            }
            /* Update failed */ else if ($status == '0') {
                $response = "<font color='green'><strong>PHP Error Logging Status: </strong>Off</font>";
            }

            echo json_encode($response);
        }
    }

    /**
     * procDefaultCredsManualSet - Change the status for using the default credential set when manually uploading/downloading configs to/from devices
     */
    function procDefaultCredsManualSet() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if ($_GET['defaultCredsManualSet'] == '1') {
            $status = "enabled";
            $defaultCredsManualSet = $_GET['defaultCredsManualSet'];
        } else {
            $status = "disabled";
            $defaultCredsManualSet = 0;
        }
        $db2->query("UPDATE `settings` SET `useDefaultCredsManualSet` = :defaultCredsManualSet");
        $db2->bind(':defaultCredsManualSet', $defaultCredsManualSet);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            if ($_GET['defaultCredsManualSet'] == '1') {
                $response = "<font color='red'>Default credentials are disabled and individual users will have to input their credentials for manual config uploads & downloads</font>";
            } else {
                $response = "<font color='red'>Default credentials are enabled and will be used for manual config uploads & downloads</font>";
            }
        }
        /* Update failed */ else {
            $response = "failed";
            $log->Warn("Failure: Could not update useDefaultCredsManualSet in DB for ajaxSettingsProcess.php:" . $queryResult);
        }
        echo json_encode($response);
    }

    /**
     * getDefaultCredsManualSet - Get value set for using default credentials with manual uploads & downloads
     */
    function getDefaultCredsManualSet() {
        
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();

        if (isset($_GET['getDefaultCredsManualSet'])) {
            $db2->query("SELECT useDefaultCredsManualSet FROM settings");
            $result = $db2->single();
            $useDefaultCredsManualSet = $result['useDefaultCredsManualSet'];
            /* Update successful */
            $response = $useDefaultCredsManualSet;
            echo json_encode($response);
        }
    }
    /* User adjust debugging Option */
    if (isset($_GET['debugOnOff'])) {
        procDebugOnOff();
    } else if (isset($_GET['deviceToutVal'])) {
        procDeviceTimeout();
    } else if (isset($_GET['pageTimeoutVal'])) {
        procPageTimeout();
    } else if (isset($_GET['timeZoneChange'])) {
        procTimeZoneChange();
    } else if (isset($_GET['getTimeZone'])) {
        getTimeZone();
    } else if (isset($_GET['enableLDAPAuth'])) {
        enableLDAPAuth();
    } else if (isset($_GET['getDebugStatus'])) {
        getDebugStatus();
    } else if (isset($_GET['phpLoggingOnOff'])) {
        phpLoggingOnOff();
    } else if (isset($_GET['getPhpLoggingStatus'])) {
        getPhpLoggingStatus();
    } else if (isset($_GET['defaultCredsManualSet'])) {
        procDefaultCredsManualSet();
    } else if (isset($_GET['getDefaultCredsManualSet'])) {
        getDefaultCredsManualSet();
    }
    
}