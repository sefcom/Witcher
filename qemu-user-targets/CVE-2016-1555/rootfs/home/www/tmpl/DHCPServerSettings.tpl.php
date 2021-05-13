<?php /* Smarty version 2.6.18, created on 2009-06-23 06:31:56
         compiled from DHCPServerSettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'DHCPServerSettings.tpl', 28, false),array('modifier', 'replace', 'DHCPServerSettings.tpl', 40, false),)), $this); ?>
<script language="javascript">
<!--
<?php echo '
	var spaceMask=/^\\s{0,}$/g
'; ?>

-->
</script>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('DHCP Server Settings','dhcpServerConfiguration')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php $this->assign('dhcpServerStatus', $this->_tpl_vars['data']['dhcpsSettings']['dhcpServerStatus']); ?>
<?php if ($this->_tpl_vars['config']['WNDAP330']['status']): ?>
                            <?php if ($this->_tpl_vars['data']['httpRedirectSettings']['httpRedirectStatus'] == '1'): ?>
								<?php $this->assign('onclickStr', "displayHotspotError(this);"); ?>
							<?php else: ?>
								<?php $this->assign('onclickStr', "graysomething(this,true);"); ?>
							<?php endif; ?>
<?php else: ?>
                            <?php $this->assign('onclickStr', "graysomething(this,true);"); ?>
<?php endif; ?>
							<?php echo smarty_function_input_row(array('label' => 'DHCP Server','id' => 'dhcpServerStatus','name' => "system[dhcpsSettings][dhcpServerStatus]",'type' => 'radio','options' => "1-Enable,0-Disable",'onclick' => ($this->_tpl_vars['onclickStr']),'selectCondition' => "==".($this->_tpl_vars['dhcpServerStatus'])), $this);?>

<?php if ($this->_tpl_vars['config']['MBSSID']['status']): ?>
							<?php echo smarty_function_input_row(array('label' => 'DHCP Server VLAN ID','id' => 'dhcpsVlanId','name' => "system[dhcpsSettings][dhcpsVlanId]",'type' => 'text','value' => $this->_tpl_vars['data']['dhcpsSettings']['dhcpsVlanId'],'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "Numericality, (( minimum:1, maximum: 4094, onlyInteger: true ))^Presence"), $this);?>

<?php endif; ?>
							<?php echo smarty_function_input_row(array('label' => 'Starting IP Address','id' => 'dhcpsIpStart','name' => "system[dhcpsSettings][dhcpsIpStart]",'type' => 'ipfield','value' => $this->_tpl_vars['data']['dhcpsSettings']['dhcpsIpStart'],'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))^Presence"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Ending IP Address','id' => 'dhcpsIpEnd','name' => "system[dhcpsSettings][dhcpsIpEnd]",'type' => 'ipfield','value' => $this->_tpl_vars['data']['dhcpsSettings']['dhcpsIpEnd'],'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))^Presence"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Subnet Mask','id' => 'dhcpsNetMask','name' => "system[dhcpsSettings][dhcpsNetMask]",'type' => 'ipfield','value' => $this->_tpl_vars['data']['dhcpsSettings']['dhcpsNetMask'],'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( onlyNetMask: true ))^Presence"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Gateway IP Address','id' => 'dhcpsGateway','name' => "system[dhcpsSettings][dhcpsGateway]",'type' => 'ipfield','value' => $this->_tpl_vars['data']['dhcpsSettings']['dhcpsGateway'],'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))^Presence"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Primary DNS Server','id' => 'dhcpsPriDns','name' => "system[dhcpsSettings][dhcpsPriDns]",'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['dhcpsSettings']['dhcpsPriDns'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Secondary DNS Server','id' => 'dhcpsSndDns','name' => "system[dhcpsSettings][dhcpsSndDns]",'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['dhcpsSettings']['dhcpsSndDns'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Primary WINS Server','id' => 'dhcpsPriWins','name' => "system[dhcpsSettings][dhcpsPriWins]",'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['dhcpsSettings']['dhcpsPriWins'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Secondary WINS Server','id' => 'dhcpsSndWins','name' => "system[dhcpsSettings][dhcpsSndWins]",'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['dhcpsSettings']['dhcpsSndWins'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'disableCondition' => "1!=".($this->_tpl_vars['dhcpServerStatus']),'validate' => "IpAddress, (( allowZero: false ))"), $this);?>


							<tr>
								<td class="DatablockLabel">Lease</td>
								<td class="DatablockContent">
									<input class="input" size="2" maxlength="2" id="dhcpsLeaseDays" label="Lease Days" value="" type="text" onblur="convertLeaseTime2Seconds()" <?php if ($this->_tpl_vars['dhcpServerStatus'] != 1): ?>disabled="disabled"<?php endif; ?> validate="Numericality^Presence" onkeydown="setActiveContent();"> <small>days</small>
									<input class="input" size="2" maxlength="2" id="dhcpsLeaseHours" label="Lease Hours" value="" type="text" onblur="convertLeaseTime2Seconds()" <?php if ($this->_tpl_vars['dhcpServerStatus'] != 1): ?>disabled="disabled"<?php endif; ?> validate="Numericality, <?php echo '{ minimum:0, maximum: 23, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();"> <small>hours</small>
									<input class="input" size="2" maxlength="2" id="dhcpsLeaseMinutes" label="Lease Minutes" value="" type="text" onblur="convertLeaseTime2Seconds()" <?php if ($this->_tpl_vars['dhcpServerStatus'] != 1): ?>disabled="disabled"<?php endif; ?> validate="Numericality, <?php echo '{ minimum:0, maximum: 59, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();"> <small>minutes</small>
									<input type="hidden" name="<?php echo $this->_tpl_vars['parentStr']['dhcpsSettings']['dhcpsLeaseTime']; ?>
" id="dhcpsLeaseTime" value="<?php echo $this->_tpl_vars['data']['dhcpsSettings']['dhcpsLeaseTime']; ?>
">
									<script type="text/javascript">
									<!--
										$('dhcpsLeaseDays').value = convertLeaseTime($('dhcpsLeaseTime').value,'days');
										$('dhcpsLeaseHours').value = convertLeaseTime($('dhcpsLeaseTime').value,'hours');
										$('dhcpsLeaseMinutes').value = convertLeaseTime($('dhcpsLeaseTime').value,'minutes');
									-->
									</script>
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
	<script language="javascript">
	<!--
	<?php if (( $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] == 5 )): ?>
		Form.disable(document.dataForm);
	<?php endif; ?>
	-->
	</script>