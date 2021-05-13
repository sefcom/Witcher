<?php
	@include('sessionCheck.inc');
	
	$productIdArr = explode(' ', conf_get('system:monitor:productId'));
	$productId = $productIdArr[1];
	
	$flag=false;
	$msg='';
	if (!empty($_REQUEST['writeData'])) {
		if (!empty($_REQUEST['macAddress']) && array_search($_REQUEST['reginfo'],Array('WW'=>'0','NA'=>'1'))!==false && ereg("[0-9a-fA-F]{12,12}",$_REQUEST['macAddress'],$regs)!==false) {
			//echo "test ".$_REQUEST['macAddress']." ".$_REQUEST['reginfo'];
			//exec("wr_mfg_data ".$_REQUEST['macAddress']." ".$_REQUEST['reginfo'],$dummy,$res);
			exec("wr_mfg_data -m ".$_REQUEST['macAddress']." -c ".$_REQUEST['reginfo'],$dummy,$res);
			if ($res==0) {
				conf_set_buffer("system:basicSettings:apName netgear".substr($_REQUEST['macAddress'], -6)."\n");
				conf_save();
				$msg = 'Update Success!';
				$flag = true;
			}
		 }
		if (($productId=='WNDAP360' || $productId=='WNDAP350' || $productId=='WNAP320') && ($_REQUEST['antenna']!='')) {
			conf_set_buffer("system:wlanSettings:wlanSettingTable:wlan0:antenna ".$_REQUEST['antenna']."\n");
			conf_save();
			$flag = true;
		}
		else
			$flag = true;
	}

?>
<html>
	<head>
		<title>Netgear</title>
		<style>
			<!--
				TABLE {
					margin-left: auto;
					margin-right: auto;
				}
				TD {
					padding: 5px;
					text-align: left;
					vertical-align: top;
				}
				.right {
					text-align: right;
				}
			-->
		</style>
		<script type="text/javascript">
			<!--
				function checkMAC(eventobj,mac) {
					if (!(/^[0-9A-Fa-f]{12,12}$/.test(mac))) {
						document.getElementById('br_head').innerHTML='Enter valid MAC Address!';
						document.getElementById('errorMessageBlock').style.display='block';
						document.getElementById('macAddress').focus();
						if (!eventobj || ((navigator.userAgent.toLowerCase().indexOf("msie") != -1) && (navigator.userAgent.toLowerCase().indexOf("opera") == -1)))
						{
							window.event.returnValue = false;
							window.event.cancelBubble = true;
							event.returnValue = false;
						}
						else
						{
							eventobj.stopPropagation();
							eventobj.preventDefault();
						}
						return false;
					}
					else {
						document.getElementById('errorMessageBlock').style.display='none';
					}
				}
			-->
		</script>
	</head>
	<body align="center">
		<form name="hiddenForm" action="boardDataNA.php" method="post" align="center">
			<div align="center">
			<table align="center" style="margin: 20px; width: 40%; text-align: center; border: 1px solid #46008F">
				<tr>
					<td width="100%" colspan="2" align="center">
						<div align="center" style="margin:auto;">
							<table id="errorMessageBlock" align="center" style="margin: 4px auto 10px auto; <?php if ($flag != true) echo 'display: none;' ?>">
								<tr>
									<td style="padding: 5px; vertical-align: top;"><img src="images/alert.gif" style="border: 0px; padding: 0px; margin: 0px;"></td>
									<td style="padding: 5px 5px 5px 0px; vertical-align: middle;"><b id="br_head" style="color: #CC0000;"><?php if ($flag == true) echo ($msg=='')?"Invalid Data!":$msg; ?></b></td>
								</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td width="30%" class="right"><label for="macAddress"><b>MAC Address</b></label></td>
					<td width="70%"><input type="text" id="macAddress" name="macAddress" label="MAC Address" value="<?php echo $_REQUEST['macAddress'] ?>" onasdf="checkMAC(this.value);">&nbsp;<small>* Format: xxxxxxxxxxxx (x = Hex String)</small></td>
				</tr>
                               <?php if($productId=='WNDAP360' || $productId=='WNDAP350' || $productId=='WNAP320'){ ?>  
                                <tr>
                                        <td width="30%" class="right"><label for="antenna"><b>Antenna</b></label></td>
                                        <td width="70%"><select id="antenna" name="antenna">
                                                            <option value='0'>Internal </option>
                                                            <option value='1' <?php if($_REQUEST['antenna']==1){ ?> selected<?} ?>>External </option>  
                                                        </select>
                                        </td>
                                </tr>
                               <?php } ?>
				<tr>
					<td width="30%" class="right"><label for="reginfo"><b>Region</b></label></td>
					<td width="70%">
						<input type="radio" id="reginfo" name="reginfo" value="1" checked="true"><small>North America (NA)</small><br>
					</td>
				</tr>
				<tr>
					<td width="30%" class="right"><input type="submit" name="writeData" value="Submit" onclick="checkMAC(event, document.getElementById('macAddress').value);"></td>
					<td width="70%"><input type="reset" name="reset" value="Reset Form"></td>
				</tr>
			</table>
			</div>
		</form>
	</body>
</html>
