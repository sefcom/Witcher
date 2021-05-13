<?php /* Smarty version 2.6.18, created on 2009-03-05 06:51:23
         compiled from Login.tpl */ ?>
<script language="javascript">
<!--
<?php echo '
	if (window.top.frames.header.loginPage == undefined)
	{
		window.top.location.href = "index.php";
	}
'; ?>

-->
</script>
	<tr>
		<td>
			<table class="tableStyle" height="100%">
				<tr>
					<td class="" style="width: 8px;"><img src="images/clear.gif" width="8"/></td>
					<td class="spacer100Percent topAlign" style="text-align: center;"><table class="loginBox">
				<tr>
					<td colspan="3"><table class='tableStyle'><tr><td colspan='2' class='subSectionTabTopLeft spacer80Percent font12BoldBlue' align="left">Login</td><td class='subSectionTabTopRight spacer20Percent'><a href='javascript: void(0);'  onclick="showHelp('Login','login');"><img src='images/help_icon.gif' width='12' height='12' title='Click for help'/></a></td></tr><tr><td colspan='3' class='subSectionTabTopShadow'></td></tr></table></td>
				</tr>
				<tr>
					<td class="subSectionBodyDot">&nbsp;</td>
					<td class="spacer100Percent paddingsubSectionBodyLogin">
						<table class="padding5Top tableStyle">
							<tr>
								<td class="font10Bold spacer25Percent" align="left">Username</td>
								<td class="spacer55Percent" align="left"><input type='text' class="input"  name="username" id="username" maxlength="48" label="User Name" validate="Presence, <?php echo '{ onlyOnSubmit: true }'; ?>
"></td>
							</tr>
							<tr>
								<td class="font10Bold padding4Top" align="left">Password</td>
								<td class="padding4Top" align="left"><input type='password' class="input"  name="password" id="password" maxlength="64" label="Password" validate="Presence, <?php echo '{ onlyOnSubmit: true }'; ?>
"></td>
							</tr>
							<tr>
								<td colspan="2" class="padding5TopBottom10Right"><input type="image" onclick="doLogin(event);" src="images/login_on.gif" title="Login" border="0px;"><input type="hidden" name="login" id="login" value=""></td>
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
<script language="javascript">
<!--
$('username').focus();
-->
</script>