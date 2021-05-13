<?php /* Smarty version 2.6.18, created on 2009-06-23 06:32:10
         compiled from AdvancedHotspot.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'AdvancedHotspot.tpl', 21, false),array('modifier', 'replace', 'AdvancedHotspot.tpl', 23, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Hotspot Settings','hotspotSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php $this->assign('httpRedirectStatus', $this->_tpl_vars['data']['httpRedirectSettings']['httpRedirectStatus']); ?>
<?php if ($this->_tpl_vars['config']['WNDAP330']['status']): ?>
                            <?php if ($this->_tpl_vars['data']['dhcpsSettings']['dhcpServerStatus'] == '0'): ?>
								<?php $this->assign('onclickStr', "displayDHCPSError(this);"); ?>
							<?php else: ?>
								<?php $this->assign('onclickStr', "$('httpRedirectURL').disabled=(this.value==1?false:true);"); ?>
							<?php endif; ?>
<?php else: ?>
                            <?php $this->assign('onclickStr', "$('httpRedirectURL').disabled=(this.value==1?false:true);"); ?>
<?php endif; ?>
							<?php echo smarty_function_input_row(array('label' => 'HTTP Redirect','id' => 'httpRedirectStatus','name' => $this->_tpl_vars['parentStr']['httpRedirectSettings']['httpRedirectStatus'],'type' => 'radio','options' => "1-Enable,0-Disable",'onclick' => ($this->_tpl_vars['onclickStr']),'selectCondition' => "==".($this->_tpl_vars['httpRedirectStatus'])), $this);?>


							<?php echo smarty_function_input_row(array('label' => 'Redirect URL','id' => 'httpRedirectURL','name' => $this->_tpl_vars['parentStr']['httpRedirectSettings']['httpRedirectURL'],'type' => 'text','value' => ((is_array($_tmp=$this->_tpl_vars['data']['httpRedirectSettings']['httpRedirectURL'])) ? $this->_run_mod_handler('replace', true, $_tmp, '\\', '') : smarty_modifier_replace($_tmp, '\\', '')),'size' => '25','maxlength' => '120','disableCondition' => "1!=".($this->_tpl_vars['httpRedirectStatus']),'validate' => "IpAddress, (( allowURL: true, allowZero: false, allowGeneric: false ))^Presence"), $this);?>

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