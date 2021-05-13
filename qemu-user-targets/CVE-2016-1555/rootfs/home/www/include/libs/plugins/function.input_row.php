<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_input_row($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('function','generate_input_fields');

    $templateFieldGen=new TemplateFieldGenerator;

    $label='';

	//{input_row label="Enable Wireless Bridging and Repeating" id="wdsEnabled" name="wdsEnabled" type="checkbox" event="onclick" action="wdsOnEnable(this.checked)" value=$data.wdsSettings.apMode selectCondition="neq-0"}
    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    //print_r($params);
    if (isset($params['row_id'])) {
    	$rowid = 'id="'.$params['row_id'].'"';
    	unset ($params['row_id']);
    }
    if (isset($params['rowClass'])) {
    	$rowClass = 'class="'.$params['rowClass'].'"';
    	unset ($params['rowClass']);
    }
    if (isset($params['label_id'])) {
    	$labelid = 'id="'.$params['label_id'].'"';
    	unset ($params['label_id']);
    }
	if (isset($params['disableCondition'])) {
		eval('$res=('.$params['disableCondition'].');');
		if ($res)
			$params['disabled']="disabled";
		unset($params['disableCondition']);
    }
	if (isset($params['readonlyCondition'])) {
		eval('$res=('.$params['readonlyCondition'].');');
		if ($res)
			$params['readonly']="readonly";
		unset($params['readonlyCondition']);
    }
    
	$fieldInputStr = $templateFieldGen->GenerateTemplateField($params);
	$label=$params['label'];

	$prefix .= '<tr '.$rowid.' ' .$rowClass. '>
				<td class="DatablockLabel" '.$labelid.'>'.$label.'</td>
				<td class="DatablockContent">'.$fieldInputStr.'</td>
			</tr>';
    return $prefix;
}

/* vim: set expandtab: */

?>
