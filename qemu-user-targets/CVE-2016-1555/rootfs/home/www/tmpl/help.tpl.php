<?php /* Smarty version 2.6.18, created on 2009-05-18 05:18:39
         compiled from help.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'help.tpl', 1, false),)), $this); ?>
<input type="hidden" id="helpURL" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['templateName'])) ? $this->_run_mod_handler('replace', true, $_tmp, '.tpl', '') : smarty_modifier_replace($_tmp, '.tpl', '')); ?>
">
<?php if ($this->_tpl_vars['errorString'] == 'Wireless Radio is turned off!' && $this->_tpl_vars['navigation']['2'] != 'Statistics' && $this->_tpl_vars['navigation']['2'] != 'Logs'): ?>
    <?php echo '
    <script type="text/javascript">
    <!--
    [\'refresh\',\'edit\',\'save\',\'details\'].each(function(buttonId) {
        if (window.top.frames[\'action\'].$(buttonId) != undefined) {
            window.top.frames[\'action\'].$(buttonId).disabled = true;
            window.top.frames[\'action\'].$(buttonId).src = window.top.frames[\'action\'].$(buttonId).src.replace(\'_on.gif\',\'_off.gif\');
        }
    });
    -->
    </script>
    '; ?>

<?php endif; ?>