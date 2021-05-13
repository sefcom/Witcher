<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty wordwrap modifier plugin
 *
 * Type:     modifier<br>
 * Name:     wordwrap<br>
 * Purpose:  wrap a string of text at a given length
 * @link http://smarty.php.net/manual/en/language.modifier.wordwrap.php
 *          wordwrap (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @return string
 */
function smarty_modifier_wordwrap($string,$length=80,$break="\n",$cut=false)
{
	if ($break == '<br />' || $break == '<BR />')
		$break = '<br/>';
	$string = str_replace('&nbsp;',' ',$string);
	$string = str_replace('&amp;','&',$string);
    $string = wordwrap($string,$length,$break,$cut);
	$string = str_replace('&','&amp;',$string);
	$string = str_replace(' ','&nbsp;',$string);
	return $string;
}

?>
