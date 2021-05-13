<?php /* Smarty version 2.6.18, created on 2009-02-17 11:52:13
         compiled from RemoteConsole.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'RemoteConsole.tpl', 13, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Remote Console','remoteConsole')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
<?php if ($this->_tpl_vars['config']['SSH']['status']): ?>
							<?php $this->assign('sshStatus', $this->_tpl_vars['data']['remoteSettings']['sshStatus']); ?>
							<?php echo smarty_function_input_row(array('label' => "Secure Shell (SSH)",'id' => 'sshStatus','name' => $this->_tpl_vars['parentStr']['remoteSettings']['sshStatus'],'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['sshStatus'])), $this);?>

<?php endif; ?>
<?php if ($this->_tpl_vars['config']['TELNET']['status']): ?>
							<?php $this->assign('telnetStatus', $this->_tpl_vars['data']['remoteSettings']['telnetStatus']); ?>
							<?php echo smarty_function_input_row(array('label' => 'Telnet','id' => 'telnetStatus','name' => $this->_tpl_vars['parentStr']['remoteSettings']['telnetStatus'],'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['telnetStatus'])), $this);?>

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