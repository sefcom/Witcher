<?php /* Smarty version 2.6.18, created on 2009-10-26 17:46:38
         compiled from AdvancedQoSSettings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'data_header', 'AdvancedQoSSettings.tpl', 18, false),array('function', 'ip_field', 'AdvancedQoSSettings.tpl', 32, false),array('modifier', 'default', 'AdvancedQoSSettings.tpl', 203, false),)), $this); ?>
	<tr>
		<td>
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Qos Settings','modifyQoSQueueParameters')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody" style="padding: 0px;">
						<table class="tableStyle">
							<tr>
								<td>
									<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bandStrip.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
									<div id="IncludeTabBlock">
<?php if ($this->_tpl_vars['config']['TWOGHZ']['status']): ?>
										<div id="wlan1">
											<div class="BlockContentTable" style="border-bottom: 0px;" id="table_wlan1">
												<?php echo smarty_function_data_header(array('label' => 'AP EDCA parameters','headerType' => 'inline'), $this);?>

												<table class="BlockContentTable">
                                                    <input type="hidden" name="dummyAPMode0" id="ApMode0" value="<?php echo $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode']; ?>
">
                                                    <input type="hidden" name="dummyWMMSupport0" id="WMMSupport0" value="<?php echo $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['wmmSupport']; ?>
">

													<tr>
														<th>Queue</th>
														<th>AIFS</th>
														<th>cwMin</th>
														<th>cwMax</th>
														<th class="Last">Max. Burst</th>
													</tr>
													<tr>
														<th>Data 0 (Best Effort)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) AIFS",'size' => '3','maxlength' => '3','id' => '01_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMin",'id' => 'wmmApEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMax",'id' => 'wmmApEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) Max. Burst",'size' => '5','maxlength' => '5','id' => '01_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['1']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 1 (Background)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) AIFS",'size' => '3','maxlength' => '3','id' => '02_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMin",'id' => 'wmmApEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMax",'id' => 'wmmApEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) Max. Burst",'size' => '5','maxlength' => '5','id' => '02_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['2']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr>
														<th>Data 2 (Video)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) AIFS",'size' => '3','maxlength' => '3','id' => '03_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMin",'id' => 'wmmApEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMax",'id' => 'wmmApEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) Max. Burst",'size' => '5','maxlength' => '5','id' => '03_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['3']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 3 (Voice)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) AIFS",'size' => '3','maxlength' => '3','id' => '04_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMin",'id' => 'wmmApEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMax",'id' => 'wmmApEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) Max. Burst",'size' => '5','maxlength' => '5','id' => '04_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan0']['4']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
												</table>
												<?php echo smarty_function_data_header(array('label' => 'Station EDCA parameters','headerType' => 'inline'), $this);?>

												<table class="BlockContentTable">
													<tr>
														<th>Queue</th>
														<th>AIFS</th>
														<th>cwMin</th>
														<th>cwMax</th>
														<th class="Last">TXOP Limit</th>
													</tr>
													<tr>
														<th>Data 0 (Best Effort)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) AIFS",'size' => '3','maxlength' => '3','id' => '01_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMin",'id' => 'wmmStaEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMax",'id' => 'wmmStaEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) TXOP Limit",'size' => '5','maxlength' => '5','id' => '01_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['1']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 1 (Background)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) AIFS",'size' => '3','maxlength' => '3','id' => '02_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMin",'id' => 'wmmStaEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMax",'id' => 'wmmStaEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) TXOP Limit",'size' => '5','maxlength' => '5','id' => '02_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['2']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr>
														<th>Data 2 (Video)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) AIFS",'size' => '3','maxlength' => '3','id' => '03_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMin",'id' => 'wmmStaEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMax",'id' => 'wmmStaEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) TXOP Limit",'size' => '5','maxlength' => '5','id' => '03_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['3']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 3 (Voice)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) AIFS",'size' => '3','maxlength' => '3','id' => '04_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMin",'id' => 'wmmStaEdcaCwMin0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMax",'id' => 'wmmStaEdcaCwMax0','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) TXOP Limit",'size' => '5','maxlength' => '5','id' => '04_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan0']['4']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
												</table>
											</div>
										</div>
<?php endif; ?>
<!--@@@FIVEGHZSTART@@@-->
<?php if ($this->_tpl_vars['config']['FIVEGHZ']['status']): ?>
										<div id="wlan2" <?php if ($this->_tpl_vars['config']['TWOGHZ']['status'] && ! $this->_tpl_vars['config']['DUAL_CONCURRENT']['status']): ?>style="display:none;"<?php endif; ?>>
											<div class="BlockContentTable" style="border-bottom: 0px;" id="table_wlan2">
												<?php echo smarty_function_data_header(array('label' => 'AP EDCA parameters','headerType' => 'inline'), $this);?>

												<table class="BlockContentTable">
                                                    <input type="hidden" name="dummyAPMode1" id="ApMode1" value="<?php echo $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['apMode']; ?>
">
                                                    <input type="hidden" name="dummyWMMSupport1" id="WMMSupport1" value="<?php echo $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['wmmSupport']; ?>
">

													<tr>
														<th>Queue</th>
														<th>AIFS</th>
														<th>cwMin</th>
														<th>cwMax</th>
														<th class="Last">Max. Burst</th>
													</tr>
													<tr>
														<th>Data 0 (Best Effort)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) AIFS",'size' => '3','maxlength' => '3','id' => '11_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMin",'id' => 'wmmApEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMax",'id' => 'wmmApEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) Max. Burst",'size' => '5','maxlength' => '5','id' => '11_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['1']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 1 (Background)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) AIFS",'size' => '3','maxlength' => '3','id' => '12_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMin",'id' => 'wmmApEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMax",'id' => 'wmmApEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) Max. Burst",'size' => '5','maxlength' => '5','id' => '12_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['2']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr>
														<th>Data 2 (Video)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) AIFS",'size' => '3','maxlength' => '3','id' => '13_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMin",'id' => 'wmmApEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMax",'id' => 'wmmApEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) Max. Burst",'size' => '5','maxlength' => '5','id' => '13_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['3']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 3 (Voice)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) AIFS",'size' => '3','maxlength' => '3','id' => '14_wmmApEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMin",'id' => 'wmmApEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMax",'id' => 'wmmApEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) Max. Burst",'size' => '5','maxlength' => '5','id' => '14_wmmApEdcaMaxBurst','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaMaxBurst'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmApEdcaSettingTable']['wlan1']['4']['wmmApEdcaMaxBurst'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
												</table>
												<?php echo smarty_function_data_header(array('label' => 'Station EDCA parameters','headerType' => 'inline'), $this);?>

												<table class="BlockContentTable">
													<tr>
														<th>Queue</th>
														<th>AIFS</th>
														<th>cwMin</th>
														<th>cwMax</th>
														<th class="Last">TXOP Limit</th>
													</tr>
													<tr>
														<th>Data 0 (Best Effort)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) AIFS",'size' => '3','maxlength' => '3','id' => '11_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMin",'id' => 'wmmStaEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 0 (Best Effort) cwMax",'id' => 'wmmStaEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 0 (Best Effort) TXOP Limit",'size' => '5','maxlength' => '5','id' => '11_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['1']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 1 (Background)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) AIFS",'size' => '3','maxlength' => '3','id' => '12_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMin",'id' => 'wmmStaEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 1 (Background) cwMax",'id' => 'wmmStaEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 1 (Background) TXOP Limit",'size' => '5','maxlength' => '5','id' => '12_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['2']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr>
														<th>Data 2 (Video)</th>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) AIFS",'size' => '3','maxlength' => '3','id' => '13_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMin",'id' => 'wmmStaEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 2 (Video) cwMax",'id' => 'wmmStaEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td><?php echo smarty_function_ip_field(array('label' => "Data 2 (Video) TXOP Limit",'size' => '5','maxlength' => '5','id' => '13_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['3']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
													<tr class="Alternate">
														<th>Data 3 (Voice)</th>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) AIFS",'size' => '3','maxlength' => '3','id' => '14_wmmStaEdcaAifs','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaAifs'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaAifs'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8, onlyInteger: true ))^Presence"), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMin",'id' => 'wmmStaEdcaCwMin1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaCwMin'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaCwMin']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('type' => 'select','label' => "Data 3 (voice) cwMax",'id' => 'wmmStaEdcaCwMax1','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaCwMax'],'options' => $this->_tpl_vars['apEdcaCwList'],'selected' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaCwMax']), $this);?>
</td>
														<td class="Alternate"><?php echo smarty_function_ip_field(array('label' => "Data 3 (voice) TXOP Limit",'size' => '5','maxlength' => '5','id' => '14_wmmStaEdcaTxopLimit','name' => $this->_tpl_vars['parentStr']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaTxopLimit'],'value' => $this->_tpl_vars['data']['wmmSettings']['wmmStaEdcaSettingTable']['wlan1']['4']['wmmStaEdcaTxopLimit'],'type' => 'text','validate' => "Numericality, (( minimum:0, maximum: 8192, onlyInteger: true ))^Presence"), $this);?>
</td>
													</tr>
												</table>
											</div>
										</div>
<?php endif; ?>
<!--@@@FIVEGHZEND@@@-->
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
	<?php echo smarty_function_ip_field(array('label' => "&nbsp;",'id' => 'enableQoS','name' => 'enableQoS','type' => 'hidden','enableForm' => 'true'), $this);?>

<script language="javascript">
<!--
var prevInterface = <?php echo ((is_array($_tmp=@$_POST['previousInterfaceNum'])) ? $this->_run_mod_handler('default', true, $_tmp, "''") : smarty_modifier_default($_tmp, "''")); ?>
;
		var form = new formObject();
<?php echo '
            if (prevInterface != \'\') {
                    if(prevInterface == \'1\'){
                        form.tab1.activate();
                    }
                    else if(prevInterface == \'2\'){
                        form.tab2.activate();
                    }
             }
             else {
'; ?>

            <?php if ($this->_tpl_vars['config']['TWOGHZ']['status']): ?>
                    form.tab1.activate();
            <?php endif; ?>
//<!--@@@FIVEGHZSTART@@@-->
            <?php if ($this->_tpl_vars['config']['FIVEGHZ']['status']): ?>
                <?php if ($this->_tpl_vars['data']['radioStatus1'] == '1' && $this->_tpl_vars['data']['radioStatus0'] != '1'): ?>
                    form.tab2.activate();
                <?php endif; ?>
            <?php endif; ?>
//<!--@@@FIVEGHZEND@@@-->
<?php echo '
            }
'; ?>

-->
</script>