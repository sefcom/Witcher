<?php /* Smarty version 2.6.18, created on 2009-10-27 06:40:50
         compiled from SNMP.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'SNMP.tpl', 13, false),array('modifier', 'replace', 'SNMP.tpl', 21, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('SNMP Settings','snmpSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">	
								
							<?php $this->assign('snmpStatus', $this->_tpl_vars['data']['remoteSettings']['snmpStatus']); ?>
							<?php echo smarty_function_input_row(array('label' => 'SNMP','id' => 'snmpStatus','name' => $this->_tpl_vars['parentStr']['remoteSettings']['snmpStatus'],'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['snmpStatus']),'onclick' => "checkTR069Status(this, true)"), $this);?>


							<?php echo smarty_function_input_row(array('label' => "Read-Only Community Name",'id' => 'readOnlyCommunity','name' => $this->_tpl_vars['parentStr']['remoteSettings']['readOnlyCommunity'],'type' => 'text','value' => $this->_tpl_vars['data']['remoteSettings']['readOnlyCommunity'],'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'size' => '32','maxlength' => '31','validate' => "Presence^AlphaNumericWithHU"), $this);?>
 
							
							<?php echo smarty_function_input_row(array('label' => "Read-Write Community Name",'id' => 'readWriteCommunity','name' => $this->_tpl_vars['parentStr']['remoteSettings']['readWriteCommunity'],'type' => 'text','value' => $this->_tpl_vars['data']['remoteSettings']['readWriteCommunity'],'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'size' => '32','maxlength' => '31','validate' => "Presence^AlphaNumericWithHU"), $this);?>
 
							
							<?php echo smarty_function_input_row(array('label' => 'Trap Community Name','id' => 'trapServerCommunity','name' => $this->_tpl_vars['parentStr']['remoteSettings']['trapServerCommunity'],'type' => 'text','value' => $this->_tpl_vars['data']['remoteSettings']['trapServerCommunity'],'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'size' => '32','maxlength' => '31','validate' => "Presence^AlphaNumericWithHU"), $this);?>
 
							
							<?php echo smarty_function_input_row(array('label' => 'IP Address to Receive Traps','id' => 'trapServerIP','name' => $this->_tpl_vars['parentStr']['remoteSettings']['trapServerIP'],'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['remoteSettings']['trapServerIP'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'validate' => "IpAddress, (( allowZero: false ))"), $this);?>
 
<?php if ($this->_tpl_vars['config']['TRAP_PORT_CONFIG']['status']): ?>
							<?php echo smarty_function_input_row(array('label' => 'Trap Port','id' => 'trapPort','name' => $this->_tpl_vars['parentStr']['remoteSettings']['trapPort'],'type' => 'text','value' => $this->_tpl_vars['data']['remoteSettings']['trapPort'],'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'size' => '5','maxlength' => '5','validate' => "Numericality, (( minimum:1, maximum: 65535, onlyInteger: true ))^Presence"), $this);?>

<?php endif; ?>
<?php if ($this->_tpl_vars['config']['MANAGERIP_CONFIG']['status']): ?>
							<?php echo smarty_function_input_row(array('label' => 'SNMP Manager IP','id' => 'managerIP','name' => $this->_tpl_vars['parentStr']['remoteSettings']['managerIP'],'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['remoteSettings']['managerIP'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'disableCondition' => "0==".($this->_tpl_vars['snmpStatus']),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'validate' => "IpAddress, (( allowZero: false , allowBcastAll: true ))"), $this);?>

<?php endif; ?>
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
	<script language="javascript">
	<!--
	<?php if ($this->_tpl_vars['config']['TR69']['status']): ?>
					<?php if ($this->_tpl_vars['data']['tr069CpeConfiguration']['tr069Status'] == '1'): ?>
									     var tr069OnStatus=true;
				<?php endif; ?>
			
	<?php endif; ?>		
	-->
	</script>