<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * GlobalContentelements
 * Use contentelements in every extension
 *
 * @author Christoph Wiechert <wio@psitrax.de>
 * @copyright 4ward.media 2011
 * @licence LGPL
 */

if(version_compare(VERSION,'3.0','>='))
{
header('Content-Type: text/plain');
echo <<<EOERR
GlobalContentelements is NOT compatible with Contao 3!!!

Please delete system/modules/GlobalContentelements folder!
To migrate the Database you have to run some queries:

### if you use news4ward:
UPDATE tl_content SET ptable='tl_news4ward_article' WHERE do='news4ward';

### if you use boxes4ward:
UPDATE tl_content SET ptable='tl_boxes4ward_article' WHERE do='boxes4ward';

if the ptable row dosnt already exists, create it with
ALTER TABLE tl_content ADD ptable VARCHAR(64) NOT NULL default '';
EOERR;

exit;
}

// Register Callback to prevent deleting the wrong CEs
$GLOBALS['TL_HOOKS']['reviseTable'][] = array('GlobalContentelements','reviseTable');

// Hook for updateDatabase to populate tl_content.do with "article"
$GLOBALS['TL_HOOKS']['sqlCompileCommands'][] = array('GlobalContentelements','sqlCompileCommands');


// Load the modified ModuleArticle class to prevent
// the classloader to load the Contao-Core ModuleArticle class
if(TL_MODE == 'FE')
{
	require_once(TL_ROOT.'/system/modules/GlobalContentelements/ModuleArticle.php');
}

