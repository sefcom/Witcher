<?php

$coverage_dpath = "/tmp/coverages/"; //__DIR__;

$mysqli = new mysqli("127.0.0.1","witcher", "witcherpw", "witchercc");

$result = $mysqli->query("SELECT min(create_ts) as cts, max(last_exec_ts) as lts, sum(execs)  FROM page");
if ($row = $result->fetch_row()) {
    $create_ts = $row[0];
    $last_exec_ts = $row[1];
    $total_execs = $row[2];
}

$execs_out = array("start_time"=>$create_ts, "last_time"=>$last_exec_ts, "total_execs"=>$total_execs);

$result = $mysqli->query("SELECT pagename, execs, create_ts, last_exec_ts FROM page ");
while ($row = $result->fetch_row()) {
    list($pagename, $execs, $create_ts, $last_exec_ts) = $row;
    $execs_out[$pagename] = array("execs"=>$execs, "create_ts"=> $create_ts, "last_exec_ts"=>$last_exec_ts);
}

$execs_out_str = json_encode($execs_out); // obj to str
file_put_contents($coverage_dpath . "execs.json", $execs_out_str, LOCK_EX);

$ccdata = array();
$result = $mysqli->query("SELECT scriptname FROM script ");

$scriptnames = array();
while ($row = $result->fetch_row()) {
    $scriptnames[] = $row[0];
}

foreach ($scriptnames as $snindex => $scriptname):
    $ccdata[$scriptname] = array();
    $stmtcc = $mysqli->prepare("SELECT lineno, ccval FROM codecov WHERE FK_scriptname = ? ");
    if ($stmtcc === false) {
       die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }
    $stmtcc->bind_param("s", $scriptname);
    if ($stmtcc->execute()){
        $stmtcc->bind_result($lineno, $ccval);
        while ($stmtcc->fetch()) {
            $ccdata[$scriptname][$lineno] = $ccval;
        }
    }
    $stmtcc->close();
endforeach;

$ccdata_str = json_encode($ccdata); // obj to str
file_put_contents($coverage_dpath . "codecoveragedata.json", $ccdata_str, LOCK_EX);


