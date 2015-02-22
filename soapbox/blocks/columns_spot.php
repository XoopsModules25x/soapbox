<?php 
// $Id: columns_spot.php,v 0.0.1 2005/10/30 20:30:00 domifara Exp $
/**
 * $Id: arts_spot.php v 1.0 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.0
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
/* This function spotlights a column, with a spotlight article and links to others */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
function b_columns_spot_show( $options ){

	$block_outdata = array();
	//-------------------------------------	
	$myts = & MyTextSanitizer :: getInstance();
	$module_name = 'soapbox' ;
	$hModule =& xoops_gethandler('module');
	$soapModule =& $hModule->getByDirname($module_name) ;
	if (!is_object($soapModule) ) {
		return ;
	}

	$hModConfig =& xoops_gethandler('config');
	$module_id = $soapModule -> getVar( 'mid' );
	$soapConfig =& $hModConfig->getConfigsByCat(0, $module_id);
	//-------------------------------------	
	// To handle options in the template
	if ( isset($options[0]) && $options[0] == 1 ) {
		$block_outdata['showspotlight'] = 1;
	} else {
		$block_outdata['showspotlight'] = 0;
	}
	//-------------------------------------	
	if ( isset($options[1])  ) {
		$options[1] = intval($options[1]);
	} else {
		$options[1] = 0 ;
	}
	if ( $options[1] == 0 ) {
		$block_outdata['showartcles'] = 0;
	} else {
		$block_outdata['showartcles'] = 1;
	}
	//-------------------------------------	
	if ( isset($options[2]) && $options[2] == 1 ) {
		$block_outdata['showdateask'] = 1;
	} else {
		$block_outdata['showdateask'] = 0;
	}
	//-------------------------------------	
	if ( isset($options[3]) && $options[3] == 1 ) {
		$block_outdata['showbylineask'] = 1;
	} else {
		$block_outdata['showbylineask'] = 0;
	}
	//-------------------------------------	
	if ( isset($options[4]) && $options[4] == 1 ) {
		$block_outdata['showstatsask'] = 1;
	} else {
		$block_outdata['showstatsask'] = 0;
	}
	//-------------------------------------	
	if ( isset($options[5]) && $options[5] == 'ver' ) {
		$block_outdata['verticaltemplate'] = 1;
	} else {
		$block_outdata['verticaltemplate'] = 0;
	}
	//-------------------------------------	
	if ( isset($options[6]) && $options[6] == 1 ) {
		$block_outdata['showpicask'] = 1;
	} else {
		$block_outdata['showpicask'] = 0;
	}
	//-------------------------------------	
	$sortname =$options[7];
	if ( !in_array($sortname , array('datesub' , 'weight' , 'counter' , 'rating' ,'headline')) ) {
		$sortname = 'datesub';
	} 
	if ($sortname == 'weight'){
		$sortorder = 'ASC';
	} else {
		$sortorder = 'DESC';
	}
	//-------------------------------------	
	if ( isset($options[8]) && intval($options[8]) > 0 ) {
		$options[8] = intval($options[8]);
	} else {
		$options[8] = 65;
	}
	//-------------------------------------	
	// Try to see what tabs are visibles (if we are in restricted view of course)
	$opt_columnIDs=array();
	if( !empty($options[9])) {
        $opt_columnIDs = array_slice($options, 9);
	}
	if (!empty($opt_columnIDs) && is_array($opt_columnIDs)) {
		foreach ($opt_columnIDs  as $v){
			$columnIDs[] = intval($v) ;
		}
	} else {
		$columnIDs = null ;
	}
	// Retrieve the column's name
//	$resultB = $xoopsDB -> query( "SELECT name, colimage FROM ". $xoopsDB -> prefix( "sbcolumns" ) . " WHERE columnID = " . $options[0] . " " );
//	list ( $name, $colimage ) = $xoopsDB -> fetchRow( $resultB );
	//-------------------------------------	
	$_entrydata_handler =& xoops_getmodulehandler('entryget',$module_name);
	//-------------------------------------	
	//get category object
	$_categoryob_arr =& $_entrydata_handler->getColumnsAllPermcheck( 
																0 , 0 , 
																true , 'weight' , 'ASC' ,
																$columnIDs, null ,
																true ,
																false);
	if (empty($_categoryob_arr)  || count($_categoryob_arr) == 0 ) {
		$block_outdata['display'] = 0;
		return $block_outdata;
	}
	$block_outdata['display'] = 1;
	//-------------------------------------	
	$block_outdata['totalcols'] = $_entrydata_handler->total_getColumnsAllPermcheck;
	$block_outdata['moduledir'] = $module_name;
	$block_outdata['modulename'] = $soapModule ->getVar( 'name' );
	$block_outdata['sbuploaddir']  = $myts->htmlSpecialChars($soapConfig['sbuploaddir']) ;
	//-------------------------------------	
	$i_col = 1;
    xoops_load('XoopsUserUtility');
	foreach ($_categoryob_arr as $_categoryob) {
		//----------------------------
		$category = $_categoryob->toArray(); //all assign
		$_outdata_arr = array(); 
		$_outdata_arr= $category ; 
		$_outdata_arr['authorname'] = XoopsUserUtility::getUnameFromId(intval( $category['author'] ));
		//-------------------------------------
		if ( $options[1] == 0 ) {
			$_outdata_arr['artdatas'] = array() ;
		} else {
			//-------------------------------------
			// Retrieve the latest article in the selected column
			$_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
																						 $options[1] , 0 ,
																						 true, true, 0,  0, 1,
																						 $sortname , $sortorder ,
																						 $category['columnID'] , null ,
																						 false ,
																						 false) ;
			$_outdata_arr['totalarts'] = $_entrydata_handler->total_getArticlesAllPermcheck;
			//----------------------------
            //xoops_load('XoopsUserUtility');
			$i = 1;
			foreach ($_entryob_arr  as $key => $_entryob){
				// get vars initialize
				//-------------------------------------	
				$articles = $_entryob->toArray();
				$articles[] =$articles; 
				//spot
				$articles['poster'] = XoopsUserUtility::getUnameFromId( $articles['uid'] );
				$articles['date'] = $myts->htmlSpecialChars(formatTimestamp( $articles['datesub'], $soapConfig['dateformat'] ) );
				$articles['rating'] = number_format($articles['rating'] , 2, '.', '');
				// -- Then the teaser text and as sorted data
				$articles['subhead']  = xoops_substr($articles['headline'] , 0, $options[8] )  ;
				$articles['sublead'] = xoops_substr( $articles['lead'] ,0 , $options[8] );
				$articles['subteaser'] = xoops_substr( $articles['teaser'] ,0 , $options[8] );
				$articles['subbodytext'] = xoops_substr( $articles['bodytext'] ,0 , $options[8] );
				$articles['bodytext'] = '';
		
				if ( $sortname == "datesub" ){
					$articles['new'] =  $myts->htmlSpecialChars(formatTimestamp( $articles['datesub'], $soapConfig['dateformat'] ));
				} elseif ( $sortname == "counter" ) {
					$articles['new'] =  _MB_SB_HITS . $articles['counter'];
				} elseif ( $sortname == "weight" ) {
					 $articles['new'] = _MB_SB_WEIGHT . $articles['weight'];
				} elseif ( $sortname == "rating" ) {
					$articles['new'] = _MB_SB_RATING . number_format($articles['rating'], 2, '.', '') ._MB_SB_VOTE . $articles['votes'];
				} else {
					$articles['new'] =  $myts->htmlSpecialChars(formatTimestamp( $articles['datesub'], $soapConfig['dateformat'] ));
				}
				//--------------------
				$_outdata_arr['artdatas'][$i] = $articles ;
				unset($articles);
				$i++ ;
			}
		}
		//-------------------------------------
		$block_outdata['coldatas'][$i_col] = $_outdata_arr ;
		unset($_outdata_arr);
		$i_col++ ;
	}
	return $block_outdata;
} 

