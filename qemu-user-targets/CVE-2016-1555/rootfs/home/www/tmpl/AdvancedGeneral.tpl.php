<?php /* Smarty version 2.6.18, created on 2009-03-02 06:07:16
         compiled from AdvancedGeneral.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'AdvancedGeneral.tpl', 13, false),array('function', 'ip_field', 'AdvancedGeneral.tpl', 40, false),)), $this); ?>
<?php if ($this->_tpl_vars['config']['STP']['status']): ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Spanning Tree Protocol','spanningTreeProtocol')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php $this->assign('spanTreeStatus', $this->_tpl_vars['data']['basicSettings']['spanTreeStatus']); ?>
							<?php echo smarty_function_input_row(array('label' => 'Spanning Tree Protocol','id' => 'chkSTP','name' => $this->_tpl_vars['parentStr']['basicSettings']['spanTreeStatus'],'type' => 'radio','options' => "1-Enable,0-Disable",'selectCondition' => "==".($this->_tpl_vars['spanTreeStatus'])), $this);?>

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
	<tr>
		<td class="spacerHeight21"></td>
	</tr>
<?php endif; ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('802.1Q VLAN','802_1QVLAN')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<tr>
								<?php $this->assign('untaggedVlanStatus', $this->_tpl_vars['data']['basicSettings']['untaggedVlanStatus']); ?>
								<td class="DatablockLabel"><?php echo smarty_function_ip_field(array('id' => 'untaggedvlan','name' => $this->_tpl_vars['parentStr']['basicSettings']['untaggedVlanStatus'],'type' => 'checkbox','value' => '1','selectCondition' => "==".($this->_tpl_vars['untaggedVlanStatus']),'onclick' => "fetchObjectById('untaggedvlanid').disabled=!this.checked;"), $this);?>
&nbsp;Untagged VLAN</td>
								<td class="DatablockContent"><input class="input" id="untaggedvlanid" name="<?php echo $this->_tpl_vars['parentStr']['basicSettings']['untaggedVlanID']; ?>
" value="<?php echo $this->_tpl_vars['data']['basicSettings']['untaggedVlanID']; ?>
" size="6" maxlength="4" type="text" <?php if ($this->_tpl_vars['data']['basicSettings']['untaggedVlanStatus'] != 1): ?>disabled="disabled"<?php endif; ?> label="Untagged VLAN" onkeydown="setActiveContent();" validate="Presence^Numericality,<?php echo '{ minimum:1, maximum: 4094, onlyInteger: true }'; ?>
"></td>
							</tr>
							<?php echo smarty_function_input_row(array('label' => 'Management VLAN','id' => 'mgmtvlan','name' => $this->_tpl_vars['parentStr']['basicSettings']['managementVlanID'],'type' => 'text','class' => 'input','value' => $this->_tpl_vars['data']['basicSettings']['managementVlanID'],'size' => '6','maxlength' => '4','validate' => "Numericality, (( minimum:1, maximum: 4094, onlyInteger: true ))^Presence"), $this);?>

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
	
	<script language="javascript">
	<!--
			<?php if ($this->_tpl_vars['config']['CLIENT']['status']): ?>
				<?php if (( $this->_tpl_vars['config']['TWOGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] == 5 ) || ( $this->_tpl_vars['config']['FIVEGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['apMode'] == 5 )): ?>
					Form.disable(document.dataForm);
				<?php endif; ?>
			<?php endif; ?>
			
	-->
	</script>
	