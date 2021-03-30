<?php

// php 5.1.0 date(): Now issues the E_STRICT and E_NOTICE time zone errors.
date_default_timezone_set('Europe/Dublin');

/**
 * Logging class for loglevels
 *
 * This class is for simple logging
 * with custom logformat and different loglevels
 * supports logfile rotation with maxFileSize and maxHistoryFiles
 *
 * changelog:
 * 1.0 2012-02-16
 *   first version
 * 1.1 2012-02-16
 *   added $logDir
 *
 * @version 1.0
 * @author Ante Damjanovic
 *
 */
class ADLog {

    /**
     * Logging Directory
     *
     * @var path
     */
    public $logDir = "/home/rconfig/logs/"; // default currentdir
    /**
     * Path to logfile
     *
     * @var path
     */
    public $logFile = "default.log"; // default logfile
    /**
     * log Format
     *
     * Placeholder:
     * #DATETIME# - current DateTime [with format #DATETIMEY-m-d H:i:s.u#]
     * #LEVEL# - Log level $this->logLevels[]
     * #FILE# - File Name __FILE__
     * #METHOD# - Method Name __METHOD__
     * #LINE# - Line Number __LINE__
     * #MESSAGE# - Log Message
     *
     * @var string
     */
    public $logFormat = "#DATETIMEY-m-d H:i:s##LEVEL##FILE##METHOD##LINE##MESSAGE#"; // default all possible placeholders
    /**
     * Max filesize for log
     *
     * @var int
     */
    public $maxFileSize = 10000000; // default 10MB
    /**
     * Max log-files in History
     *
     * @var int
     */
    public $maxHistoryFiles = 5; // default 5 files in History (6 files with current file)
    /**
     * Enable All-.log file with messages from all loglevels
     *
     * @var bool
     */
    public $enableAll = true; // Save All-.log an each loglevel
    /**
     * loglevels that will be saved
     *
     * @var mixed
     */
    public $saveLevels = Array("All", "Info", "Debug", "Warn", "Fatal", "Conn"); // Default save all loglevels
    /**
     * Log-Level names
     *
     * @var mixed
     */
    private $logLevels = Array("All", "Info", "Debug", "Warn", "Fatal", "Conn");

    /**
     * Filesize of all Level-LogFiles
     *
     * @var mixed
     */
    private $fileSize = Array();

    /**
     * singleton instance
     *
     * @var mixed ADLog
     */
    private static $instances = array();

    /**
     * Get Instance for Logfile
     *
     * Resturn a Singleton instance of this class with specific logFile
     *
     * @param path $logFile If nothing "default.log" is used
     * @return ADLog
     */
    public static function getInstance($logFile = null) {

        $logFile = (empty($logFile)) ? "default.log" : $logFile;

        if (!isset(self::$instances[$logFile])) {
            self::$instances[$logFile] = new ADLog($logFile);
        }
        return self::$instances[$logFile];
    }

    /**
     * Constructor sets LogFile and reads LogFile size for all levels
     * private constructor function to prevent external instantiation
     *
     * @param path $logFile
     * @return ADLog
     */
    private function __construct($logFile = null) {
        if ($logFile != null)
            $this->logFile = $logFile;
        foreach ($this->logLevels as $level) {
            if (file_exists($level . "-" . $this->logFile))
                $this->fileSize[$level] = filesize($level . "-" . $this->logFile);
            else
                $this->fileSize[$level] = 0;
        }
    }

