<?
@include('sessionCheck.inc');
require_once 'common.php';

$GUIobject = new webGUI($template, 'thirdMenu');

$GUIobject->setNavigation();

$GUIobject->setTemplateVars();

$GUIobject->displayTemplate();
?>
