<?
include "/htdocs/phplib/xnode.php";

fwrite("w",$START, "#!/bin/sh\n");
fwrite("w", $STOP, "#!/bin/sh\n");

fwrite("a",$START,	
	"service PHYINF.BAND5G-1.1 start\n".
	"service PHYINF.BAND5G-1.2 start\n"
	);
	
fwrite("a",$STOP,
	"service PHYINF.BAND5G-1.2 stop\n".
	"service PHYINF.BAND5G-1.1 stop\n"
	);

fwrite("a",$START,	"exit 0\n");
fwrite("a", $STOP,	"exit 0\n");
?>
