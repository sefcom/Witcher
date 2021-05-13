<?php /* Smarty version 2.6.18, created on 2009-03-02 06:07:19
         compiled from AdvancedSyslog.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'AdvancedSyslog.tpl', 12, false),array('modifier', 'replace', 'AdvancedSyslog.tpl', 14, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Syslog Settings','syslogSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php $this->assign('syslogStatus', $this->_tpl_vars['data']['logSettings']['syslogStatus']); ?>
							<?php echo smarty_function_input_row(array('label' => 'Enable Syslog','id' => 'enableSyslog','name' => $this->_tpl_vars['parentStr']['logSettings']['syslogStatus'],'type' => 'checkbox','value' => '1','selectCondition' => "==".($this->_tpl_vars['syslogStatus']),'onclick' => "toggleSyslog(this);"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Syslog Server IP Address','id' => 'syslogSrvIp','name' => $this->_tpl_vars['parentStr']['logSettings']['syslogSrvIp'],'type' => 'ipfield','value' => ((is_array($_tmp=$this->_tpl_vars['data']['logSettings']['syslogSrvIp'])) ? $this->_run_mod_handler('replace', true, $_tmp, '0.0.0.0', '') : smarty_modifier_replace($_tmp, '0.0.0.0', '')),'size' => '16','maxlength' => '15','masked' => 'true','onchange' => "this.setAttribute('masked',(this.value != '')?false:true)",'validate' => "IpAddress, (( allowZero: false ))"), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Port Number','id' => 'apName','name' => $this->_tpl_vars['parentStr']['logSettings']['syslogSrvPort'],'type' => 'text','value' => $this->_tpl_vars['data']['logSettings']['syslogSrvPort'],'size' => '5','maxlength' => '5','validate' => "Numericality, (( minimum:1, maximum: 65535, onlyInteger: true ))^Presence"), $this);?>

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
		toggleSyslog($('cb_enableSyslog'));
		<?php if (( $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] == 5 )): ?>
			Form.disable(document.dataForm);
		<?php endif; ?>
	-->
	</script>