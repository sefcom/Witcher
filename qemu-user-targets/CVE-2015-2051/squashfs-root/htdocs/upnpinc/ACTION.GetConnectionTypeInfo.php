<?
if (query("/runtime/device/layout")=="router")	$type = "IP_Routed";
else											$type = "IP_Bridged";
?><NewConnectionType><?=$type?></NewConnectionType>
<NewPossibleConnectionTypes><?=$type?></NewPossibleConnectionTypes>
