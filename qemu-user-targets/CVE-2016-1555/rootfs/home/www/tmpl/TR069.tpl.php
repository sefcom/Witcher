<?php /* Smarty version 2.6.18, created on 2009-11-13 05:19:01
         compiled from TR069.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'TR069.tpl', 13, false),)), $this); ?>
<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('TR 069 Settings','tr069settings')</script></td>
					
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
					<table class="tableStyle">
							<?php $this->assign('tr069Status', $this->_tpl_vars['data']['tr069CpeConfiguration']['tr069Status']); ?>
							<?php echo smarty_function_input_row(array('label' => 'TR 069','id' => 'tr069Status','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['tr069Status'],'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['tr069Status']),'onclick' => "checkSNMPStatus(this, true)"), $this);?>


							<tr>
								<td class="spacerHeight21"></td>
							</tr>														
							<?php echo smarty_function_input_row(array('label' => 'ACS URL','id' => 'acsurl','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['acsURL'],'type' => 'text','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['acsURL'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '32','maxlength' => '128','validate' => "Presence^IpAddress, (( allowURL: true, allowZero: false, allowGeneric: false ))"), $this);?>

							<?php echo smarty_function_input_row(array('label' => 'ACS User Name','id' => 'acsusrname','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['acsUserName'],'type' => 'text','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['acsUserName'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '32','maxlength' => '32','validate' => "Presence^AlphaNumericWithHU"), $this);?>

							<?php echo smarty_function_input_row(array('label' => 'ACS Password','id' => 'acspasswd','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['acsPassword'],'type' => 'password','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['acsPassword'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '32','maxlength' => '32','validate' => "Presence, (( onlyIfChecked: 'dummy' ))^Ascii"), $this);?>

							<tr>
								<td class="spacerHeight21"></td>
							</tr>
							<?php $this->assign('periodicInformEnable', $this->_tpl_vars['data']['tr069CpeConfiguration']['periodicInformEnable']); ?>
							<?php echo smarty_function_input_row(array('label' => 'Periodic Inform','id' => 'prdinfostatus','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['periodicInformEnable'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['periodicInformEnable'])), $this);?>

							
							<?php echo smarty_function_input_row(array('label' => 'Periodic Inform Interval','id' => 'prdInfoInterval','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['periodicInformInterval'],'type' => 'text','class' => 'input','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['periodicInformInterval'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '10','maxlength' => '10','validate' => "Numericality, (( minimum:1, maximum: 4294967295, onlyInteger: true ))^Presence"), $this);?>

							<?php echo smarty_function_input_row(array('label' => 'Periodic Inform Time','id' => 'prdinfotime','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['periodicInformTime'],'type' => 'text','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['periodicInformTime'],'size' => '32','maxlength' => '32','validate' => "Presence^Ascii",'disableCondition' => "0==".($this->_tpl_vars['tr069Status'])), $this);?>
							
							
							<tr>
								<td class="spacerHeight21"></td>
							</tr>							
							<?php echo smarty_function_input_row(array('label' => 'Connection Request User Name','id' => 'conReqsername','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['connectionRequestUserName'],'type' => 'text','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['connectionRequestUserName'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '32','maxlength' => '32','validate' => "Presence^AlphaNumericWithHU"), $this);?>

							<?php echo smarty_function_input_row(array('label' => 'Connection Request User Password','id' => 'conreqserpwd','name' => $this->_tpl_vars['parentStr']['tr069CpeConfiguration']['connectionRequestUserPassword'],'type' => 'password','value' => $this->_tpl_vars['data']['tr069CpeConfiguration']['connectionRequestUserPassword'],'disableCondition' => "0==".($this->_tpl_vars['tr069Status']),'size' => '32','maxlength' => '32','validate' => "Presence, (( onlyIfChecked: 'dummy' ))^Ascii"), $this);?>

	
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
	<tr>
		<td class="spacerHeight21"></td>
	</tr>
	
	<script language="javascript">
	<!--
	<?php if ($this->_tpl_vars['config']['SNMP']['status']): ?>
					<?php if ($this->_tpl_vars['data']['remoteSettings']['snmpStatus'] == '1'): ?>
									     var snmpOnStatus=true;
				<?php endif; ?>
			
	<?php endif; ?>		
	-->
	</script>