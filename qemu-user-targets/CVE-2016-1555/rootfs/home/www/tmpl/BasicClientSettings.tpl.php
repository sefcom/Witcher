<?php /* Smarty version 2.6.18, created on 2008-09-29 11:08:52
         compiled from BasicClientSettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'BasicClientSettings.tpl', 44, false),array('function', 'ip_field', 'BasicClientSettings.tpl', 61, false),array('modifier', 'regex_replace', 'BasicClientSettings.tpl', 77, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Client Settings','clientSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody" style="padding: 0px;">
						<table class="tableStyle">
						<tr>
							<td>
								<div  id="WirelessBlock">
									<table class="inlineBlockContent" style="margin-top: 10px; width: 100%;">
										<tr>
											<td>
												<ul class="inlineTabs">
													<li id="inlineTab1" <?php if ($this->_tpl_vars['data']['activeMode'] == '2' || $this->_tpl_vars['data']['activeMode'] == '1' || $this->_tpl_vars['data']['activeMode'] == '0'): ?>class="Active" activeRadio="true"<?php else: ?> activeRadio="false"<?php endif; ?> currentId="1"><a id="inlineTabLink1" href="javascript:void(0)" onclick="if (inputForm && inputForm.formLiveValidate()) <?php echo ' { activateTab($(\'inlineTab1\'),\'1\'); } '; ?>
">802.11<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '0'): ?>Active<?php endif; ?>">b<?php if ($this->_tpl_vars['data']['activeMode'] == '0'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span>/<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '1'): ?>Active<?php endif; ?>">bg<?php if ($this->_tpl_vars['data']['activeMode'] == '1'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span>/<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '2'): ?>Active<?php endif; ?>">ng<?php if ($this->_tpl_vars['data']['activeMode'] == '2'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span></a></li>
												</ul>
											</td>
										</tr>
									</table>
								</div>
								<div id="IncludeTabBlock">
									<div  class="BlockContent" id="wlan1">
										<table class="BlockContent Trans">
											<tr class="Gray">
												<td class="DatablockLabel" style="width: 1%;">Wireless Mode</td>
												<td class="DatablockContent" style="width: 100%;">
													<span class="legendActive">2.4GHz Band</span>
													<?php 
													$this->_tpl_vars['data']['activeMode']="3";
													 ?>
													<input type="radio" style="padding: 2px;" name="<?php echo $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['operateMode']; ?>
" id="WirelessMode1" onclick="//DispChannelList(1,'0','chkRadio0');enable11nFields('hide',1);enableFields(this.value);" <?php if ($this->_tpl_vars['data']['activeMode'] == '0'): ?>checked="checked"<?php endif; ?> value="0"><span id="mode_b" <?php if ($this->_tpl_vars['data']['activeMode'] == '0'): ?>class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'>11b<img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php else: ?>>11b<?php endif; ?></span>
													<input type="radio" style="padding: 2px;" name="<?php echo $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['operateMode']; ?>
" id="WirelessMode1" onclick="//DispChannelList(1,'1','chkRadio0');enable11nFields('hide',1);enableFields(this.value);" <?php if ($this->_tpl_vars['data']['activeMode'] == '1'): ?>checked="checked"<?php endif; ?> value="1"><span id="mode_bg" <?php if ($this->_tpl_vars['data']['activeMode'] == '1'): ?>class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'>11bg<img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php else: ?>>11bg<?php endif; ?></span>
													<input type="radio" style="padding: 2px;" name="<?php echo $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['operateMode']; ?>
" id="WirelessMode1" onclick="//DispChannelList(1,'2','chkRadio0');enable11nFields('show',1);enableFields(this.value);" checked="checked" value="2"><span id="mode_ng" <?php if ($this->_tpl_vars['data']['activeMode'] == '2'): ?>class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'>11ng<img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php else: ?>>11ng<?php endif; ?></span>
													<input type="hidden" name="activeMode" id="activeMode" value="<?php echo $this->_tpl_vars['data']['activeMode']; ?>
">
													<input type="hidden" name="currentMode" id="currentMode" value="<?php echo $this->_tpl_vars['data']['activeMode']; ?>
">
													<input type="hidden" name="modeWlan0" id="modeWlan0" value="<?php if ($this->_tpl_vars['data']['activeMode'] > 2): ?>2<?php else: ?><?php echo $this->_tpl_vars['data']['activeMode']; ?>
<?php endif; ?>">
												</td>
											</tr>
											<?php $this->assign('radioStatus', $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['radioStatus']); ?>
											<?php $this->assign('operateMode', $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['operateMode']); ?>
											<?php echo smarty_function_input_row(array('row_id' => 'radioRow1','label' => 'Turn Radio On','id' => 'chkRadio0','name' => $this->_tpl_vars['parentStr']['wlanSettings']['wlanSettingTable']['wlan0']['radioStatus'],'type' => 'checkbox','value' => $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['radioStatus'],'onclick' => "setActiveMode(this,'WirelessMode1', true)"), $this);?>

											
											<tr>
												<td class="DatablockLabel">Wireless Network Name (SSID)</td>
												<td class="DatablockContent">
													<input class="input" type="text" id="wirelessSSID0" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['ssid']; ?>
" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['ssid']; ?>
" maxlength="32" validate="<?php echo 'Presence, {allowSpace: true}^Length, { minimum: 2, maximum: 32 }'; ?>
">&nbsp;
													<input name="clientMode_button" style="text-align: center;" value="Site Survey" onclick="showSurveyPopupWindow(); return false;" type="button">
												</td>
											</tr>
																																	
											<?php echo smarty_function_input_row(array('label' => 'Network Authentication','id' => 'authenticationType','name' => $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'],'type' => 'select','options' => $this->_tpl_vars['clientAuthenticationTypeList'],'selected' => $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'],'onchange' => "DisplayClientSettings(this.value);"), $this);?>


											<?php 
												$this->_tpl_vars['encTypeList'] = $this->_tpl_vars['clientEncryptionTypeList'][$this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType']];
											 ?>
											<?php echo smarty_function_input_row(array('label' => 'Data Encryption','id' => 'key_size_11g','name' => 'encryptionType','type' => 'select','options' => $this->_tpl_vars['encTypeList'],'selected' => $this->_tpl_vars['encryptionSel'],'onchange' => "if ($('authenticationType').value=='0') DisplayClientSettings('1',1);"), $this);?>


											<?php echo smarty_function_ip_field(array('id' => 'encryption','name' => $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['encryption'],'type' => 'hidden','value' => $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['encryption']), $this);?>

											<?php if (! ( $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 1 || ( $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 0 && $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['encryption'] != 0 ) )): ?>
												<?php echo smarty_function_ip_field(array('id' => 'wepKeyType','name' => $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyType'],'type' => 'hidden','value' => $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyType'],'disabled' => 'true'), $this);?>

											<?php else: ?>
												<?php echo smarty_function_ip_field(array('id' => 'wepKeyType','name' => $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyType'],'type' => 'hidden','value' => $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyType']), $this);?>

											<?php endif; ?>
											<?php if (! ( $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 1 || ( $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 0 && $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['encryption'] != 0 ) )): ?>
												<?php $this->assign('hideWepRow', "style=\"display: none;\" disabled='true'"); ?>
											<?php endif; ?>

											<?php if (! ( $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 16 || $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 32 || $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['authenticationType'] == 48 )): ?>
												<?php $this->assign('hideWPARow', "style=\"display: none;\" disabled='true'"); ?>
											<?php endif; ?>
											<tr id="wep_row" <?php echo $this->_tpl_vars['hideWepRow']; ?>
>
												<td class="DatablockLabel">Passphrase</td>
												<td class="DatablockContent">
													<input class="input" size="20" maxlength="39" id="wepPassPhrase" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepPassPhrase']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepPassPhrase'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/(.)/", '*') : smarty_modifier_regex_replace($_tmp, "/(.)/", '*')); ?>
" type="text" label="Passphrase" validate="Presence, <?php echo '{ allowQuotes: false, allowSpace: true, allowTrimmed:false }'; ?>
" onkeydown="setActiveContent();">
													<input name="szPassphrase_button" style="text-align: center;" value="Generate Keys" onclick="gen_11g_keys()" type="button">
													<input type="hidden" id="wepPassPhrase_hidden" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepPassPhrase']; ?>
">
												</td>
											</tr>
											<tr mode="1" <?php echo $this->_tpl_vars['hideWepRow']; ?>
>
												<td class="DatablockLabel">Key 1&nbsp;<input id="keyno_11g1" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo']; ?>
" value="1" <?php if ($this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo'] == '1'): ?>checked="checked"<?php endif; ?> type="radio" onclick="setActiveContent();" <?php echo $this->_tpl_vars['disableWepRow']; ?>
></td>
												<td class="DatablockContent">
													<input class="input" size="20" id="wepKey1" name="system['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey1']" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey1']; ?>
" type="text" label="WEP Key 1" validate="<?php echo 'Presence, { onlyIfChecked: \'keyno_11g1\' }^HexaDecimal,{ isMasked: \'wepKey1\' }^Length,{ isWep: true }'; ?>
" onkeydown="setActiveContent();" masked="true" onchange="this.setAttribute('masked',(this.value != '')?false:true);" onfocus="if(/^\*<?php echo '{1,}$'; ?>
/.test(this.value)) this.value='';setActiveContent();">
												</td>
											</tr>
											<tr mode="1" <?php echo $this->_tpl_vars['hideWepRow']; ?>
>
												<td class="DatablockLabel">Key 2&nbsp;<input id="keyno_11g2" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo']; ?>
" value="2" <?php if ($this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo'] == '2'): ?>checked="checked"<?php endif; ?> type="radio" onclick="setActiveContent();" <?php echo $this->_tpl_vars['disableWepRow']; ?>
></td>
												<td class="DatablockContent">
													<input class="input" size="20" id="wepKey2" name="system['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey2']" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey2']; ?>
" type="text" label="WEP Key 2" validate="<?php echo 'Presence, { onlyIfChecked: \'keyno_11g2\' }^HexaDecimal,{ isMasked: \'wepKey2\' }^Length,{ isWep: true }'; ?>
" onkeydown="setActiveContent();" masked="true" onchange="this.setAttribute('masked',(this.value != '')?false:true);" onfocus="if(/^\*<?php echo '{1,}$'; ?>
/.test(this.value)) this.value='';setActiveContent();">
												</td>
											</tr>
											<tr mode="1" <?php echo $this->_tpl_vars['hideWepRow']; ?>
>
												<td class="DatablockLabel">Key 3&nbsp;<input id="keyno_11g3" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo']; ?>
" value="3" <?php if ($this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo'] == '3'): ?>checked="checked"<?php endif; ?> type="radio" onclick="setActiveContent();" <?php echo $this->_tpl_vars['disableWepRow']; ?>
></td>
												<td class="DatablockContent">
													<input class="input" size="20" id="wepKey3" name="system['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey3']" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey3']; ?>
" type="text" label="WEP Key 3" validate="<?php echo 'Presence, { onlyIfChecked: \'keyno_11g3\' }^HexaDecimal,{ isMasked: \'wepKey3\' }^Length,{ isWep: true }'; ?>
" onkeydown="setActiveContent();" masked="true" onchange="this.setAttribute('masked',(this.value != '')?false:true);" onfocus="if(/^\*<?php echo '{1,}$'; ?>
/.test(this.value)) this.value='';setActiveContent();">
												</td>
											</tr>
											<tr mode="1" <?php echo $this->_tpl_vars['hideWepRow']; ?>
>
												<td class="DatablockLabel">Key 4&nbsp;<input id="keyno_11g4" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo']; ?>
" value="4" <?php if ($this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKeyNo'] == '4'): ?>checked="checked"<?php endif; ?> type="radio" onclick="setActiveContent();" <?php echo $this->_tpl_vars['disableWepRow']; ?>
></td>
												<td class="DatablockContent">
													<input class="input" size="20" id="wepKey4" name="system['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey4']" value="<?php echo $this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['wepKey4']; ?>
" type="text" label="WEP Key 4" validate="<?php echo 'Presence, { onlyIfChecked: \'keyno_11g4\' }^HexaDecimal,{ isMasked: \'wepKey4\' }^Length,{ isWep: true }'; ?>
" onkeydown="setActiveContent();" masked="true" onchange="this.setAttribute('masked',(this.value != '')?false:true);" onfocus="if(/^\*<?php echo '{1,}$'; ?>
/.test(this.value)) this.value='';setActiveContent();">
												</td>
											</tr>
											<tr id="wpa_row" <?php echo $this->_tpl_vars['hideWPARow']; ?>
>
												<td class="DatablockLabel">WPA Passphrase (Network Key)</td>
												<td class="DatablockContent">
													<input id="wpa_psk" class="input" size="28" maxlength="63" name="<?php echo $this->_tpl_vars['parentStr']['staSettings']['staSettingTable']['wlan0']['sta0']['presharedKey']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['staSettings']['staSettingTable']['wlan0']['sta0']['presharedKey'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/(.)/", '*') : smarty_modifier_regex_replace($_tmp, "/(.)/", '*')); ?>
" type="text" label="WPA Passphrase (Network Key)" validate="Presence,<?php echo ' { allowQuotes: false, allowSpace: true, allowTrimmed: false }'; ?>
^Length,<?php echo '{minimum: 8}'; ?>
" onkeydown="setActiveContent();">
												</td>
											</tr>

										</table>
									</div>
								</div>
							</div>
						</td>
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
  <div id="layeredWin" style="display: none; margin: auto; width: 70%;text-align: center;">
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
												<?php $_from = $this->_tpl_vars['data']['monitor']['apScanList']['wlan0']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['profiles'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['profiles']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
        $this->_foreach['profiles']['iteration']++;
?>
												<tr <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?> id="row_<?php echo $this->_foreach['profiles']['iteration']; ?>
">
													<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?> style="width: 5%;"><input type="button"  id="ApScanId" name="profileid0" value="Select" onclick="window.opener.copyAPDetails(this.parentNode.parentNode);"></td>
													<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['ssid']; ?>
</td>
													<?php 
														$this->_tpl_vars['authType'] = $this->_tpl_vars['clientAuthenticationTypeList'][$this->_tpl_vars['value']['authenticationType']];
													 ?>
													<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['authType']; ?>
<input type="hidden" name="authType" value="<?php echo $this->_tpl_vars['value']['authenticationType']; ?>
"</td>
													<?php 
													//print_r($this->_tpl_vars['clientEncryptionTypeList']);
														$this->_tpl_vars['encType'] = $this->_tpl_vars['clientEncryptionTypeList'][$this->_tpl_vars['value']['authenticationType']][$this->_tpl_vars['value']['encryptionType']];
													 ?>
													<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['encType']; ?>
<input type="hidden" name="encType" value="<?php echo $this->_tpl_vars['value']['encryptionType']; ?>
"</td>
													
													<td <?php if ($this->_foreach['profiles']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['channel']; ?>
</td>
												</tr>
												<?php endforeach; endif; unset($_from); ?>
											</table>
										<tr class="Alternate">
											<td colspan="2" style="text-align: right; padding: 5px;"><input type="button" value="Close" onclick="window.close();"></td>
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
</div>

    <script language="javascript">
	<!--
		var ChannelList_0 = <?php echo $this->_tpl_vars['ChannelList_0']; ?>
; //11b
		var ChannelList_1 = <?php echo $this->_tpl_vars['ChannelList_1']; ?>
; //11bg
		
		var ChannelList_0_20 = <?php echo $this->_tpl_vars['ChannelList_0_20']; ?>
;
		var ChannelList_1_20 = <?php echo $this->_tpl_vars['ChannelList_1_20']; ?>
;
		
		<?php if ($this->_tpl_vars['data']['activeMode'] != '2'): ?>
			enable11nFields('hide',1);
		<?php else: ?>
			enable11nFields('show',1);
		<?php endif; ?>
		
		<?php echo 'window.onload=function changeHelp() {$(\'helpURL\').value=$(\'helpURL\').value+\'_g\';};
		'; ?>

			
		<?php if ($this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] != 0): ?>
			var disableChannelonWDS0 = true;
		<?php endif; ?>
	
		toggleDisplay('<?php echo $this->_tpl_vars['interface']; ?>
');
	-->
	</script>