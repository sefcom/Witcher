<?php

/**
 * textFile.class.php
 * 
 * The text file class is meant to simplify the task of creating text files and folders 
 * and inserting command output to the correct file
 * 
 * Written by: Stephen Stack, rConfig
 * Last Updated: Oct 3 2017
 */
class file {

    /**
     * Class Constructor
     * @param  string  $date Full date in 'Ymd' format
     * @param  string  $year Year in 'Y' format
     * @param  string  $year month in 'm' format
     * @param  string  $day month in 'd' format
     * @param  string  $catfolder Device category name folder
     * @param  string  $hostfolder Device name folder
     * @param  string  $yearfolder Year folder
     * @param  string  $monthfolder Month folder
     * @param  string  $todayfolder Todays Date folder
     * @return object  file object
     */
    public function __construct($catName, $deviceName, $config_data_basedir) {

        // Set some variables for file and folder creation
        $this->date = date('Ymd');
        $this->year = date('Y');
        $this->month = date('M');
        $this->day = date('d');
        $this->catfolder = $config_data_basedir . $catName . "/";
        $this->hostfolder = $config_data_basedir . $catName . "/" . $deviceName;
        $this->yearfolder = $config_data_basedir . $catName . "/" . $deviceName . "/" . $this->year;
        $this->monthfolder = $config_data_basedir . $catName . "/" . $deviceName . "/" . $this->year . "/" . $this->month;
        $this->todayfolder = $config_data_basedir . $catName . "/" . $deviceName . "/" . $this->year . "/" . $this->month . "/" . $this->day;
    }

    /**
     * Function createFile
     * @param  string  $command Device Command String
     * @return string  $fullpath fullpath after addition of category,device,date,command
     */
    public function createFile($command) {
        $command = cleanDeviceName($command);
        //create the file
        $filename = $this->_createFileName($command);
        $fullpath = $this->todayfolder . "/" . $filename;

        // create category dir based on hostname if not already made
        if (!is_dir($this->catfolder)) {
            mkdir($this->catfolder);
            chown($this->catfolder, 'apache');
        }

        // create hostname dir based on hostname if not already made
        if (!is_dir($this->hostfolder)) {
            mkdir($this->hostfolder);
            chown($this->hostfolder, 'apache');
        }

        // create todays dir.name based on this years date if not already made
        if (!is_dir($this->yearfolder)) {
            mkdir($this->yearfolder);
            chown($this->yearfolder, 'apache');
        }

        // create todays dir.name based on this months date if not already made
        if (!is_dir($this->monthfolder)) {
            mkdir($this->monthfolder);
            chown($this->monthfolder, 'apache');
        }

        // create todays dir.name based on todays date if not already made
        if (!is_dir($this->todayfolder)) {
            mkdir($this->todayfolder);
            chown($this->todayfolder, 'apache');
        }

        // if'' to create the filename based on the command if not created & chmod to 666
        if (!file_exists($fullpath)) {
            exec("touch " . $fullpath);
            chmod($fullpath, 0666);
        }

        return(string) $fullpath;
    }

    /**
     * Function insertFileContents
     * @param  string  $lines Command output from device
     * @param  string  $fullpath Fullpath as return by createFile Function to main script
     */
    public function insertFileContents($lines, $fullpath) {
        // if the file is alread in place chmod it to 666 before writing info
        chmod($fullpath, 0666);

        // dump array into file & chmod back to RO
        $filehandle = fopen($fullpath, 'w+');
        file_put_contents($fullpath, $lines);
        fclose($filehandle);
        chmod($fullpath, 0444);
    }

    /**
     * Function appendFileContents
     * @param  string  $lines Command output from device
     * @param  string  $fullpath Fullpath as return by createFile Function to main script
     */
    public static function appendFileContents($lines, $fullpath) {
        // if the file is alread in place chmod it to 666 before writing info
        chmod($fullpath, 0666);

        // dump array into file & chmod back to RO
        $filehandle = fopen($fullpath, 'a');
        file_put_contents($fullpath, $lines);
        fclose($filehandle);
        chmod($fullpath, 0444);
    }

    /**
     * Function private _createFileName
     * @param  string  $command Command from device
     * @param  string  $filename Filename after removing spaces and appending '.txt'
     */
    private function _createFileName($command) {
        $timestamp = date('Gi'); // format 1301
        // Create file name and return it
        $filename = str_replace(" ", "", $command) . '-' . $timestamp . ".txt";
        return $filename;
    }

}
