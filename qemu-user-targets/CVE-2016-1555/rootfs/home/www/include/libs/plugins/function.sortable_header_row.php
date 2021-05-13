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
 * @author   B.Suresh Kumar
 * @author credits to Duda <duda@big.hu> - wrote first image function
 *           in repository, helped with lots of functionality
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_sortable_header_row($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared','escape_special_chars');
    
    $sortable='true';
    $tableid='';
    $rowid='0';
    $content='';
    $last='';
    $sortingLink='';
    $lastClass='';
    $server_vars = ($smarty->request_use_auto_globals) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];
    $basedir = isset($server_vars['DOCUMENT_ROOT']) ? $server_vars['DOCUMENT_ROOT'] : '';
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'sortable':
            case 'tableid':
            case 'rowid':
            case 'content':
            case 'last':
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

    if (empty($tableid)===true) {
        $smarty->trigger_error("data_header: missing 'tableid' parameter", E_USER_NOTICE);
        return;
    }

    if ($rowid == '') {
        $smarty->trigger_error("data_header: missing 'rowid' parameter", E_USER_NOTICE);
        return;
    }
    
    if (empty($content)===true) {
        $smarty->trigger_error("data_header: missing 'content' parameter", E_USER_NOTICE);
        return;
    }
    
    if(isset($sortable) && $sortable == 'true') {
         $sortingLink .= 'onclick="headerClicked(\''.$tableid.'\','.$rowid.');"';
//        $smarty->trigger_error("data_header: missing 'Label' parameter", E_USER_NOTICE);
//        return;
    }

    if(isset($last) && $last == 'true') {
         $lastClass .= 'class="Last"';
//        $smarty->trigger_error("data_header: missing 'Label' parameter", E_USER_NOTICE);
//        return;
    }
    
	$prefix .= '<th '.$lastClass.' '.$sortingLink.' id="'.$tableid.'_'.$rowid.'">'.$content.'&nbsp;<img src="images/down_arrow.gif" style="display:none;" alt="Ascending" id="'.$tableid.'_down_'.$rowid.'"><img src="images/up_arrow.gif" style="display:none;" alt="Descending" id="'.$tableid.'_up_'.$rowid.'"></th>';
    return $prefix;
}

/* vim: set expandtab: */

?>
