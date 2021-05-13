<?php /* Smarty version 2.6.18, created on 2008-12-26 06:06:54
         compiled from BackupSettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'BackupSettings.tpl', 11, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Backup Settings','backupSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php echo smarty_function_input_row(array('label' => 'Backup a copy of the current settings to a file','id' => 'backupSettings','name' => 'backupSettings','type' => 'image','src' => "images/backup_on.gif",'value' => 'Backup','onclick' => "$('backupSettingsForm').submit();return false;",'style' => "text-align:center; border: 0px;"), $this);?>

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
<form name="backupSettingsForm" id="backupSettingsForm" action="downloadFile.php?file=config" method="post">
	<input type="hidden" id="dummy" name="dummy" value="">