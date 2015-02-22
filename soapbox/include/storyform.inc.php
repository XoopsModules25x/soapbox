<?php
// $Id: storyform.inc.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: storyform.inc.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
if (file_exists(XOOPS_ROOT_PATH.'/language/'. $myts ->htmlSpecialChars( $xoopsConfig['language'] ).'/calendar.php')) {
	include_once XOOPS_ROOT_PATH.'/language/'. $myts ->htmlSpecialChars( $xoopsConfig['language'] ).'/calendar.php';
} else {
	include_once XOOPS_ROOT_PATH.'/language/english/calendar.php';
}
//include_once XOOPS_ROOT_PATH . "/class/xoopstree.php";
include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";

$sform = new XoopsThemeForm( _MD_SB_SUB_SMNAME, "storyform", $myts->htmlSpecialChars(xoops_getenv( 'PHP_SELF' )) );
	//get select category object
if ( is_object( $xoopsUser ) ) {
	if ( $xoopsUser -> isAdmin($xoopsModule -> getVar('mid'))) {
		$_can_edit_categoryob_arr =& $_entrydata_handler->getColumns(null , true); 
	} else{
		$_can_edit_categoryob_arr =& $_entrydata_handler->getColumnsByAuthor($xoopsUser->uid() , true ); 
	}

//----------------------------
	$collist = array();
	foreach ($_can_edit_categoryob_arr as $key => $_can_edit_categoryob) {
		$collist[$key] = $_can_edit_categoryob->getVar('name') ;
	}
    $col_select = new XoopsFormSelect('', 'columnID' ,intval( $e_articles['columnID'] ) );
    $col_select->addOptionArray($collist);
    $col_select_tray = new XoopsFormElementTray(_MD_SB_COLUMN, "<br />");
    $col_select_tray->addElement($col_select);
    $sform->addElement($col_select_tray);

}
// This part is common to edit/add
// HEADLINE, LEAD, BODYTEXT
$sform -> addElement( new XoopsFormText( _MD_SB_ARTHEADLINE, 'headline', 50, 50, $e_articles['headline'] ), true );

// LEAD
$sform -> addElement( new XoopsFormTextArea( _MD_SB_ARTLEAD, 'lead', $e_articles['lead'], 10, 120 ) );
//$editor_lead=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _MD_SB_ARTLEAD , 'lead' , $e_articles['lead'] , '100%', '200px');
//$sform->addElement($editor_lead,true);

// TEASER
$sform -> addElement( new XoopsFormTextArea( _MD_SB_ARTTEASER, 'teaser', $e_articles['teaser'], 10, 120 ) );
//$editor_teaser=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _MD_SB_ARTTEASER ,'teaser', $e_articles['teaser'] , '100%', '120px');
//$sform->addElement($editor_teaser,true);

$autoteaser_radio = new XoopsFormRadioYN( _MD_SB_AUTOTEASER, 'autoteaser', 0, ' ' . _MD_SB_YES . '', ' ' . _MD_SB_NO . '' );
$sform -> addElement( $autoteaser_radio );
$sform -> addElement( new XoopsFormText( _MD_SB_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100 ) );

$sform -> addElement( new XoopsFormDhtmlTextArea( _MD_SB_ARTBODY, 'bodytext', $e_articles['bodytext'], 20, 120 ) );
/*
	if (isset($xoopsModuleConfig['form_options']) ){
		$editor=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _MD_SB_ARTBODY, 'bodytext', $e_articles['bodytext'] , '100%', '400px');
		$sform->addElement($editor,true);
	} else {
		$sform -> addElement( new XoopsFormDhtmlTextArea( _MD_SB_ARTBODY, 'bodytext', $e_articles['bodytext'], 20, 120 ) );
	}
*/

