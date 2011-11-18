<?php  if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */


// Register submit callback to save the modules name in tl_content.do
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('GlobalContentelements','setDo');

// Set the filter to display only contentelements with the modules name
$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['filter'] = array(array('do=?',$this->Input->get('do')));


/**
 * You have to set the ptable of tl_content according your
 * modules ptable:

if($this->Input->get('do') == 'mymodule')
{
	$GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_mymodule_article';
}
*/


