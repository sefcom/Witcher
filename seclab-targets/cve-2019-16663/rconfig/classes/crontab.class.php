<?php

/**
 * crontab Class
 * @param  string  $script full crontab script as passed
 * @param  string  $taskName hashed/ commentred task name
 * @param  string  $taskDesc hashed/ commentred task description - can be null
 * @param  string  $cronPattern crontab time pattern
 */
class crontab {

    public function __construct($script, $taskName, $taskDesc = null, $cronPattern) {
        require_once("ADLog.class.php");
        $this->log = ADLog::getInstance();

        // Set some variables for file and folder creation
        $this->script = $script;
        $this->taskName = $taskName;
        $this->taskDesc = $taskDesc;
        $this->cronPattern = $cronPattern;
        $this->crontabContent = $this->taskName . PHP_EOL . $this->taskDesc . PHP_EOL . $this->cronPattern . $this->script;

        /* File and DIR variables for global use */
        $this->cronFolder = "/home/rconfig/cronfeed";
        $this->filename = "cronfeed.txt";
        $this->fullpath = $this->cronFolder . "/" . $this->filename;
    }

    /**
     * addCron method
     * @return boolean returns true if all tasks pass
     */
    public function addCron() {
        // Type your code here
        if ($this->_createCronDir() && $this->_createCronFile()) {
            $this->_chmodCronFile($this->fullpath, 0666);
            $this->_appendToCronFile($this->fullpath, $this->crontabContent);
            $this->_updateCronTab($this->fullpath);
            $this->_chmodCronFile($this->fullpath, 0444);
            $ret = true;
        } else {
            $ret = false;
        }
        return $ret;
    }

    /**
     * addCron method
     * @return boolean returns true if all tasks pass
     */
    public function removeCron($delTaskName, $delTaskDesc, $delCronJob) {
        $fileOutput = $this->_readCronFromFile($this->fullpath);
        // unset taskName, taskDesc, and Task from array
        if (is_array($fileOutput)) {
            $newArray = array();
            $newArray = $this->_arraySearchUnset($delTaskName, $fileOutput);
            $newArray = $this->_arraySearchUnset($delTaskDesc, $newArray);
            $newArray = $this->_arraySearchUnset($delCronJob, $newArray);
            $this->_chmodCronFile($this->fullpath, 0666);
            if ($this->_overwriteCronFile($this->fullpath, $newArray) == true)
                ;
            $this->_updateCronTab($this->fullpath);
            $this->_chmodCronFile($this->fullpath, 0444);
            $ret = true;
        } else {
            $ret = false;
        }
        return $ret;
    }

    /**
     * _chmodCronFile method
     */
    private function _chmodCronFile($file, $value) {
        chmod($file, $value);
    }

    /**
     * _appendToCronFile method
     */
    private function _appendToCronFile($file, $content) {
        file_put_contents($file, $content . PHP_EOL, FILE_APPEND); // append file with new task
    }

    /**
     * _overwriteCronFile method
     */
    private function _overwriteCronFile($file, $content) {
        if ($fh = fopen($file, 'w+')) {
            $string = '';
            foreach ($content as $key => $val) {
                $string .= "$val\n";
            }
            file_put_contents($file, $string);
            fclose($fh);
            return true;
        } else {
            return false;
        }
    }

    /**
     * _appendToCronFile method
     * @desc read cronfeed.txt into array
     */
    private function _readCronFromFile($file) {
        $arr = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $arr;
    }

    /**
     * _updateCronTab method
     */
    private function _updateCronTab($file) {
        echo shell_exec('sudo -u apache /usr/bin/crontab ' . $file . '  2>&1');
        /* to check crontab -l on the server directly you must sudo to apache
         *  i.e. sudo -u apache /usr/bin/crontab -l */
    }

    /**
     * _arraySearchUnset method
     */
    private function _arraySearchUnset($item, $array) {
        $taskNameindex = array_search($item, $array);
        if ($taskNameindex !== FALSE) {
            unset($array[$taskNameindex]);
        }
        return $array;
    }

    /**
     * _createCronDir method
     * @return boolean returns true if all tasks pass
     */
    private function _createCronDir() {
        // create hostname dir based on hostname if not already present
        if (!is_dir($this->cronFolder)) {
            if (mkdir($this->cronFolder, 0777)) {
                $this->log->Info("Success: Created " . $this->cronFolder . " Directory (File: " . $_SERVER['PHP_SELF'] . ")");
                return true;
            } else {
                $errors['cronFolder'] = "Could not create " . $this->cronFolder; // throw an error
                $this->log->Warn("Failure: Could not create " . $this->cronFolder . " (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * _createCronFile method
     * @return boolean returns true if all tasks pass
     */
    private function _createCronFile() {
        // check if cronfeed.txt exists and create it if NOT
        if (!file_exists($this->fullpath)) {
            $handle = fopen($this->fullpath, 'w'); // create cronfeed.txt if not 
            if (file_exists($this->fullpath)) {
                $this->log->Info("Success: Created " . $this->fullpath . " file (File: " . $_SERVER['PHP_SELF'] . ")");
                return true;
            } else {
                $errors['fileCreateError'] = "Could not create " . $this->fullpath; // throw an error
                $this->log->Warn("Failure: Could not create " . $this->fullpath . " file (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            }
            fclose($handle); // close handle that created the file
        } else {
            return true;
        }
    }

}

// end class
?>