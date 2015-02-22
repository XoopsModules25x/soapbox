<?php
// $Id: comment_functions.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: comment_functions.php v 1.5 25 April 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 25 April 2004
 * Author: hsalazar
 * Licence: GNU
 */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
function sb_com_update($art_id, $total_num)
	{
//HACK 
//get soapbox moduleConfig
	global $xoopsModule ;
	$hModConfig =& xoops_gethandler('config');
	$soapModuleConfig =& $hModConfig->getConfigList(intval($xoopsModule->getVar('mid'))) ;
	if (isset($soapModuleConfig['globaldisplaycomments']) ) {
		$globaldisplaycomments = $soapModuleConfig['globaldisplaycomments'] ;
	} else {
		$globaldisplaycomments = 0 ;
	}
		if ( $globaldisplaycomments == 0 ) {
        	$db =& XoopsDatabaseFactory::getDatabaseConnection();
        	$sql = 'UPDATE '.$db->prefix('sbarticles').' SET commentable = '.intval($total_num).' WHERE articleID = '.intval($art_id);
        	$db->query($sql);
		}
	}

function sb_com_approve(&$comment)
	{
	// notification mail here
	}
?>