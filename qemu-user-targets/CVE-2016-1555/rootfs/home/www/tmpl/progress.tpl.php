<?php /* Smarty version 2.6.18, created on 2010-03-24 15:20:05
         compiled from progress.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0"<?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Netgear</title>
<script type="text/javascript" src="include/scripts/prototype.js?code=<?php echo $this->_tpl_vars['random']; ?>
"></script>
<script type="text/javascript" src="include/scripts/effects.js?code=<?php echo $this->_tpl_vars['random']; ?>
"></script>
<script type="text/javascript" src="include/scripts/common.js?code=<?php echo $this->_tpl_vars['random']; ?>
"></script>
<script type="text/javascript" src="include/scripts/control-modal.js?code=<?php echo $this->_tpl_vars['random']; ?>
"></script>
</head>
<body style="background-color: #FFFFFF; margin: 0px; padding: 0px;">
<form name="navForm" action="<?php echo $this->_tpl_vars['ipAddress']; ?>
index.php" method="post" target="_top">
	<input type="hidden" id="logout">
</form>
<script language="javascript">
<!--
<?php echo '
if (typeof(ModalWindow) == \'function\') {
var progressBar = new ModalWindow(false, {
			contents: function() { return "<img src=\'images/loading.gif\'>"; },
			overlayCloseOnClick: true,
			overlayClassName: \'ProgressBar_overlay\',
			containerClassName: \'ProgressBar_container\',
			opacity: 100,
			iframe: true
		});
}
if (progressBar != undefined) {
	progressBar.open();
}
'; ?>

<?php if ($this->_tpl_vars['restoringDefaults']): ?>
setTimeout("window.top.location.href = '<?php echo $this->_tpl_vars['ipAddress']; ?>
';",<?php echo $this->_tpl_vars['redirectTime']; ?>
);
<?php else: ?>
window.setTimeout(processLogout,<?php echo $this->_tpl_vars['redirectTime']; ?>
);
<?php endif; ?>
<?php echo '

var _disableAll = true;
var pingID = null;
var oOptions = {
                method: "post",
                asynchronous: false,
                timeoutDelay: 5,
                onSuccess: function (oXHR, oJson) {
                    processLogout();
                },
                onFailure: function (oXHR, oJson) {
                    pingAP();
                },
                onTimeout: function(request) {
                    pingAP();
                },
                onException: function(request, exception) {
                	if (request.abort) request.abort();
                	if (pingID) window.clearTimeout(pingID);
					pingID = window.setTimeout(pingAP,5000);
                }
            };

function pingAP()
{
    new Ajax.Request(\''; ?>
<?php echo $this->_tpl_vars['ipAddress']; ?>
<?php echo 'test.php?id='; ?>
<?php echo $this->_tpl_vars['random']; ?>
<?php echo '\', oOptions);
}
pingID = window.setTimeout(pingAP,60000);
'; ?>

-->
</script>
</body>
</html>