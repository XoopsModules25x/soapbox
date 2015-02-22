<?php
// $Id: print.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: print.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
include 'header.php';
global $mydirname ;
$mydirname = $myts ->htmlSpecialChars(basename( dirname( __FILE__ )  ) );
if($mydirname !== "soapbox" && $mydirname !== "" && ! preg_match( '/^(\D+)(\d*)$/' , $mydirname ) ) {
	echo ( "invalid dirname: " . htmlspecialchars( $mydirname , ENT_QUOTES) ) ;
}

include_once XOOPS_ROOT_PATH."/modules/".$mydirname."/include/cleantags.php";
$articleID = 0;
if ( isset( $_GET['articleID'] ) ){$articleID = intval($_GET['articleID']);}
if ( isset( $_POST['articleID'] ) ){$articleID = intval($_POST['articleID']);}

if (empty($articleID))	{
	redirect_header("index.php");
}

function PrintPage($articleID)
	{
	global $mydirname ;
	global $xoopsConfig, $xoopsModule, $xoopsModuleConfig;
	$myts = & MyTextSanitizer :: getInstance();
	$articleID = intval($articleID);
	//get entry object
	$_entrydata_handler =& xoops_getmodulehandler('entryget',$mydirname);
	$_entryob =& $_entrydata_handler->getArticleOnePermcheck($articleID ,true ,true);
	if (!is_object($_entryob) ) {
		redirect_header( XOOPS_URL."/modules/".$mydirname."/index.php", 1, "Not Found" );
		exit();
	}
	//-------------------------------------	
	$articles = $_entryob->toArray();
	//get category object
	$_categoryob =& $_entryob->_sbcolumns;
	//get vars 
	$category = $_categoryob->toArray();
	//-------------------------------------	
	//get author	
	$authorname = getauthorName($category['author']);
	//-------------------------------------	

	$datetime = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $xoopsModuleConfig['dateformat']) );
//	$lead = $myts->htmlSpecialChars($lead);
//	$bodytext = str_replace("[pagebreak]","<br style=\"page-break-after:always;\">",$bodytext);
//	$bodytext = $myts->displayTarea($bodytext, $html, $smiley, $xcodes, '', $breaks);
	$bodytext = str_replace("[pagebreak]","<br style=\"page-break-after:always;\">",$_entryob->getVar('bodytext' , 'none'));
	$bodytext = $GLOBALS['SoapboxCleantags']->cleanTags($myts->displayTarea($bodytext, $articles['html'], $articles['smiley'], $articles['xcodes'], '', $articles['breaks']));
	
	$sitename = $myts->htmlSpecialChars($xoopsConfig['sitename']) ;
	$slogan = $myts->htmlSpecialChars($xoopsConfig['slogan']) ;

	echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
	echo "<html>\n<head>\n";
	echo "<title>" . $sitename . "</title>\n";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "' />\n";
	echo "<meta name='AUTHOR' content='" . $sitename . "' />\n";
	echo "<meta name='COPYRIGHT' content='Copyright (c) 2004 by " . $sitename . "' />\n";
	echo "<meta name='DESCRIPTION' content='" . $slogan . "' />\n";
	echo "<meta name='GENERATOR' content='" . XOOPS_VERSION . "' />\n\n\n";

//hack start 2003-3-18 by toshimitsu 
//Column: --> _MD_SB_COLUMNPRN , Author: --> _MD_SB_AUTHORPRN
	echo "<body bgcolor='#ffffff' text='#000000'>
			<div style='width: 600px; border: 1px solid #000; padding: 20px;'>
				<div style='text-align: center; display: block; padding-bottom: 12px; margin: 0 0 6px 0; border-bottom: 2px solid #ccc;'><img src='" . XOOPS_URL . "/modules/" . $xoopsModule -> dirname() . "/images/sb_slogo.png' border='0' alt='' /><h2 style='margin: 0;'>" . $articles['headline'] . "</h2></div>
				<div></div>
				<div>"._MD_SB_COLUMNPRN."<b>".$category['name']."</b></div>
				<div style='padding-bottom: 6px; border-bottom: 1px solid #ccc;'>"._MD_SB_AUTHORPRN." <b>".$authorname."</b></div>
				<p>".$articles['lead']."</p>
				<p>".$articles['bodytext']."</p>
				<div style='padding-top: 12px; border-top: 2px solid #ccc;'><small><b>Published: </b>&nbsp;" . $datetime . "<br /></div>
			</div>
			<br />
		  </body>
		  </html>";
	}

PrintPage($articleID);

?>