<?php /* Smarty version 2.6.18, created on 2009-05-27 05:22:35
         compiled from UnknownAPList.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'sortable_header_row', 'UnknownAPList.tpl', 39, false),array('modifier', 'replace', 'UnknownAPList.tpl', 54, false),)), $this); ?>
<script language="javascript">
<!--
    var twoGHzEmpty = 0;
    var fiveGHzEmpty = 0;
-->
</script>

<?php if ($this->_tpl_vars['config']['TWOGHZ']['status'] && $this->_tpl_vars['data']['radioStatus0'] == '1'): ?>
	<tr id="wlan1">
		<td>
			<table class="tableStyle">
				<tr>
					<td>
						<table class="tableStyle">
							<tr>
								<td colspan="3">
									<table class='tableStyle'>
										<tr>
											<td colspan='2' class='subSectionTabTopLeft spacer60Percent font12BoldBlue'>Unknown AP List (802.11<?php echo $this->_tpl_vars['wlan0ModeString']; ?>
)</td>
											<td class='subSectionTabTopRight spacer40Percent'>
												<a href='javascript: void(0);' onclick="showHelp('Unknown AP List','unknownAPList');"><img src='images/help_icon.gif' width='12' height='12' title='Click for help'/></a></td>
										</tr>
										<tr>
											<td colspan='3' class='subSectionTabTopShadow'></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="subSectionBodyDot">&nbsp;</td>
								<td class="spacer100Percent paddingsubSectionBody" style="padding: 0px;">
									<table class="tableStyle">
										<tr>
											<td>
												<div  id="BlockContentTable">
													<table class="BlockContentTable" id="unknownList">
														<thead>
															<tr>
																<?php echo smarty_function_sortable_header_row(array('sortable' => 'false','tableid' => 'unknownList','rowid' => '0','content' => "#"), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '1','content' => 'MAC Address'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '2','content' => 'SSID'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '3','content' => 'Privacy'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '4','content' => 'Channel'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '5','content' => 'Rate'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '6','content' => "Beacon Int."), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '7','content' => "# of Beacons"), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '8','last' => 'true','content' => 'Last Beacon'), $this);?>

															</tr>
														</thead>
														<tbody>
														<?php $_from = $this->_tpl_vars['data']['monitor']['apList']['unknownApTable']['wlan0']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['unknownAP'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['unknownAP']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
        $this->_foreach['unknownAP']['iteration']++;
?>
															<tr <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_foreach['unknownAP']['iteration']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['key'])) ? $this->_run_mod_handler('replace', true, $_tmp, '-', ':') : smarty_modifier_replace($_tmp, '-', ':')); ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApSsid']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApPrivacy']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApChannel']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApRate']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApBeaconInterval']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApNumBeacons']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApLastBeacon']; ?>
</td>
															</tr>
														<?php 
															$this->_tpl_vars['unknownApList'].= str_replace('-',':',$this->_tpl_vars['key']) . ',';
														 ?>
														<?php endforeach; else: ?>
															<script language="javascript">
															<!--
                                                                    <?php echo '
                                                                        twoGHzEmpty = 1;
                                                                    '; ?>

																<?php if ($this->_tpl_vars['interface'] == 'wlan1' && ! $this->_tpl_vars['config']['DUAL_CONCURRENT']['status']): ?>
																	window.top.frames['action'].$('save').disabled=true;
																	window.top.frames['action'].$('save').src = 'images/save_off.gif';
																<?php endif; ?>
															-->
															</script>
														<?php endif; unset($_from); ?>
														</tbody>
													</table>
												</div>
											</td>
										</tr>
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
		</td>
	</tr>
	<tr>
		<td class="spacerHeight21"></td>
	</tr>
<?php else: ?>
    <script language="javascript">
        <!--
        <?php echo '
           twoGHzEmpty = 1;
        '; ?>

        -->
    </script>
<?php endif; ?>

<?php if ($this->_tpl_vars['config']['FIVEGHZ']['status'] && $this->_tpl_vars['data']['radioStatus1'] == '1'): ?>
	<tr id="wlan2">
		<td>
			<table class="tableStyle">
				<tr>
					<td>
						<table class="tableStyle">
							<tr>
								<td colspan="3">
									<table class='tableStyle'>
										<tr>
											<td colspan='2' class='subSectionTabTopLeft spacer60Percent font12BoldBlue'>Unknown AP List (802.11<?php echo $this->_tpl_vars['wlan1ModeString']; ?>
)</td>
											<td class='subSectionTabTopRight spacer40Percent'>
												<a href='javascript: void(0);' onclick="showHelp('Unknown AP List','unknownAPList');"><img src='images/help_icon.gif' width='12' height='12' title='Click for help'/></a></td>
										</tr>
										<tr>
											<td colspan='3' class='subSectionTabTopShadow'></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class="subSectionBodyDot">&nbsp;</td>
								<td class="spacer100Percent paddingsubSectionBody" style="padding: 0px;">
									<table class="tableStyle">
										<tr>
											<td>
												<div  id="BlockContentTable">
													<table class="BlockContentTable" id="unknownList">
														<thead>
															<tr>
																<?php echo smarty_function_sortable_header_row(array('sortable' => 'false','tableid' => 'unknownList','rowid' => '0','content' => "#"), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '1','content' => 'MAC Address'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '2','content' => 'SSID'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '3','content' => 'Privacy'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '4','content' => 'Channel'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '5','content' => 'Rate'), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '6','content' => "Beacon Int."), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '7','content' => "# of Beacons"), $this);?>

																<?php echo smarty_function_sortable_header_row(array('sortable' => 'true','tableid' => 'unknownList','rowid' => '8','last' => 'true','content' => 'Last Beacon'), $this);?>

															</tr>
														</thead>
														<tbody>
														<?php $_from = $this->_tpl_vars['data']['monitor']['apList']['unknownApTable']['wlan1']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['unknownAP'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['unknownAP']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
        $this->_foreach['unknownAP']['iteration']++;
?>
															<tr <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_foreach['unknownAP']['iteration']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['key'])) ? $this->_run_mod_handler('replace', true, $_tmp, '-', ':') : smarty_modifier_replace($_tmp, '-', ':')); ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApSsid']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApPrivacy']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApChannel']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApRate']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApBeaconInterval']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApNumBeacons']; ?>
</td>
																<td <?php if ($this->_foreach['unknownAP']['iteration']%2 == 0): ?>class="Alternate"<?php endif; ?>><?php echo $this->_tpl_vars['value']['unknownApLastBeacon']; ?>
</td>
															</tr>
														<?php 
															$this->_tpl_vars['unknownApList'].= str_replace('-',':',$this->_tpl_vars['key']) . ',';
														 ?>
														<?php endforeach; else: ?>
															<script language="javascript">
															<!--
                                                                    <?php echo '
                                                                        fiveGHzEmpty = 1;
                                                                    '; ?>

																<?php if ($this->_tpl_vars['interface'] == 'wlan2' && ! $this->_tpl_vars['config']['DUAL_CONCURRENT']['status']): ?>
																	window.top.frames['action'].$('save').disabled=true;
																	window.top.frames['action'].$('save').src = 'images/save_off.gif';
																<?php endif; ?>
															-->
															</script>
														<?php endif; unset($_from); ?>
														</tbody>
													</table>
												</div>
											</td>
										</tr>
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
		</td>
	</tr>
<?php else: ?>
    <script language="javascript">
        <!--
        <?php echo '
           fiveGHzEmpty = 1;
        '; ?>

        -->
    </script>
<?php endif; ?>

<script language="javascript">
<!--
<?php echo '
function doSave()
{
	if(window.top.frames[\'action\'].$(\'save\').src.indexOf(\'save_on\')!== -1)
	{
		$(\'unknownApListForm\').submit();
		return false;
	}
	else
	{
		window.top.frames[\'action\'].$(\'save\').disabled=true;
	}
	'; ?>


	<?php if (( ! $this->_tpl_vars['config']['DUAL_CONCURRENT']['status'] ) && ( $this->_tpl_vars['config']['TWOGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['radioStatus'] == '0' ) && ( $this->_tpl_vars['config']['FIVEGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['radioStatus'] == '0' )): ?>
			window.top.frames['action'].$('refresh').disabled=true;
			window.top.frames['action'].$('refresh').src="images/refresh_off.gif";
	<?php endif; ?>
	<?php echo '
}
'; ?>

<?php if ($this->_tpl_vars['config']['CLIENT']['status']): ?>
	<?php if (( $this->_tpl_vars['config']['TWOGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] == 5 ) || ( $this->_tpl_vars['config']['FIVEGHZ']['status'] && $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['apMode'] == 5 )): ?>
		Form.disable(document.dataForm);
		window.top.frames['action'].$('refresh').disabled=true;
		window.top.frames['action'].$('refresh').src="images/refresh_off.gif";
		window.top.frames['action'].$('save').disabled=true;
		window.top.frames['action'].$('save').src="images/save_off.gif";
	<?php endif; ?>
<?php endif; ?>

	<?php if ($this->_tpl_vars['config']['DUAL_CONCURRENT']['status']): ?>
        <?php echo '
            if(twoGHzEmpty == 1 && fiveGHzEmpty == 1){
                window.top.frames[\'action\'].$(\'save\').disabled=true;
                window.top.frames[\'action\'].$(\'save\').src="images/save_off.gif";
            }
        '; ?>

    <?php endif; ?>

-->
</script>
</form>
<form name="unknownApListForm" id="unknownApListForm" action="saveTable.php" method="post">
<input type="hidden" name="ApList" value="<?php echo $this->_tpl_vars['unknownApList']; ?>
">
</form>