<?php

/**
 * debugClass.php
 * 
 * The Debug class is to have a single function for echoing and 
 * printing device connection debugging information
 * 
 * Written by: Stephen Stack, rConfig
 * Last Updated: July 21 2012
 */
class debug {

    /**
     * Class Constructor
     * @param  string  $debugPath DebugPath Per DB value
     * @return object  connectProcess object
     */
    public function __construct($debugPath) {
        $this->_debugPath = $debugPath;
    }

    /**
     * debug - takes the $value as inputted to the function and echos to console, and writes to file path per $debugPath
     */
    public function debug($value) {

        $value = print_r($value, true);
        echo $this->_date();
        echo $value;
        $filename = $this->_debugPath . "debug" . date('YMd') . ".txt";
        $fp = fopen($filename, "a");
        fwrite($fp, $this->_date());
        fwrite($fp, $value);
        fclose($fp);
    }

    private function _date() {
        $date = date('j F Y h:i:s A') . "\n";
        return $date;
    }

}

?>