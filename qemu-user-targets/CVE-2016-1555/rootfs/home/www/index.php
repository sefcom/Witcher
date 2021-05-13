#!/usr/bin/php
<?
require_once 'common.php';

$GUIobject = new webGUI($template,empty($_REQUEST['page'])?'main':$_REQUEST['page']);

$GUIobject->setNavigation();

if ($GUIobject->sessionEnabled()) {
	
	$GUIobject->doAction();

	$GUIobject->getData();
}

$GUIobject->setTemplateName();
	
$GUIobject->setTemplateVars();

$GUIobject->displayTemplate();
?>
