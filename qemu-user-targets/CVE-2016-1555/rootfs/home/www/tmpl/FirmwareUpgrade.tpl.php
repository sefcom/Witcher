<?php /* Smarty version 2.6.18, created on 2009-02-17 11:52:17
         compiled from FirmwareUpgrade.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'FirmwareUpgrade.tpl', 24, false),)), $this); ?>
<?php 
if ($_SERVER['SERVER_PORT']=='443') {
 ?>
	<script>
	<!--
		$('br_head').innerHTML = "Please switch to HTTP mode for upgrading firmware!";
		$('errorMessageBlock').show();
	-->
	</script>
<?php 
} else {
 ?>

	<tr>
		<td>
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Firmware Upgrade','upgradeFirmware')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php echo smarty_function_input_row(array('label' => 'Select file','id' => 'firmwareFile','class' => 'input','name' => 'firmwareFile','type' => 'file','oncontextmenu' => 'return false','onkeydown' => "this.blur()",'onpaste' => 'return false'), $this);?>

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
<?php 
}
 ?>