<?php /* Smarty version 2.6.18, created on 2008-12-10 08:58:15
         compiled from AdvancedEthernet.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'AdvancedEthernet.tpl', 12, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Ethernet','ethernet')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
										<?php $this->assign('configType', $this->_tpl_vars['data']['ethernetSettings']['configType']); ?>
										<?php echo smarty_function_input_row(array('label' => 'Configuration Type','id' => 'configType','name' => $this->_tpl_vars['parentStr']['ethernetSettings']['configType'],'type' => 'radio','options' => "0-Auto,1-Manual",'selectCondition' => "==".($this->_tpl_vars['configType']),'onclick' => "$('speed').disabled=(this.value==1?false:true);"), $this);?>

										
										<?php echo smarty_function_input_row(array('label' => 'Speed','id' => 'speed','name' => $this->_tpl_vars['parentStr']['ethernetSettings']['speed'],'type' => 'select','options' => $this->_tpl_vars['speedList'],'selected' => $this->_tpl_vars['data']['ethernetSettings']['speed'],'disableCondition' => "1!=".($this->_tpl_vars['configType'])), $this);?>

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