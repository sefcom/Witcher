<?php
   if ($_REQUEST['status']=='remindlater') {
    $File = "/tmp/Popup_RemindLater"; 
    $Handle = fopen($File, 'w');
    fclose($Handle);  
   }
   else if ($_REQUEST['status']=='turnoff')
   {
    $File = "/var/Prod_Reg_Rem_TurnOff"; 
    $Handle = fopen($File, 'w');
    fclose($Handle);  
   }
   else if ($_REQUEST['status']=='registered')
   {
    $File = "/var/SerialRegistered"; 
    $Handle = fopen($File, 'w');
    fclose($Handle);  
   }
?>