<?php /* Smarty version 2.6.18, created on 2010-06-23 06:01:32
         compiled from BasicGeneral.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'input_row', 'BasicGeneral.tpl', 11, false),array('function', 'ip_field', 'BasicGeneral.tpl', 13, false),)), $this); ?>
	<tr>
		<td>	
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('General','basicSettings')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<?php echo smarty_function_input_row(array('label' => 'Access Point Name','id' => 'apName','name' => $this->_tpl_vars['parentStr']['basicSettings']['apName'],'type' => 'text','class' => 'input','value' => $this->_tpl_vars['data']['basicSettings']['apName'],'size' => '16','maxlength' => '15','validate' => "Presence^AlphaNumericWithH^NotAllNums^NotLastH"), $this);?>

							<?php echo smarty_function_input_row(array('label' => "Country / Region",'id' => 'sysCountryRegion','name' => $this->_tpl_vars['parentStr']['basicSettings']['sysCountryRegion'],'type' => 'select','class' => 'select','options' => $this->_tpl_vars['countryList'],'selected' => $this->_tpl_vars['data']['basicSettings']['sysCountryRegion']), $this);?>

							<?php echo smarty_function_ip_field(array('label' => "&nbsp;",'id' => 'sysCountry','name' => 'sysCountry','type' => 'hidden','value' => $this->_tpl_vars['data']['basicSettings']['sysCountryRegion']), $this);?>

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