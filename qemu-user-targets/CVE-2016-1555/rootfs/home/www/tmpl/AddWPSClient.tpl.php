<?php /* Smarty version 2.6.18, created on 2009-12-24 06:58:36
         compiled from AddWPSClient.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'AddWPSClient.tpl', 176, false),)), $this); ?>
	<tr id="1" style="display:none">
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Add WPS Client','addwpsclient')</script></td>
				</tr>
				
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody" style="text-align: left";>
									<p class="font10Bold"><b>New and easy way to connect to the Wireless Access Point via<br> WiFi Protected Setup(WPS)</B></p><br>
									<p class="font10">A wireless client has to support WPS function in order to use this wizard to add the client to your WPS enabled Wireless Access Point.</p>
									<p class="font10">Please check the user manual and giftbox of your wireless client to see whether it supports the WPS function.</p>
									<p class="font10">If your wireless client does not support the WPS function,you have to configure your wireless client manually so it has the same SSID and wireless security settings as on this access point.</p>
								<table class="tableStyle">
									<tr>
										<td class="DatablockContent" style="text-align: center;">	
											<input type="submit" name="NEXT" id="next" value="Next" onclick="setWPSWizardPage(2);return false;" <?php if ($this->_tpl_vars['data']['wpsSettings']['wpsDisable'] == '1'): ?>disabled="disabled"<?php endif; ?>>&nbsp;&nbsp;
										</td>
									</tr>
								</table>
					</td>
					<td class="subSectionBodyDotRight">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="subSectionBottom"></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr id="2" style="display:none">
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Add WPS Client','addWPSClient')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody style="text-align:left">
						<table class="tableStyle" style="text-align:left">
						<p class="font10Bold" align="left">Select a Setup Method</p><br>
							<div align = "left" class = "font10Bold"><input type="radio" id="wpspushbutton" name="wpsButton" onclick="$('pushbutton').style.display='';$('pinbutton').style.display='none';$('pusBtnImg').focus();" <?php if ($this->_tpl_vars['data']['monitor']['wpsSoftwarePushButtonSupport'] == '1'): ?>checked<?php endif; ?>>Push Button(Recommended)</div>
							
							<div id="pushbutton">
							<P class="font10" align="left">You can either press the Push Button physically on the access point or <br>press the Button below(Soft Push Button).<br>
							&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<INPUT title="Push Button" TYPE="image" id ="pusBtnImg" SRC="images/pushButton_On.gif" BORDER="0" ALT="PUSH BUTTON" onclick="setWPSWizardPage(3,1,0)"></p>
							</div>
							
							<div class = "font10Bold" align = "left"><input type="radio" id="wpspinbutton" name="wpsButton" onclick="$('pinbutton').style.display='';$('pushbutton').style.display='none';$('wpsPin').focus()" <?php if ($this->_tpl_vars['data']['monitor']['wpsPinSupport'] == '1' && $this->_tpl_vars['data']['monitor']['wpsSoftwarePushButtonSupport'] == '0'): ?>checked<?php endif; ?>>PIN(Personal Identification Number)</div>
							<div id="pinbutton">
							<P class="font10" align = "left">This is the security PIN of the WPS client.While connecting,WPS-enabled adapters provide a randomly-generated security PIN.</p>
							<div align = "left"><font class = "font10Bold">Enter Client's PIN:</font><input type="text" id="wpsPin" name="wpspin" onKeyDown="KeyDownHandler()">&nbsp&nbsp<input id="pinnum" type="button" value="Next" onclick="validateWPSPIN(3);"></div>
							</div>
						</table>
					</td>
					<td class="subSectionBodyDotRight">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="subSectionBottom"></td>
				</tr>
			</table>
		</td>
	</tr>
		
	<tr id="3" style="display:none">
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Connecting to New Wireless Client','connectwirelessclient')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
								<tr>
									<td align="center">
										<table>
											<tr>
												<td colspan=2>
													<div id="showbarValue" style="font-size:10pt;padding:2px;black 1px;text-align: left"></div>
												</td>
											</tr>		
											<tr>
												<td>
													<div id="wpsonoffimage"><img src="images/pushButton_On.gif"</div><td><div id="showbar" style="font-size:8pt;padding:2px;border:solid black 1px;"></div>
												</td>
											</tr>		
									</td>
								</tr>
							</table>
							<tr>
								<td align="center">
									<input type="button" id="wpsCancelButton" name="wpsCancelButton" value="Cancel" onClick="updateCanceToBackend()"><input style="height:25px;width:25px" type="button" id="WpsSuccessOkButton" name="WpsSuccessOkButton" value="OK" onclick="setWPSWizardPage(1,0,0)">
								</td>
							</tr>
			</table>
		</td>
				<td class="subSectionBodyDotRight">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="subSectionBottom"></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr id="4" style="display:none">
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Failure','failure')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
						<tr>
							<td><div class="font10Bold" id="wpsfailureredmsg" style="color:red;padding:2px;black 1px;text-align: center"></div></td></tr>
							<td><div class="font10Bold" id="wpsfailuremsg" style="padding:2px;black 1px;text-align: center"></div></td></tr>
							<tr><td style="text-align: center";><br><p class="font10">Click OK to go back to the Wi-Fi Protected Setup page...</p>
							<tr><td align="center"><input type="button" style="height:25px;width:25px" id="okBtn4" value="OK" onclick="setWPSWizardPage(1,0,0)"</td></tr>
						</tr>

						</table>
					</td>
					<td class="subSectionBodyDotRight">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="subSectionBottom"></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr id="5" style="display:none">
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Success','success')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
						<tr>
							<td><div class="font10Bold" id="wpssuccessmsg" style="padding:2px;black 1px;text-align: center"></div></td></tr>
							<td><div class="font10" id="wpssuccesssetup" style="padding:2px;black 1px;text-align: center"></div></td></tr>						
							<tr><td align="center"><input type="button" style="height:25px;width:25px" id="okBtn5" value="OK" onclick="setWPSSuccesspage()"</td></tr>
						</tr>

						</table>
					</td>
					<td class="subSectionBodyDotRight">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="subSectionBottom"></td>
				</tr>
			</table>
		</td>
	</tr>
	

    <input type="hidden" name="bridgingStatus" id="bridgingStatus" value="<?php echo $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode']; ?>
">
    <input type="hidden" name="whichwpsfailmsg" id="whichwpsfailmsg">
    <input type="hidden" name="wizardPageID" id="wizardPageID">
    <input type="hidden" name="whichWPSMethod" id="whichWPSMethod">
    <input type="hidden" name="userPinValue" id="userPinValue">
    <input type="hidden" name="pushButtonStatus" id="pushButtonStatus" value=<?php echo $this->_tpl_vars['data']['monitor']['wpsSoftwarePushButtonSupport']; ?>
>
	<input type="hidden" name="pinButtonStatus" id="pinButtonStatus" value=<?php echo $this->_tpl_vars['data']['monitor']['wpsPinSupport']; ?>
>
<script language="javascript">
	<!--

	toggleWPSButtons()
	createWPSProgressBar();
    var wizardPage = <?php echo ((is_array($_tmp=@$_POST['wizardPageID'])) ? $this->_run_mod_handler('default', true, $_tmp, "''") : smarty_modifier_default($_tmp, "''")); ?>
;
    var wpsMethod = <?php echo ((is_array($_tmp=@$_POST['whichWPSMethod'])) ? $this->_run_mod_handler('default', true, $_tmp, "''") : smarty_modifier_default($_tmp, "''")); ?>
;
    var wpsfailmsg = <?php echo ((is_array($_tmp=@$_POST['whichwpsfailmsg'])) ? $this->_run_mod_handler('default', true, $_tmp, "''") : smarty_modifier_default($_tmp, "''")); ?>
;
    var userPinVal = '<?php echo ((is_array($_tmp=@$_POST['userPinValue'])) ? $this->_run_mod_handler('default', true, $_tmp, "") : smarty_modifier_default($_tmp, "")); ?>
';

    var _timer = 0;
	window.top.frames['action'].$('standardButtons').hide();
	<?php echo '
	if (wizardPage != \'\') {
		loadWPSWizardPage(wizardPage,wpsMethod,wpsfailmsg,userPinVal);
	    //loadWPSWizardPage(\'3\');
	}
	else{
		loadWPSWizardPage(\'1\',wpsMethod);
		}
        window.onload=function setDefaultButton(){
            if (wizardPage == \'\'){
                if($(\'next\').disabled == false)
                    $(\'next\').focus();
            }
            else if(wizardPage == \'2\'){
                $(\'pusBtnImg\').focus();
            }
            else if(wizardPage == \'3\'){
            $(\'wpsCancelButton\').focus();
            }
            else if(wizardPage == \'4\'){
            $(\'okBtn4\').focus();
            }
            else if(wizardPage == \'5\'){
            $(\'okBtn5\').focus();
            }
        };

        function KeyDownHandler(){
            if(event.keyCode == 13){
                validateWPSPIN(3);
            }
        }
	'; ?>

        <?php echo '
        if(($(\'errorMessageBlock\').style.display != \'none\') && ($(\'br_head\').innerHTML == \'Wireless Radio is turned off!\')){
            Form.disable(document.dataForm);
        }

        if(($(\'bridgingStatus\').value == \'1\') || ($(\'bridgingStatus\').value == \'4\') || ($(\'bridgingStatus\').value == \'5\')){
            Form.disable(document.dataForm);
        }
        '; ?>


	-->
	
</script>