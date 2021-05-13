<?php /* Smarty version 2.6.18, created on 2009-02-17 11:52:08
         compiled from RebootAP.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'RebootAP.tpl', 11, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Reboot AP','rebootAP')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">			
									<?php echo smarty_function_input_row(array('label' => 'Reboot','id' => 'rebootAP','name' => 'rebootAP','type' => 'radio','options' => "1-Yes,0-No",'selectCondition' => "==0"), $this);?>
 
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