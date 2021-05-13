<?php /* Smarty version 2.6.18, created on 2009-04-06 08:55:18
         compiled from bandStrip.tpl */ ?>
<div  id="WirelessBlock">
	<table class="BlockContent" style="margin-top: 10px; width: 100%;">
		<tr>
			<td style="margin: 0px; padding: 0px; border-spacing: 0px;">
				<ul class="inlineTabs">
					<?php if ($this->_tpl_vars['config']['TWOGHZ']['status']): ?>
					<li <?php if ($this->_tpl_vars['data']['activeMode'] == '0' || $this->_tpl_vars['data']['activeMode'] == '1' || $this->_tpl_vars['data']['activeMode'] == '2' || $this->_tpl_vars['data']['activeMode0'] == '0' || $this->_tpl_vars['data']['activeMode0'] == '1' || $this->_tpl_vars['data']['activeMode0'] == '2'): ?>class="Active" activeRadio="true"<?php else: ?> activeRadio="false"<?php endif; ?> id="inlineTab1"><a href="#">802.11<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '0' || $this->_tpl_vars['data']['activeMode0'] == '0'): ?>Active<?php endif; ?>">b<?php if ($this->_tpl_vars['data']['activeMode'] == '0' || $this->_tpl_vars['data']['activeMode0'] == '0'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span>/<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '1' || $this->_tpl_vars['data']['activeMode0'] == '1'): ?>Active<?php endif; ?>">bg<?php if ($this->_tpl_vars['data']['activeMode'] == '1' || $this->_tpl_vars['data']['activeMode0'] == '1'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span><?php if ($this->_tpl_vars['config']['MODE11N']['status']): ?>/<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '2' || $this->_tpl_vars['data']['activeMode0'] == '2'): ?>Active<?php endif; ?>">ng<?php if ($this->_tpl_vars['data']['activeMode'] == '2' || $this->_tpl_vars['data']['activeMode0'] == '2'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span><?php endif; ?></a></li>
					<input type="hidden" id="activeMode0" value="<?php echo $this->_tpl_vars['data']['activeMode0']; ?>
">
					<?php endif; ?>
					<?php if ($this->_tpl_vars['config']['FIVEGHZ']['status']): ?>
					<li <?php if ($this->_tpl_vars['data']['activeMode'] == '3' || $this->_tpl_vars['data']['activeMode'] == '4' || $this->_tpl_vars['data']['activeMode1'] == '3' || $this->_tpl_vars['data']['activeMode1'] == '4'): ?>class="Active" activeRadio="true"<?php else: ?> activeRadio="false"<?php endif; ?> id="inlineTab2"><a href="#">802.11<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '3' || $this->_tpl_vars['data']['activeMode1'] == '3'): ?>Active<?php endif; ?>">a<?php if ($this->_tpl_vars['data']['activeMode'] == '3' || $this->_tpl_vars['data']['activeMode1'] == '3'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span><?php if ($this->_tpl_vars['config']['MODE11N']['status']): ?>/<span class="Active" onmouseover='showLayer(this);' onmouseout='hideLayer(this);'><b class="RadioText<?php if ($this->_tpl_vars['data']['activeMode'] == '4' || $this->_tpl_vars['data']['activeMode1'] == '4'): ?>Active<?php endif; ?>">na<?php if ($this->_tpl_vars['data']['activeMode'] == '4' || $this->_tpl_vars['data']['activeMode1'] == '4'): ?><img src="../images/activeRadio.gif"><span>Radio is set to 'ON'</span><?php endif; ?></b></span><?php endif; ?></a></li>
					<input type="hidden" id="activeMode1" value="<?php echo $this->_tpl_vars['data']['activeMode1']; ?>
">
					<?php endif; ?>
					<span id="radioOn" style="display: none;">Radio is set to 'ON'</span>
					<input type="hidden" id="activeMode" value="<?php echo $this->_tpl_vars['data']['activeMode']; ?>
">
				</ul>
			</td>
		</tr>
	</table>
	<input type="hidden" name="currentInterface" id="currentInterface" value="<?php echo $this->_tpl_vars['interface']; ?>
">
	<input type="hidden" name="currentInterfaceNum" id="currentInterfaceNum" value="<?php echo $this->_tpl_vars['interfaceNum']; ?>
">
    <input type="hidden" name="previousInterfaceNum" id="previousInterfaceNum">