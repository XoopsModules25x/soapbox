<?php
// $Id: column.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: column.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

include( "header.php" );
$op = '';
//HACK for cache by domifara
if (is_object($xoopsUser)) {
	$xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
}

$xoopsOption['template_main'] = 'sb_column.html';
include_once( XOOPS_ROOT_PATH . "/header.php" );

$mydirname = $myts ->htmlSpecialChars(basename( dirname( __FILE__ )  ) );
if($mydirname !== "soapbox" && $mydirname !== "" && ! preg_match( '/^(\D+)(\d*)$/' , $mydirname ) ) {
	echo ( "invalid dirname: " . htmlspecialchars( $mydirname , ENT_QUOTES) ) ;
}

$columnID = isset($_GET['columnID']) ? intval($_GET['columnID']) : 0;
//---GET view sort --
$sortname = isset($_GET['sortname']) ? strtolower(trim(strip_tags($myts->stripSlashesGPC($_GET['sortname'])))) : 'datesub';
if ( !in_array($sortname , array('datesub' , 'weight' , 'counter' , 'rating' ,'headline')) ) {
	$sortname = 'datesub';
}
$sortorder = isset($_GET['sortorder']) ? strtoupper(trim(strip_tags($myts->stripSlashesGPC($_GET['sortorder']))))  : 'DESC';
if ( !in_array($sortorder , array('ASC','DESC')) ) {
	$sortorder = 'DESC';
}
//---------------
include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$start = isset( $_GET['start'] ) ? intval( $_GET['start'] ) : 0;

	//-------------------------------------	
	$_entrydata_handler =& xoops_getmodulehandler('entryget',$xoopsModule->dirname());
	//-------------------------------------	
	$_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
																				 intval($xoopsModuleConfig['indexperpage']) , $start ,
																				 true, true, 0,  0, null,
																				 $sortname, $sortorder,
																				 $columnID , null ,
																				 true ,
																				 false) ;
	$totalarts = $_entrydata_handler->total_getArticlesAllPermcheck;
	if (empty($_entryob_arr) || $totalarts == 0 ) {
		redirect_header( XOOPS_URL."/modules/".$mydirname."/index.php", 1, _MD_SB_MAINNOTOPICS );
		exit();
	}
	//get category object
	$_categoryob =& $_entryob_arr[0]->_sbcolumns;
	//get vars 

$category = array();
$category = $_categoryob->toArray(); //all assign

$category['colid'] = $columnID;
$category['author'] = getLinkedUnameFromId($category['author'], 0);
$category['authorname'] = getauthorName($category['author']);
$category['image'] = $category['colimage'];
$category['total'] = $totalarts;
$xoopsTpl -> assign( 'category', $category);

//------------------------------------------------------
	foreach ($_entryob_arr as $_entryob) {
		//-----------
		unset($articles) ;
		$articles = array();
		//get vars 
		$articles = $_entryob->toArray() ;
		//--------------------
		$articles['id'] = $articles['articleID'];
		$articles['datesub'] =$myts->htmlSpecialChars(formatTimestamp( $articles['datesub'], $xoopsModuleConfig['dateformat'] )) ;;
//		$articles['poster'] = XoopsUserUtility::getUnameFromId( $articles['uid'] );
		$articles['poster'] = getLinkedUnameFromId( $category['author'] );
		$articles['bodytext'] = xoops_substr( $articles['bodytext'] ,0 , 255);
		//--------------------
		if ($articles['submit'] != 0){
			$articles['headline'] ='['. _MD_SB_SELSUBMITS .']' .$articles['headline'];
			$articles['teaser'] =$xoopsUser->getVar('uname') ._MD_SB_SUB_SNEWNAMEDESC;
			$articles['lead'] =$xoopsUser->getVar('uname') ._MD_SB_SUB_SNEWNAMEDESC;
		} elseif ($_entryob->getVar('datesub') == 0 || $_entryob->getVar('datesub') > time()){
			$articles['headline'] ='['. _MD_SB_SELWAITEPUBLISH .']' .$articles['headline'];
			$articles['teaser'] =$xoopsUser->getVar('uname') ._MD_SB_SUB_SNEWNAMEDESC;
			$articles['lead'] =$xoopsUser->getVar('uname') ._MD_SB_SUB_SNEWNAMEDESC;
		}
		//--------------------
		if ( !empty($articles['artimage']) && $articles['artimage'] != 'blank.png' && file_exists( XOOPS_ROOT_PATH . "/" .$myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir']) . "/" . $articles['artimage'] ) )	{
			$articles['image'] = XOOPS_URL . "/" .$myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir']) . "/" . $articles['artimage']  ;
		} else {
			$articles['image'] = '';
		} 

		if ($xoopsModuleConfig['includerating'] == 1) {
			$xoopsTpl->assign('showrating', 1);
			$rating = $articles['rating'];
			$votes = $articles['votes'];
			if ( $rating != 0.00 ) {
				$articles['rating'] = _MD_SB_RATING . ": " . $myts->htmlSpecialChars( number_format( $rating, 2 ) );
				$articles['votes'] = _MD_SB_VOTES . ": " . $myts->htmlSpecialChars( $votes );
			} else {
				$articles['rating'] = _MD_SB_RATING . ": 0.00";
				$articles['votes'] = _MD_SB_VOTES . ": 0";
			}
		}
		//--------------------
		// Functional links
		$articles['adminlinks'] = $_entrydata_handler->getadminlinks( $_entryob , $_categoryob );
		$articles['userlinks'] = $_entrydata_handler->getuserlinks( $_entryob );
		
		$xoopsTpl -> append( 'articles', $articles);
	}

$pagenav = new XoopsPageNav( $totalarts , intval($xoopsModuleConfig['indexperpage']), $start, 'start', 'columnID=' . $articles['columnID'] .'&sortname=' . $sortname . '&sortorder=' . $sortorder);
$category['navbar'] = '<div style="text-align:right;">' . $pagenav -> renderNav() . '</div>';

$xoopsTpl->assign('xoops_pagetitle', $category['name']);
$xoopsTpl -> assign( 'category', $category);

$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
$xoopsTpl->assign('lang_moduledirname', $mydirname);
$xoopsTpl->assign('imgdir',  $myts ->htmlSpecialChars($xoopsModuleConfig['sbimgdir']));
$xoopsTpl->assign('uploaddir',  $myts ->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']));

$xoopsTpl->assign('sortname', $sortname);
$xoopsTpl->assign('sortorder', $sortorder);

$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/style.css" />');

include( XOOPS_ROOT_PATH . "/footer.php" );

?>