    /**
     * Log a Info message
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    public function Info($message, $file = null, $method = null, $lineNr = null) {
        $rawline = $this->MakeLogLine($message, $file, $method, $lineNr);
        $line = $this->AddLevelToLine("Info", $rawline);
        if (in_array("All", $this->saveLevels))
            $this->Save("All", $line);
        if (in_array("Info", $this->saveLevels))
            $this->Save("Info", $line);
    }

    /**
     * Log a Debug message
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    public function Debug($message, $file = null, $method = null, $lineNr = null) {
        $rawline = $this->MakeLogLine($message, $file, $method, $lineNr);
        $line = $this->AddLevelToLine("Debug", $rawline);
        if (in_array("All", $this->saveLevels))
            $this->Save("All", $line);
        if (in_array("Debug", $this->saveLevels))
            $this->Save("Debug", $line);
    }

    /**
     * Log a Warn message
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    public function Warn($message, $file = null, $method = null, $lineNr = null) {
        $rawline = $this->MakeLogLine($message, $file, $method, $lineNr);
        $line = $this->AddLevelToLine("Warn", $rawline);
        if (in_array("All", $this->saveLevels))
            $this->Save("All", $line);
        if (in_array("Warn", $this->saveLevels))
            $this->Save("Warn", $line);
    }

    /**
     * Log a Fatal message
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    public function Fatal($message, $file = null, $method = null, $lineNr = null) {
        $rawline = $this->MakeLogLine($message, $file, $method, $lineNr);
        $line = $this->AddLevelToLine("Fatal", $rawline);
        if (in_array("All", $this->saveLevels))
            $this->Save("All", $line);
        if (in_array("Fatal", $this->saveLevels))
            $this->Save("Fatal", $line);
    }

    /**
     * Log an rConfig Connection message
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    public function Conn($message, $file = null, $method = null, $lineNr = null) {
        $rawline = $this->MakeLogLine($message, $file, $method, $lineNr);
        $line = $this->AddLevelToLine("Conn", $rawline);
        if (in_array("All", $this->saveLevels))
            $this->Save("All", $line);
        if (in_array("Conn", $this->saveLevels))
            $this->Save("Conn", $line);
    }

    /**
     * get var_dump information of object
     *
     * @param mixed $var Debug object
     * @return string var_dump($var) output
     */
    public function VarDump($var) {
        ob_start();
        var_dump($var);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * get print_r information of object
     *
     * @param mixed $var Debug object
     * @return string print_r($var) output
     */
    public function VarPrint($var) {
        return print_r($var, true);
    }

    /**
     * Save message to level-logfile
     *
     * @param string $level logLevel
     * @param string $line LogMessage
     */
    private function Save($level, $line) {
        $filename = $level . "-" . $this->logFile;
        $this->fileSize[$level] += strlen($line);
        if ($this->fileSize[$level] > $this->maxFileSize) {
            $this->RotateFiles($filename);
            file_put_contents($this->logDir . $filename, $line);
            $this->fileSize[$level] = strlen($line);
        } else {
            file_put_contents($this->logDir . $filename, $line, FILE_APPEND);
        }
    }

    /**
     * Rotate files depending on $this->maxFileSize and $this->maxHistoryFiles
     *
     * @param path $filename
     */
    private function RotateFiles($filename) {
        for ($i = $this->maxHistoryFiles; $i > 0; $i--) {
            $filenameHistory = $this->logDir . $filename . '.' . $i . '.log';
            if (file_exists($filenameHistory) && $i == $this->maxHistoryFiles) {
                unlink($filenameHistory);
            } else if (file_exists($filenameHistory)) {
                $filenameHistoryNext = $this->logDir . $filename . '.' . ($i + 1) . '.log';
                if (file_exists($filenameHistoryNext))
                    unlink($filenameHistoryNext);
                copy($filenameHistory, $filenameHistoryNext);
            }
        }
        $filenameHistoryNext = $this->logDir . $filename . '.1.log';
        if (file_exists($filenameHistoryNext))
            unlink($filenameHistoryNext);
        copy($this->logDir . $filename, $filenameHistoryNext);
        file_put_contents($this->logDir . $filename, "");
    }

    /**
     * Reaplace placeholders in LogMessage
     *
     * @param string $message LogMessage
     * @param __FILE__ $file FileName
     * @param __METHOD__ $method MethodName
     * @param __LINE__ $lineNr LineNumber
     */
    private function MakeLogLine($message, $file = null, $method = null, $lineNr = null) {
        $line = $this->logFormat;

        $strposDateTime = strpos($this->logFormat, "#DATETIME");
        if ($strposDateTime !== false) {
            $strposEndHash = strpos(substr($this->logFormat, $strposDateTime + 9), "#");
            $format = substr($this->logFormat, $strposDateTime + 9, $strposEndHash);
            if (!empty($format))
                $line = str_replace("#DATETIME" . $format . "#", date($format) . " ", $line);
            else
                $line = str_replace("#DATETIME#", date("Y-m-d H:i:s") . " ", $line);
        }

        $line = str_replace("#LINE#", $lineNr ? $lineNr . ": " : "", $line);
        $line = str_replace("#FILE#", $file ? $file . " " : "", $line);
        $line = str_replace("#METHOD#", $method ? $method . "() " : "", $line);
        $line = str_replace("#MESSAGE#", $message, $line);

        return $line . "\n";
    }

    /**
     * Replace #LEVEL# with $level
     *
     * @param string $level logLevel
     * @param string $line LogMessage
     * @return string LogMessage
     */
    private function AddLevelToLine($level, $line) {
        return str_replace("#LEVEL#", strtoupper($level) . " ", $line);
    }

}
