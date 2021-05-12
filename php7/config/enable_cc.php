<?php

    $coverage_dpath = "/tmp/coverages/"; //__DIR__;
    if (!is_dir($current_dir)){
        mkdir($current_dir, 0777, true);
    }
    $tarut = (isset($_SERVER['SCRIPT_FILENAME']) && !empty($_SERVER['SCRIPT_FILENAME'])) ? $_SERVER['SCRIPT_FILENAME'] : $_SERVER['REQUEST_URI'];
    $tarut_dirname = str_replace("/app", "", dirname($tarut));
    $tarut_dirname = str_replace("/","+",$tarut_dirname );

    $tarut_basename = basename($tarut, ".php");

    $tarut_name = $tarut_dirname . "+" . $tarut_basename;
    //echo "hidy-ho neighbor " . $tarut_dirname . "+" . $tarut_basename . "\n";

    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

    function end_coverage()
    {
        global $tarut_name;
        global $coverage_dpath;

        $coverageName = $coverage_dpath . $tarut_name . ".cc";

        try {
            xdebug_stop_code_coverage(false);
            $cur_cc_jdata = xdebug_get_code_coverage();

            // if prior file exists merge jsons
            if (file_exists($tarut_name)) {
                $prior_cc_jdata = json_decode(file_get_contents($tarut_name));
                $cur_cc_jdata = merge_jsons($prior_cc_jdata, $cur_cc_jdata);
                $cur_cc_jdata = json_encode($out_jdata);
            }
            $cur_cc_jstr = json_encode($cur_cc_jdata); // obj to str

            file_put_contents($coverageName . '.json', $cur_cc_jstr);

        } catch (Exception $ex) {
            file_put_contents($coverageName . '.ex', $ex);
        }
    }

    // merges 2 code coverage jsons always keeping values > 0 from either dictionary
    function merge_jsons($jdata1, $jdata2) {

        foreach ($jdata1 as $uri => $uri_data):

            foreach ($uri_data as $line => $line_result):
                if (isset($jdata2[$uri])){
                    if (isset($jdata2[$uri][$line]) && $jdata2[$uri][$line] > 0){
                     // do nothing
                    } else {
                        // if jdata2 doesn't have the value or its less than zero than use line_result, copy everything from jdata1
                        $jdata2[$uri][$line] = $line_result;
                    }
                } else {
                    $jdata2[$uri] = array();
                    $jdata2[$uri][$line] = $line_result;
                }

            endforeach;

        endforeach;

        return $jdata2;
    }

    class coverage_dumper
    {
        function __destruct()
        {
            try {
                end_coverage();
            } catch (Exception $ex) {
                echo str($ex);
            }
        }
    }

    $_coverage_dumper = new coverage_dumper();
