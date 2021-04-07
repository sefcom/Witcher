<?
function SLP_setlangcode($code)
{
	set("/runtime/device/langcode", $code);
	if		($code=="en")	ftime("STRFTIME", "%m/%d/%Y %T");
	else if	($code=="fr")	ftime("STRFTIME", "%d/%m/%Y %T");
	else ftime("STRFTIME", "%Y/%m/%d %T");
}
?>
