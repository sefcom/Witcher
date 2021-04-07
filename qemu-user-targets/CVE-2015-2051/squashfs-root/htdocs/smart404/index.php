<?
include "/htdocs/phplib/xnode.php";
?>
<HTML>
	<HEAD>
		<TITLE><?echo query("/runtime/device/modelname");?></TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=big5"> 
		<meta http-equiv="Pragma" content="no-cache"> 
		<meta http-equiv="expires" content="0"> 
	</HEAD>
<script language="JavaScript" type = "text/javascript">
<?
	$size = query("/runtime/device/devconfsize");
	if		($size == "") {	$is_default = 0; }
	else if ($size > 0)	{ $is_default = 0; }
	else				{ $is_default = 1; }
	if($is_default==1) 	{ echo "location.href = \"../index.php\";"; }
?>
</script>
	<BODY>
<table id="___01" width="836" height="551" border="0" cellpadding="0" cellspacing="0"> 
	<tr> 
		<td height="14" colspan="3" bgcolor="#E6E6E6">&nbsp;</td> 
	</tr> 
	<tr> 
		<td width="23" height="537" rowspan="2" bgcolor="#E6E6E6">&nbsp;</td> 
		<td> 
			<table id="___" width="786" height="539" border="0" cellpadding="0" cellspacing="0"> 
				<tr> 
					<td rowspan="10" width="78">&nbsp;</td> 
					<td height="35" colspan="7">&nbsp;</td> 
				</tr> 
				<tr> 
					<td width="120"><strong><font face="Arial, Helvetica, sans-serif"><font color="#66CCFF" size="6">Oops!</font></font></strong></td> 
					<td height="45" colspan="6">&nbsp;</td> 
				</tr> 
				<tr> 
					<td height="13" colspan="7">&nbsp;</td> 
				</tr> 
				<tr> 
					<td height="35" colspan="7"><font color="#808080" size="6"><font color="#999999" size="6" face="Arial, Helvetica, sans-serif">The 
					page you requested is not available.</font></font></td> 
				</tr> 
				<tr> 
					<td height="38" colspan="7">&nbsp;</td> 
				</tr> 
				<tr> 
					<td colspan="5"><a href="../index.php"><img src="../pic/smart_head.jpg" width="606" height="128" alt="" style="border:none;"></a></td> 
					<td height="128" colspan="2">&nbsp;</td> 
				</tr> 
				<tr> 
					<td height="13" colspan="7">&nbsp;</td> 
				</tr> 
				<tr> 
						<td colspan="5"> 
					<p><font color="#999999" size="2" face="Verdana, Arial, Helvetica, sans-serif">Suggestions:</font></p> 
					<font color="#999999" size="2"> 
					<ol style="font-family:Verdana, Arial, Helvetica, sans-serif"> 
					<li><?echo i18n("Make sure your internet cable is securely connected to the internet port on your router, and your internet LED is blink green or blue.");?></li> 
					<li><?echo i18n("Check to make sure that the");?> <a href="../index.php"><?echo i18n("Internet 
							settings");?></a> <?echo i18n("on your router are set correctly, such as your PPPoE 
							username/password settings.");?></li> 
					<li><?echo i18n("The DNS server may be down at the moment, please contact your ISP or try again later.");?></li> 
					</ol></font></td> 
					<td height="86" colspan="2">&nbsp;</td> 
				</tr> 
				<tr> 
					<td height="48" colspan="3">&nbsp;</td> 
					<td colspan="3" rowspan="2"><img src="../pic/smart_head_1.jpg" width="229" height="75" alt=""></td> 
					<td width="46" height="75" rowspan="2">&nbsp;</td> 
				</tr> 
				<tr> 
					<td height="2" colspan="2">&nbsp;</td> 
					<td width="305" height="2"><font face="Arial, Helvetica, sans-serif" size="1"><a href="http://www.dlink.com">Copyright &copy; 2012 D-Link Corporation. All rights reserved.</a></font></td> 
				</tr> 
				</table> 
			</td> 
				<td width="27" height="537" rowspan="2" bgcolor="#E6E6E6">&nbsp;</td> 	
	</tr> 
		<tr> 
			<td width="782" height="32" bgcolor="#E6E6E6">&nbsp;</td> 
		</tr> 
</table> 


	</BODY>
</HTML>