// The article CAN have its own image :)
// First, if the article's image doesn't exist, set its value to the blank file
if (!file_exists(XOOPS_ROOT_PATH . "/" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . "/" . $e_articles['artimage']) || empty($e_articles['artimage']) ) {
	$artimage = "blank.png";
} 
// Code to create the image selector
$graph_array = & XoopsLists :: getImgListAsArray( XOOPS_ROOT_PATH . "/" . $myts ->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) );
$artimage_select = new XoopsFormSelect( '', 'artimage', $e_articles['artimage'] );
$artimage_select -> addOptionArray( $graph_array );
$artimage_select -> setExtra( "onchange='showImgSelected(\"image5\", \"artimage\", \"" . $myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir'] ) . "\", \"\", \"" . XOOPS_URL . "\")'" );
$artimage_tray = new XoopsFormElementTray( _MD_SB_SELECT_IMG, '&nbsp;' );
$artimage_tray -> addElement( $artimage_select );
$artimage_tray -> addElement( new XoopsFormLabel( '', "<br /><br /><img src='" . XOOPS_URL . "/" . $myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir'] ) . "/" . $e_articles['artimage'] . "' name='image5' id='image5' alt='' />" ) );
$sform -> addElement( $artimage_tray );

if ( is_object( $xoopsUser ) ) {

// WEIGHT
	$sform->addElement(new XoopsFormText(_MD_SB_WGT, 'weight', 4, 4, $e_articles['weight']));
	//----------
	// datesub
	//----------
	$datesub_caption = $myts->htmlSpecialChars( formatTimestamp( $e_articles['datesub'] , $xoopsModuleConfig['dateformat']) . "=>");
    $datesub_tray = new XoopsFormDateTime( _MD_SB_POSTED.'<br />' . $datesub_caption ,'datesub' , 15, time()) ;
	// you don't want to change datesub
	$datesubnochage_checkbox = new XoopsFormCheckBox( _MD_SB_DATESUBNOCHANGE, 'datesubnochage', 0 );
    $datesubnochage_checkbox->addOption(1, _MD_SB_YES);
	$datesub_tray -> addElement( $datesubnochage_checkbox );
	$sform->addElement($datesub_tray);
	//-----------

// COMMENTS
	if (isset($GLOBALS['xoopsModuleConfig']['globaldisplaycomments']) && $GLOBALS['xoopsModuleConfig']['globaldisplaycomments'] == 1){
		// COMMENTS
		// Code to allow comments
		$addcommentable_radio = new XoopsFormRadioYN( _MD_SB_ALLOWCOMMENTS, 'commentable', $e_articles['commentable'], ' ' . _MD_SB_YES . '', ' ' . _MD_SB_NO . '' );
		$sform -> addElement( $addcommentable_radio );
	}	
	if (isset($xoopsModuleConfig['autoapprove']) &&  $xoopsModuleConfig['autoapprove'] == 1 ){
		if ( $xoopsUser->isAdmin($xoopsModule->mid())) {
			// OFFLINE
			// Code to take article offline, for maintenance purposes
			$offline_radio = new XoopsFormRadioYN(_MD_SB_SWITCHOFFLINE, 'offline', $e_articles['offline'] , ' '._MD_SB_YES.'', ' '._MD_SB_NO.'');
			$sform -> addElement($offline_radio);
		} else {
			// submit user
			// Code to take article offline, for maintenance purposes
			$submit_radio = new XoopsFormRadioYN(_MD_SB_SWITCHSUBMITS, 'submit', $e_articles['submit'] , ' '._MD_SB_YES.'', ' '._MD_SB_NO.'');
			$sform -> addElement($submit_radio);
		}
		
		// ARTICLE IN BLOCK
		// Code to put article in block
		$block_radio = new XoopsFormRadioYN( _MD_SB_BLOCK, 'block', $e_articles['block']  , ' ' . _MD_SB_YES . '', ' ' . _MD_SB_NO . '' );
		$sform -> addElement( $block_radio );

		// notification public
		$notifypub_radio = new XoopsFormRadioYN( _MD_SB_NOTIFY, 'notifypub', $e_articles['notifypub'] , ' ' . _MD_SB_YES . '', ' ' . _MD_SB_NO . '' );
		$sform -> addElement( $notifypub_radio );

	}

	if (isset($e_articles['articleID']) && !empty($e_articles['articleID'])) {
		$sform -> addElement( new XoopsFormHidden( 'articleID', $e_articles['articleID'] ) );
	}

} 

$button_tray = new XoopsFormElementTray( '', '' );
$hidden = new XoopsFormHidden( 'op', 'post' );
$button_tray -> addElement( $hidden );
$button_tray -> addElement( new XoopsFormButton( '', 'post', _MD_SB_CREATE, 'submit' ) );

$sform -> addElement( $button_tray );
	//-----------
	$xoopsGTicket->addTicketXoopsFormElement( $sform , __LINE__  ) ;
	//-----------
$sform -> display();
unset( $hidden );
 
?>