#!/usr/bin/php
<?
@include('sessionCheck.inc');
require_once 'include.php';

$GUIobject = new webGUI($template);

$GUIobject->setNavigation();

if ($GUIobject->sessionEnabled()) {
	
	$GUIobject->getData("configSave.cfg");
	
//	if ($GUIobject->checkAction()) {
		//print_r($_POST);
		//$GUIobject->setData($_POST);
//	}
	
}

$GUIobject->setTemplateName();
	
$GUIobject->setTemplateVars();

$GUIobject->displayTemplate();
?>
