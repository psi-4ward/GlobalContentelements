<?php if(!defined('TL_ROOT')) {die('You cannot access this file directly!');}

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */

// delete tl_content records when deleting a tl_article
$GLOBALS['TL_DCA']['tl_article']['config']['ondelete_callback'][] = array('GlobalContentelements','deleteChildRecords');
