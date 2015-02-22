<?php
// $Id: index.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: index.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

include( "header.php" );
$op = '';
if (is_object($xoopsUser)) {
	$xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
}
$xoopsOption['template_main'] = 'sb_index.html';
include_once( XOOPS_ROOT_PATH . "/header.php" );

$mydirname = $myts ->htmlSpecialChars(basename( dirname( __FILE__ )  ) );
if($mydirname !== "soapbox" && $mydirname !== "" && ! preg_match( '/^(\D+)(\d*)$/' , $mydirname ) ) {
	echo ( "invalid dirname: " . htmlspecialchars( $mydirname , ENT_QUOTES) ) ;
}
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
//---------------

$columna = array ();
// Options
switch ( $op )
{
	case "default":
	default:
		//-------------------------------------	
		$_entrydata_handler =& xoops_getmodulehandler('entryget',$mydirname);
		//-------------------------------------	

		$author = isset($_GET['author']) ? intval($_GET['author']) : 0;
		//get category object
	if ( !empty($author) ) {
		$_categoryob_arr =& $_entrydata_handler->getColumnsByAuthor(
																	$author, true,
																	intval($xoopsModuleConfig['colsperindex']) , $start ,
																	'weight' , 'ASC') ;
		$totalcols = $_entrydata_handler->total_getColumnsByAuthor;
	} else{
		//get category object
		$_categoryob_arr =& $_entrydata_handler->getColumnsAllPermcheck( 
																	intval($xoopsModuleConfig['colsperindex']) , $start , 
																	true , 'weight' , 'ASC' ,
																	null , null ,
																	true ,
																	false);
		$totalcols = $_entrydata_handler->total_getColumnsAllPermcheck;
	}
		$xoopsTpl->assign('lang_mainhead', $myts->htmlSpecialChars($xoopsModuleConfig['introtitle']));
		$xoopsTpl->assign('lang_maintext', $myts->htmlSpecialChars($xoopsModuleConfig['introtext']));
		$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
		$xoopsTpl->assign('lang_moduledirname', $mydirname);
		$xoopsTpl->assign('imgdir',  $myts ->htmlSpecialChars( $xoopsModuleConfig['sbimgdir']) );
		$xoopsTpl->assign('uploaddir',  $myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir']) );

		//----------------------------
		if ( $totalcols == 0 ) {
			$xoopsTpl->assign('lang_nothing', _MD_SB_NOTHING);
		}
		//----------------------------
		foreach ($_categoryob_arr as $_categoryob) {
			//----------------------------
			$category = $_categoryob->toArray(); //all assign
			//-------------------------------------	
			//get author	
			$category['authorname'] = getauthorName($category['author']);
			//-------------------------------------	
			if ($category['colimage'] != ''){
				$category['imagespan'] = "<span class=\"picleft\"><img class=\"pic\" src=\"".XOOPS_URL."/". $myts ->htmlSpecialChars( $xoopsModuleConfig['sbuploaddir']) ."/".$category['colimage']."\" /></span>";
			} else {
				$category['imagespan'] = "";
			}
			//-------------------------------------	
			$_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
																						 1 , 0 ,
																						 true, true, 0,  0, null,
																						 $sortname, $sortorder,
																						 $_categoryob , null ,
																						 true ,
																						 false) ;
			$totalarts = $_entrydata_handler->total_getArticlesAllPermcheck;
			$category['totalarts'] = $totalarts;
			//------------------------------------------------------
			foreach ($_entryob_arr as $_entryob) {
				//-----------
				unset($articles) ;
				$articles = array();
				//get vars
				$articles = $_entryob->toArray() ;
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

				// Functional links
				$articles['adminlinks'] = $_entrydata_handler->getadminlinks( $_entryob , $_categoryob );
				$articles['userlinks'] = $_entrydata_handler->getuserlinks( $_entryob );
				//loop tail
				$category['content'][] = $articles;
			}

			$category['total'] = $totalcols;
			$pagenav = new XoopsPageNav( $category['total'], intval($xoopsModuleConfig['colsperindex']) , $start, 'start', '');
			$category['navbar'] = '<div style="text-align:right;">' . $pagenav -> renderNav() . '</div>';
			
			$xoopsTpl->append_by_ref('cols', $category);
			unset($category);
		}
}
//$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="style.css" />');
$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$mydirname.'/style.css" />');

include( XOOPS_ROOT_PATH . "/footer.php" );

?>