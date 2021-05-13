<?php /* Smarty version 2.6.18, created on 2010-08-09 06:26:59
         compiled from PacketCapture.tpl */ ?>
<script language="javascript">
<!--
    var rad1Status = 1;
    var rad2Status = 1;
-->
</script>
<tr>
		<td>
			<table class="tableStyle">
				<tr>
					<td colspan="3"><script>tbhdr('Packet Capture','packetCapture')</script></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBody">
						<table class="tableStyle">
							<tr>
								<td class="DatablockContent" style="text-align: center;">
									<input type="button" name="start" id="start" value="Start" onclick="doPacketCapture('start')" <?php if ($this->_tpl_vars['data']['monitor']['pktCaptureStatus'] == '1'): ?>disabled="disabled"<?php endif; ?>>&nbsp;&nbsp;
									<input type="button" name="stop" id="stop" value="Stop" onclick="doPacketCapture('stop')" <?php if ($this->_tpl_vars['data']['monitor']['pktCaptureStatus'] != '1'): ?>disabled="disabled"<?php endif; ?>>&nbsp;&nbsp;
									<input type="button" name="saveas" id="saveas" value="Save as..." onclick="doSave()" <?php if ($this->_tpl_vars['data']['monitor']['pktCaptureStatus'] != '2'): ?>disabled="disabled"<?php endif; ?>>
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
	<script language="javascript">
	<!--
    <?php if ($this->_tpl_vars['config']['TWOGHZ']['status']): ?>
        <?php if ($this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['radioStatus'] == 0): ?>
                rad1Status = 0;
        <?php endif; ?>
    <?php else: ?>
                rad1Status = 0;
    <?php endif; ?>

    <?php if ($this->_tpl_vars['config']['FIVEGHZ']['status']): ?>
        <?php if ($this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan1']['radioStatus'] == '0'): ?>
                rad2Status = 0;
        <?php endif; ?>
    <?php else: ?>
                rad2Status = 0;
    <?php endif; ?>

    <?php echo '
    if(rad1Status == 0 && rad2Status == 0){
        document.getElementById(\'start\').disabled=true;
        document.getElementById(\'stop\').disabled=true;
        document.getElementById(\'saveas\').disabled=true;
    }
    '; ?>


<?php echo '
        function doSave()
        {
            document.location.href=\'downloadFile.php?file=pcap&id=\'+Math.random(10000,99999);
            return false;

        }
'; ?>


<?php if (( $this->_tpl_vars['data']['wlanSettings']['wlanSettingTable']['wlan0']['apMode'] == 5 )): ?>
	Form.disable(document.dataForm);
<?php endif; ?>
	-->
	</script>