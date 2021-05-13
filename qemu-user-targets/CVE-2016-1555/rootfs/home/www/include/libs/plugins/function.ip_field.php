<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {ip_field} function plugin
 *
 * Type:     function<br>
 * Name:     ip_field<br>
 * Date:     Aug 23, 2007<br>
 * Purpose:  get ip input fields for each sub section of the IP<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - height = image height (optional, default actual height)
 *         - width = image width (optional, default actual width)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *         - path_prefix = prefix for path output (optional, default empty)
 *
 * @author   Suresh <shri@rishie.net>
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_ip_field($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    require_once $smarty->_get_plugin_filepath('function','generate_input_fields');
    
    $templateFieldGen=new TemplateFieldGenerator;

    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    if ($params['type']!='hidden' && empty($params['type']))
		$params['type']="ipfield";
	$prefix = $templateFieldGen->GenerateTemplateField($params);

    return $prefix;
}

/* vim: set expandtab: */

?>
