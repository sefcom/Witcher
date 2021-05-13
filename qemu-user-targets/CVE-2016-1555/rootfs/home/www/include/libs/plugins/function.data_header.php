<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_image} function plugin
 *
 * Type:     function<br>
 * Name:     html_image<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  format HTML tags for the image<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - height = image height (optional, default actual height)
 *         - width = image width (optional, default actual width)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *         - path_prefix = prefix for path output (optional, default empty)
 *
 * Examples: {html_image file="/images/masthead.gif"}
 * Output:   <img src="/images/masthead.gif" width=400 height=23>
 * @link http://smarty.php.net/manual/en/language.function.html.image.php {html_image}
 *      (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @author credits to Duda <duda@big.hu> - wrote first image function
 *           in repository, helped with lots of functionality
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_data_header($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');

    $label='';
    $helptextLink='';
    $actionButtons='';
    $prefix='';
    $backgroundColor='';
    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'label':
            case 'helplink':
            case 'actionButtons':
            case 'headerType':
            case 'backgroundColor':
                $$_key = $_val;
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    $smarty->trigger_error("data_header: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    if (empty($label)) {
        $smarty->trigger_error("data_header: missing 'Label' parameter", E_USER_NOTICE);
        return;
    }


    if(isset($params['helplink'])) {
         $helptextLink .= 'onclick="showHelp(event,\''.$helplink.'\');"';
//        $smarty->trigger_error("data_header: missing 'Label' parameter", E_USER_NOTICE);
//        return;
    }

    if (isset($params['headerType']) && $params['headerType'] == 'inline' ) {
    	$headerLeft='dataheaderAction';
    	$headerRight='dataheaderAction';
    	$headerBullet="";
    	if (!empty($params['backgroundColor'])) {
    		$style="style='background-color: ".$backgroundColor."'";
    	}
    }
    else {
    	$headerLeft='dataheaderLeft';
    	$headerRight='dataheaderRight';
    	$headerBullet="::";
    }

    if(isset($params['actionButtons'])) {
         $actionButtons = $actionButtons;
//        $smarty->trigger_error("data_header: missing 'Label' parameter", E_USER_NOTICE);
//        return;
    }
    
$prefix .= '<table class="tableStyle" style="width: 100%">
				<tr>
					<td class="spacer50Percent font12BoldBlue" style="padding: 0px; text-align: left; white-space: nowrap;">'.$label.'</td>
					<td class="spacer50Percent" style="text-align: right; white-space: nowrap;">'.$actionButtons.'</td>
				</tr>
			</table>';
    return $prefix;
}

/* vim: set expandtab: */

?>