function b_columns_spot_edit( $options )
	{
	$myts = & MyTextSanitizer :: getInstance();
	$module_name = 'soapbox' ;
	$hModule =& xoops_gethandler('module');
	$soapModule =& $hModule->getByDirname($module_name) ;
	if (!is_object($soapModule) ) {
		return ;
	}
	$form = ''; 
	//-----
	$chked = "";
	$form .= _MB_SB_SPOTLIGHT;
	if ( $options[0] == 1 ) {
		$chked = " checked='checked'";
	}
	$form .= "<input type='radio' name='options[0]' value='1'".$chked." />&nbsp;"._YES;
	$chked = "";
	if ( $options[0] == 0 ) {
		$chked = " checked='checked'";
	}
	$form .= "&nbsp;<input type='radio' name='options[0]' value='0'".$chked." />"._NO . "<br />";
	//-----
	//-----
	$form .= _MB_SB_ARTSTOSHOW ."<input type='text' name='options[1]' value='" . $myts->htmlSpecialChars($options[1]) . "' />&nbsp; "._MB_SB_ARTCLS.".<br />";
	//-----
	$chked = "";
	$form .= _MB_SB_SHOWDATE;
	if ( $options[2] == 1 ) {
		$chked = " checked='checked'";
	}
	$form .= "<input type='radio' name='options[2]' value='1'".$chked." />&nbsp;"._YES;
	$chked = "";
	if ( $options[2] == 0 ) {
		$chked = " checked='checked'";
	}
	$form .= "&nbsp;<input type='radio' name='options[2]' value='0'".$chked." />"._NO . "<br />";
	//-----
	$chked = "";
	$form .= _MB_SB_SHOWBYLINE;
	if ( $options[3] == 1 ) {
		$chked = " checked='checked'";
	}
	$form .= "<input type='radio' name='options[3]' value='1'".$chked." />&nbsp;"._YES;
	$chked = "";
	if ( $options[3] == 0 ) {
		$chked = ' checked="checked"';
	}
	$form .= '&nbsp;<input type="radio" name="options[3]" value="0"'.$chked.' />'._NO.'<br />';
	//-----
	$chked = "";
	$form .= _MB_SB_SHOWSTATS;
	if ( $options[4] == 1 ) {
		$chked = " checked='checked'";
	}
	$form .= "<input type='radio' name='options[4]' value='1'".$chked." />&nbsp;"._YES;
	$chked = "";
	if ( $options[4] == 0 ) {
		$chked = ' checked="checked"';
	}
	$form .= "&nbsp;<input type='radio' name='options[4]' value='0' " . $chked ." />"._NO."<br />";

    $form .= _MB_SB_TEMPLATE . "<select name='options[5]' >";
	$form .= "<option value='ver'";
	if ( $options[5] == 'ver' ) {
		$form .= " selected='selected'";
	}
	$form .= ">"._MB_SB_VERTICAL."</option>\n";
	$form .= "<option value='hor'";
	if($options[5] == 'hor'){
		$form .= " selected='selected'";
	}
	$form .= ">"._MB_SB_HORIZONTAL."</option>";
	$form .= "</select><br />";

	$chked = "";
	$form .= _MB_SB_SHOWPIC;
	if ( $options[6] == 1 ) {
		$chked = " checked='checked'";
	}
	$form .= "<input type='radio' name='options[6]' value='1' ".$chked." />&nbsp;"._YES;

	$chked = "";
	if ( $options[6] == 0 ) {
		$chked = " checked='checked'";
	}
	$form .= "&nbsp;<input type='radio' name='options[6]' value='0' " . $chked ." />"._NO."<br />";
	//---------- sortname ------
	$form .= "" . _MB_SB_ORDER . "&nbsp;<select name='options[7]'>";

	$form .= "<option value='datesub'";
	if ( $options[7] == "datesub" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_DATE . "</option>\n";

	$form .= "<option value='counter'";
	if ( $options[7] == "counter" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_HITS . "</option>\n";

	$form .= "<option value='weight'";
	if ( $options[7] == "weight" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_WEIGHT . "</option>\n";

	$form .= "<option value='rating'";
	if ( $options[7] == "rating" )
		{
		$form .= " selected='selected'";
		} 
	$form .= ">" . _MB_SB_RATING . "</option>\n";

	$form .= "</select>\n";

	$form .= "&nbsp;<br>" . _MB_SB_CHARS . "&nbsp;<input type='text' name='options[8]' value='" . $myts->htmlSpecialChars($options[8]) . "' />&nbsp;" . _MB_SB_LENGTH . "";

	//-------------------------------------	
	// Try to see what tabs are visibles (if we are in restricted view of course)
	$opt_columnIDs=array();
	if( !empty($options[9])) {
        $opt_columnIDs = array_slice($options, 9);
	}
	if (!empty($opt_columnIDs) && is_array($opt_columnIDs)) {
		foreach ($opt_columnIDs  as $v){
			$columnIDs[] = intval($v) ;
		}
	}
	$_entrydata_handler =& xoops_getmodulehandler('entryget',$module_name);
	$_categoryob_arr =& $_entrydata_handler->getColumns();
	$form .= "<br />". _MB_SB_SPOTLIGHT_TOPIC."<br /><select name='options[]' multiple='multiple'>";
	$form .= "<option value='0'>(ALL)</option>";
	if (!empty($_categoryob_arr) ){
		foreach ($_categoryob_arr as $_categoryob) {
			$categoryID = $_categoryob->getVar('columnID') ;
			$name = $_categoryob->getVar('name') ;
			if ( in_array( $categoryID , $columnIDs) ) {
					$sel = " selected=\"selected\"";
			} else {
					$sel = "";
			}
			$form .= "<option value='" . $categoryID . "' " . $sel . ">" . $categoryID ." : " . $name . "</option>";
		} 
	}
	$form .= "</select><br />\n";
	
	return $form;
	} 
?>