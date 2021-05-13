<?php /* Smarty version 2.6.18, created on 2009-01-12 07:49:47
         compiled from siteSurvey.tpl */ ?>
<html>
	<title>Wireless Station Details</title>
	<head>
		<link rel="stylesheet" href="include/css/style.css" type="text/css">
		<link rel="stylesheet" href="include/css/default.css" type="text/css">
	</head>
	<body style="padding: 5px;">
		<table class="tableStyle" style="text-align: center;">
			<tr>
				<td>	
					<table class="tableStyle">
						<tr>
							<td colspan="3">
								<table class='tableStyle'>
									<tr>
										<td colspan='2' class='subSectionTabTopLeft spacer80Percent font12BoldBlue'>Site Survey List</td>
										<td class='subSectionTabTopRight spacer20Percent'></td>
									</tr>
									<tr>
										<td colspan='3' class='subSectionTabTopShadow'></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="subSectionBodyDot">&nbsp;</td>
							<td class="spacer100Percent paddingsubSectionBody" style="padding: 0px;">
								<table class="tableStyle BlockContent" id="layeredWinTable" style="background-color: #FFFFFF;">
											<table class="BlockContentTable">
													<tr>
														<th>&nbsp;</th>
														<th>SSID</th>
														<th>Security</th>
														<th>Encryption</th>
														<th>Channel</th>
													</tr>
													<?php $_from = $this->_tpl_vars['data']['monitor']['apList']['detectedApTable']['wlan0']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['profiles'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['profiles']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
        $this->_foreach['profiles']['iteration']++;
?>
													<tr <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?> id="row_<?php echo $this->_foreach['profiles']['iteration']; ?>
">
														<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?> style="width: 5%;"><input type="button"  id="ApScanId" name="profileid0" value="Select" onclick="window.opener.copyAPDetails(this.parentNode.parentNode);"></td>
														<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['apSsid']; ?>
</td>
														<?php 
															$test =	array(	"open"	=>	"Open System",
										                            		"wep"	=>	"Open System",
										                            		"wpa"	=>	"WPA-PSK",
										                            		"wpa2"	=>	"WPA2-PSK",
										                            		"wpapsk"	=>	"WPA-PSK",
										                            		"wpa2psk"	=>	"WPA2-PSK"
										                       	 	);
															$this->_tpl_vars['apAuthProto'] = array_search($test[$this->_tpl_vars['value']['apAuthProto']],$this->_tpl_vars['clientAuthenticationTypeList']);
															$this->_tpl_vars['apAuthProtoLabel'] = $test[$this->_tpl_vars['value']['apAuthProto']];
														 ?>
														<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php if ($this->_tpl_vars['value']['apAuthProto'] == 'wep'): ?>WEP<?php else: ?><?php echo $this->_tpl_vars['apAuthProtoLabel']; ?>
<?php endif; ?><input type="hidden" name="authType" id="authType" value="<?php echo $this->_tpl_vars['apAuthProto']; ?>
"></td>
														<?php 
														$test1 =	array(	"none"	=>	"None",
										                            		"NA"	=>	"&nbsp;",
										                            		"64"	=>	"64 bit WEP",
										                            		"128"   =>  "128 bit WEP",
										                            		"152"	=>	"152 bit WEP",
										                            		"tkip"	=>	"TKIP",
										                            		"ccmp"	=>	"AES"
										                      	  );
														//print_r($this->_tpl_vars['clientEncryptionTypeList']);
															$this->_tpl_vars['apPairwiseCipher'] =array_search($test1[$this->_tpl_vars['value']['apPairwiseCipher']],$this->_tpl_vars['clientEncryptionTypeList'][$this->_tpl_vars['apAuthProto']]);
															//if (empty($this->_tpl_vars['apPairwiseCipher'])) $this->_tpl_vars['apPairwiseCipher']='64';
															$this->_tpl_vars['apPairwiseCipherLabel'] =$test1[$this->_tpl_vars['value']['apPairwiseCipher']];
														 ?>
														<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['apPairwiseCipherLabel']; ?>
<input type="hidden" name="encType" id="encType" value="<?php echo $this->_tpl_vars['apPairwiseCipher']; ?>
"></td>
														
														<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['apChannel']; ?>
</td>
													</tr>
													<?php endforeach; endif; unset($_from); ?>
												</table>
											<tr class="Alternate">
												<td colspan="2" style="text-align: right; padding: 5px;"><input type="button" value="Refresh" onclick="window.location.href='siteSurvey.php';">&nbsp;<input type="button" value="Close" onclick="window.close();"></td>
											</tr>
								</table>
							</td>
							<td class="subSectionBodyDotRight">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3" class="subSectionBottom">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>