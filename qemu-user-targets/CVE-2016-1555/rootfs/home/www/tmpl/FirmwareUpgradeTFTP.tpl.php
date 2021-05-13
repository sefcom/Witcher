<?php /* Smarty version 2.6.18, created on 2010-06-11 06:38:17
         compiled from FirmwareUpgradeTFTP.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'FirmwareUpgradeTFTP.tpl', 24, false),)), $this); ?>
<?php 
if($_REQUEST['tftpfail']=='1') {
 ?>
	<script>
	<!--
		$('br_head').innerHTML = "TFTP failed to get the file!";
		$('errorMessageBlock').show();
	-->
	</script>
<?php 
}
 ?>

	<tr>
		<td>
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Firmware Upgrade TFTP','upgradeFirmwareTFTP')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php echo smarty_function_input_row(array('label' => 'Firmware File Name','id' => 'firwareFileName','name' => 'firwareFileName','type' => 'text','value' => "",'size' => '20','maxlength' => '64','validate' => "Presence^Ascii"), $this);?>

                                                        <?php echo smarty_function_input_row(array('label' => 'TFTP Server IP','id' => 'tftpServerIP','name' => 'tftpServerIP','type' => 'text','value' => "",'size' => '20','maxlength' => '32','validate' => "Presence^Ascii"), $this);?>

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