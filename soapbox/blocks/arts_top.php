<?php 
/**
 * $Id: arts_top.php v 1.0 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.0
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
function b_arts_top_show( $options ) {
	$myts = & MyTextSanitizer :: getInstance();
	$block_outdata = array();
	$module_name = 'soapbox' ;
	$hModule =& xoops_gethandler('module');
	$soapModule =& $hModule->getByDirname($module_name) ;
	if ( !is_object($soapModule) ) {
		return ;
	}
	$hModConfig =& xoops_gethandler('config');
	$module_id = $soapModule -> getVar( 'mid' );
	$soapConfig =& $hModConfig->getConfigsByCat(0, $module_id);
	//-------------------------------------	
	if ( !in_array($options[0] , array('datesub' , 'weight' , 'counter' , 'rating' ,'headline')) ) {
		$options[0] = 'datesub';
	}
	if ($options[0] == 'weight'){
		$sortorder = 'ASC';
	} else {
		$sortorder = 'DESC';
	}
	$_entrydata_handler =& xoops_getmodulehandler('entryget',$module_name);
	$_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
																				 intval($options[1]) , 0 ,
																				 true, true, 0,  0, 1,
																				 $options[0] , $sortorder ,
																				 null , null ,
																				 false ,
																				 false) ;
	if (empty($_entryob_arr)  || count($_entryob_arr) == 0 ) {
		return $block_outdata;
	}
	//-------------------------------------	
	foreach ($_entryob_arr as $_entryob) {
		if (is_object($_entryob)) {
			//-----------
			$newarts['linktext'] = xoops_substr( $_entryob->getVar('headline'), 0, intval($options[2]) )  ;
			$newarts['id'] =  $_entryob->getVar('articleID');
			$newarts['dir'] = $module_name;
			$newarts['date'] =  $myts->htmlSpecialChars(formatTimestamp( $_entryob->getVar('datesub'), $soapConfig['dateformat'] ));
			if ( $options[0] == "datesub" )	{
				$newarts['new'] =  $myts->htmlSpecialChars(formatTimestamp( $_entryob->getVar('datesub'), $soapConfig['dateformat'] ));
			} elseif ( $options[0] == "counter" ) {
				$newarts['new'] =  $_entryob->getVar('counter');
			} elseif ( $options[0] == "weight" ) {
				$newarts['new'] = $_entryob->getVar('weight');
			} elseif ( $options[0] == "rating" ) {
				$newarts['new'] = number_format($_entryob->getVar('rating'), 2, '.', ''); 
				$newarts['votes'] = $_entryob->getVar('votes');
			} else{
				$newarts['new'] =  $myts->htmlSpecialChars(formatTimestamp( $_entryob->getVar('datesub'), $soapConfig['dateformat'] ));
			} 
			$block_outdata['toparticles'][] = $newarts;
		}
	}
	return $block_outdata;
} 

function b_arts_top_edit( $options )
	{
	$myts = & MyTextSanitizer :: getInstance();
	$form = "" . _MB_SB_ORDER . "&nbsp;<select name='options[]'>";

	$form .= "<option value='datesub'";
	if ( $options[0] == "datesub" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_DATE . "</option>\n";

	$form .= "<option value='counter'";
	if ( $options[0] == "counter" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_HITS . "</option>\n";

	$form .= "<option value='weight'";
	if ( $options[0] == "weight" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_WEIGHT . "</option>\n";

	$form .= "<option value='rating'";
	if ( $options[0] == "rating" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_RATING . "</option>\n";

	$form .= "</select>\n";
	$form .= "&nbsp;" . _MB_SB_DISP . "&nbsp;<input type='text' name='options[]' value='" . $myts->htmlSpecialChars($options[1]) . "' />&nbsp;" . _MB_SB_ARTCLS . "";
	$form .= "&nbsp;<br>" . _MB_SB_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $myts->htmlSpecialChars($options[2]) . "' />&nbsp;" . _MB_SB_LENGTH . "";

	return $form;
	} 

?>