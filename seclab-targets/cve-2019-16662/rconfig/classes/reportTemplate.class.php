<?php

/**
 * report generation Class
 */
class report {

    /**
     * Class Constructor
     * @param  string  $date Full date in 'Ymd' format
     * @param  string  $year Year in 'Y' format
     * @param  string  $year month in 'm' format
     * @param  string  $day month in 'd' format
     * @param  string  $reportDir Final Reports Directory
     * @param  string  $filename Report Filename
     * @param  string  $config_reports_basedir Top Level Reports Directory
     */
    public function __construct($config_reports_basedir, $filename, $reportDir, $serverIp) {
        $this->log = ADLog::getInstance();
        // Set some variables for file and folder creation
        $this->date = date('Ymd');
        $this->year = date('Y');
        $this->month = date('M');
        $this->day = date('d');
        $this->reportDate = date('D d M Y');
        $this->reportDir = $reportDir;
        $this->filename = $filename;
        $this->reportFolder = $config_reports_basedir . $reportDir . "/";
        $this->fullReportPath = $config_reports_basedir . $reportDir . "/" . $filename;
        $this->serverIp = $serverIp;
    }

    /**
     * create File method
     *
     */
    public function createFile() {
        // create dir if not exists
        if (!file_exists($this->reportFolder)) {
            mkdir($this->reportFolder, 0755, true);
        }

        if ($this->existsFile()) {
            $this->deleteFile();
        }
        $handle = fopen($this->fullReportPath, 'w');
        if (!$handle) {

            $this->log->Fatal("Cannot open file  Func: createFile():  " . $this->fullReportPath . "(File: " . $_SERVER['PHP_SELF'] . ")");
        }
    }

    /**
     * Open File function
     *
     */
    private function openFile() {

        $handle = fopen($this->fullReportPath, 'a');
        if (!$handle) {
            $this->log->Fatal("Cannot open file Func:header() :  " . $this->fullReportPath . "(File: " . $_SERVER['PHP_SELF'] . ")");
        }
        return $handle;
    }

    /**
     * delete File method
     *
     */
    private function deleteFile() {

        unlink($this->fullReportPath);
    }

    /**
     * check if File exists function
     *
     */
    private function existsFile() {

        if (file_exists($this->fullReportPath)) {
            return true;
        }
    }

    /**
     * write and close file
     *
     */
    private function writeAndCloseFile($handle, $data) {

        fwrite($handle, $data);
        $this->closeFile($handle);
    }

    /**
     * Close File function
     *
     */
    private function closeFile($handle) {

        fclose($handle);
    }

    /**
     * create File HTML header method
     *
     */
    public function header($title, $reportName, $scriptName, $taskid, $taskStartTime) {

        $handle = $this->openFile();
        $reportCss = file_get_contents('/home/rconfig/www/css/reportstyle.css');
        $compareTableCss = file_get_contents('/home/rconfig/www/css/compareTable.css');
        $logo = 'https://www.rconfig.com/images/new_logos/red_logos/artwork_red_horizontalArtboard 1_48px.png';

        $data = <<<EOF
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>$title</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type='text/css'>
$reportCss
$compareTableCss
</style>
</head>
<body>
    <div id="wrapper">
    <div id="header">
        <h1><span id="title">$title</span></h1>
    </div>
     <div id="content">
        <table id="theader" summary="Report Metadata"> 
          <tr>
            <td>
	      <div style="float: left; font-weight: bold" id="leftinf">
                Report Name:<br/>
                Report Date:<br/>
                ScriptName:<br/>
                Task ID:<br/>
                Task Start Time:<br/>
                Task End Time:<br/>
                Task Run Time:<br/>
              </div>
            </td>
            <td> 
              <div style="float: left;" id="rightinf">
                $reportName<br/>
                $this->reportDate<br/>
                $scriptName<br/>
                $taskid<br/>
                $taskStartTime<br/>
                <taskEndTime><br/>
                <taskRunTime><br/>
              </div>
            </td>
            <td>
              <div style="float: right;"  id="summDiv"><img src="$logo" alt="rConfig" title="rConfig" /></div>
	    </td>
          </tr>
	</table>
        <div style="clear:both"> </div>
        <hr/>
EOF;
        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body method
     *
     */
    public function eachData($deviceName, $status, $data) {

        $handle = $this->openFile();

        $data = <<<EOF
<div>
	<table id="hor-zebra" summary="Report Data">
		<thead>
		<tr>
			<th scope="col" colspan="4">Device Name: $deviceName</th>
		</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<td>Status:</td>
				<td colspan="3">$status</td>
			</tr>
			<tr>
				<td>Notice:</td>
				<td colspan="3">$data</td>
			</tr>
		</tbody>
	</table>
</div>          		 
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body rowDeviceName func
     *
     */
    public function eachComplianceDataRowDeviceName($deviceName) {

        $handle = $this->openFile();

        $data = <<<EOF
<!-- BEGIN DATA APPEND -->
<div>
	<table id="hor-zebra" summary="Report Data">
		<thead>
		<tr>
			<th scope="col" colspan="4">$deviceName</th> 
		</tr>
		</thead>			
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body eachComplianceDataRowPolicyName func
     *
     */
    public function eachComplianceDataRowPolicyName($polName, $fileName) {

        $handle = $this->openFile();

        $data = <<<EOF
	<tbody>
		<tr class="odd indentRow" style="float:left; width:800px;">
			<td><b>Policy Name:</b></td>
			<td>$polName</td>
		</tr>   		 
		<tr class="odd indentRow" style="float:left; width:800px;">
			<td><b>File:</b></td>
			<td>$fileName</td>
		</tr>
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body eachComplianceData func
     *
     */
    public function eachComplianceData($data) {

        $handle = $this->openFile();

        $data = <<<EOF
			$data
		</tbody>
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body endComplianceData func
     *
     */
    public function endComplianceData() {

        $handle = $this->openFile();

        $data = <<<EOF
	</table>
</div>      
<!-- END DATA APPEND -->    		 
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body eachConfigSnippetData func
     *
     */
    public function eachConfigSnippetData($data) {

        $handle = $this->openFile();

        $data = <<<EOF
		<table style="margin: 15px;">
		<tbody>
			$data
		</tbody>
		</table>
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File body endConfigSnippetData func
     *
     */
    public function endConfigSnippetData() {

        $handle = $this->openFile();

        $data = <<<EOF
</div>      
<!-- END DATA APPEND -->    		 
EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    /**
     * create File footer HTML method
     *
     */
    public function footer() {

        $handle = $this->openFile();
        $data = <<<EOF
	 </div>
	 <div style="clear:both;"></div>
		 <div id="footer">
			   &copy; rConfig	
	     </div>   
	</div>
</body>
</html>

EOF;

        $this->writeAndCloseFile($handle, $data);
    }

    public function findReplace($tag1, $tag2) {
        $data = file_get_contents($this->fullReportPath);

        // do tag replacements or whatever you want
        $data = str_replace($tag1, $tag2, $data);

        //save it back:
        file_put_contents($this->fullReportPath, $data);
    }

}

?>
