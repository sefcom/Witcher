<?php /* Smarty version 2.6.18, created on 2009-01-12 08:01:24
         compiled from RestoreSettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'RestoreSettings.tpl', 11, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Restore Settings','restoreSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">							
							<?php echo smarty_function_input_row(array('label' => 'Restore saved settings from a file','class' => 'input','id' => 'restoreSettingsFile','name' => 'restoreSettingsFile','type' => 'file','oncontextmenu' => 'return false','onkeydown' => "this.blur()",'onpaste' => 'return false'), $this);?>

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
	</table>
	</form>
	<input type="hidden" id="dummy" name="dummy" value="">