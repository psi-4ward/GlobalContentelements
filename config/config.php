<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */

// Register Callback to prevent deleting the wrong CEs
$GLOBALS['TL_HOOKS']['reviseTable'][] = array('GlobalContentelements','reviseTable');

// Load the modified ModuleArticle class to prevent
// the classloader to load the Contao-Core ModuleArticle class
if(TL_MODE == 'FE')
{
	require_once(TL_ROOT.'/system/modules/GlobalContentelements/ModuleArticle.php');
}

?>