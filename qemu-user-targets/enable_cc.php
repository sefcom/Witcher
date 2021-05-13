<?php

  register_shutdown_function(function() {{
        $jdata = json_encode(xdebug_get_code_coverage());

        $jdataout = fopen("/tmp/testingcc_prepend.json", "w") or die("Unable to open file!");
        fwrite($jdataout, $jdata);
        fclose($jdataout);
        echo "SAVED trace to file /tmp/testingcc_prepend.json\n\n";
  }});

  xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
  //xdebug_start_code_coverage();

?>