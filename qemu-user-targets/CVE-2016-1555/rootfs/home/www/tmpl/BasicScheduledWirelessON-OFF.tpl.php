<?php /* Smarty version 2.6.18, created on 2010-06-18 06:24:35
         compiled from BasicScheduledWirelessON-OFF.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'BasicScheduledWirelessON-OFF.tpl', 18, false),)), $this); ?>

	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Scheduled Wireless ON-OFF','schWLONOFF')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">	
						<tr><td>
							<?php $this->assign('scheduledWirelessStatus', $this->_tpl_vars['data']['basicSettings']['scheduledWirelessStatus']); ?>                                                        
                            <?php $this->assign('onclickStr', "graysomething(this,true);"); ?>                                                
                                                               
                                                        
                                                        
							<?php echo smarty_function_input_row(array('label' => "Scheduled Wireless on-off",'id' => 'schWirelessonoff','name' => $this->_tpl_vars['parentStr']['basicSettings']['scheduledWirelessStatus'],'type' => 'radio','options' => "1-On,0-Off",'selectCondition' => "==".($this->_tpl_vars['scheduledWirelessStatus']),'onclick' => ($this->_tpl_vars['onclickStr']).";disableSchduleControls(this)"), $this);?>

							</td>
						<tr>
                                                        
								<td class="DatablockLabel">Radio off schedule</td>
								<td class="DatablockContent">
									<table width="80%">
										<tr align="center"><td "width=10%">M</td><td "width=10%">T</td><td "width=10%">W</td><td "width=10%">T</td><td "width=10%">F</td><td "width=10%">S</td><td "width=10%">S</td></tr>
										<tr align="center">
                                                                                    <td><Input type="checkbox" id="schRad_0" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
                                                                                    <input type="hidden" name="<?php echo $this->_tpl_vars['parentStr']['basicSettings']['scheduledWirelessWeeklyStatus']; ?>
" id="scheduledWirelessWeeklyStatus" value="<?php echo $this->_tpl_vars['data']['basicSettings']['scheduledWirelessWeeklyStatus']; ?>
">

                                                                                    <td><Input type="checkbox" id="schRad_1" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>

                                                                                    <td><Input type="checkbox" id="schRad_2" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
                                                                                    
                                                                                    <td><Input type="checkbox" id="schRad_3" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
                                                                                    
                                                                                    <td><Input type="checkbox" id="schRad_4" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
                                                                                    
                                                                                    <td><Input type="checkbox" id="schRad_5" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
                                                                                    
                                                                                    <td><Input type="checkbox" id="schRad_6" onclick="setActiveContent();convertWeekScheduleToString();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>
										</tr>                                                                                
									</table>

								</td>
							</tr>
						</tr>	
							<tr>
								<td class="DatablockLabel">Radio ON Time</td>
								<td class="DatablockContent">
									&nbsp&nbsp<input class="input" size="2" maxlength="2" id="radioOnHour" label="Radio ON Hour" value="" type="text" onblur="convert2TimeString()" validate="Numericality, <?php echo '{ minimum:0, maximum: 23, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();" <?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>&nbsp:
									<input class="input" size="2" maxlength="2" id="radioOnMin" label="Radio ON Minutes" value="" type="text"  onblur="convert2TimeString()" validate="Numericality, <?php echo '{ minimum:0, maximum: 59, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();"<?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>&nbsp hrs
									<input type="hidden" name="<?php echo $this->_tpl_vars['parentStr']['basicSettings']['radioOnTime']; ?>
" id="radioOnTime" value="<?php echo $this->_tpl_vars['data']['basicSettings']['radioOnTime']; ?>
">
								</td>
							</tr>								
							
							<tr>
								<td class="DatablockLabel">Radio OFF Time</td>
								<td class="DatablockContent">
									&nbsp&nbsp<input class="input" size="2" maxlength="2" id="radioOffHour" label="Radio OFF Hour" value="" type="text" onblur="convert2TimeString()" validate="Numericality, <?php echo '{ minimum:0, maximum: 23, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();" <?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>&nbsp:
									<input class="input" size="2" maxlength="2" id="radioOffMin" label="Radio OFF Minutes" value="" type="text" onblur="convert2TimeString()" validate="Numericality, <?php echo '{ minimum:0, maximum: 59, onlyInteger: true }^Presence"'; ?>
 onkeydown="setActiveContent();" <?php if ($this->_tpl_vars['scheduledWirelessStatus'] == '0'): ?>disabled="disabled"<?php endif; ?>>&nbsp hrs
									<input type="hidden" name="<?php echo $this->_tpl_vars['parentStr']['basicSettings']['radioOffTime']; ?>
" id="radioOffTime" value="<?php echo $this->_tpl_vars['data']['basicSettings']['radioOffTime']; ?>
">
								</td>
							</tr>
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

<script language="javascript">
	<!--
            <?php echo '
	     convertRadioOnOffTime();
             convertScheduleWeekStatus();
             '; ?>

	-->
</script>	