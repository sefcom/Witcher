<?
/* F/W is put in nand flash but  flashspeed is calc in nor flash.
  so we hard code the nand flash time.
*/
set("/runtime/device/fptime", 100);
set("/runtime/device/bootuptime", 60);
?>
