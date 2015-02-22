<?php
// $Id: submit.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: submit.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

include( "../../mainfile.php" );
//global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;
//----------------------------------------------
//allowsubmit
if ( !isset($xoopsModuleConfig['allowsubmit']) ||  $xoopsModuleConfig['allowsubmit'] != 1){
	redirect_header( "index.php", 1, _NOPERM );
	exit();
}
//guest
if (!is_object($xoopsUser)) {
	redirect_header( "index.php", 1, _NOPERM );
	exit();
}

include XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/include/gtickets.php" ;

$xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
include( XOOPS_ROOT_PATH . "/header.php" );
$myts = & MyTextSanitizer :: getInstance();
//----------------------------------------------
//post op check
$op = 'form';
if ( isset( $_POST['post'] ) ){
	$op =  'post' ;
}elseif ( isset( $_POST['edit'] ) ){
	$op =  'edit' ;
} 
//----------------------------------------------
//post or get articleID check
$articleID = 0;
if ( isset( $_GET['articleID'] ) ){$articleID = intval($_GET['articleID']);}
if ( isset( $_POST['articleID'] ) ){$articleID = intval($_POST['articleID']);}
//----------------------------------------------
//user group , edit_uid
	if ($xoopsUser->isAdmin($xoopsModule->mid())) {
		$thisgrouptype = XOOPS_GROUP_ADMIN;
	} else {
		$thisgrouptype = XOOPS_GROUP_USERS;
	}
	$edit_uid = $xoopsUser->getVar('uid');
	$name = $xoopsUser->getVar("uname");
//-------------------------------------	
$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());
//-------------------------------------	
//get can edit category object
if ($thisgrouptype == XOOPS_GROUP_ADMIN) {
	$_can_edit_categoryob_arr =& $_entrydata_handler->getColumns( null , true) ;
} else {
	$_can_edit_categoryob_arr =& $_entrydata_handler->getColumnsByAuthor( $edit_uid , true) ;
}
if (empty($_can_edit_categoryob_arr) || count($_can_edit_categoryob_arr) == 0 ) {
	redirect_header( "index.php", 1, _MD_SB_NOCOLEXISTS );
	exit();
}
//----------------------------------------------
//main
switch ( $op )	{
	case 'post':
		//-------------------------
		if ( ! $xoopsGTicket->check() ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		//-------------------------
		//articleID check
		if (isset($_POST['articleID']) ) {
			$_entryob =& $_entrydata_handler->getArticleOnePermcheck($articleID ,true ,true);
			if (!is_object($_entryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
				break;
			}
		} else  {
			$_entryob =& $_entrydata_handler->createArticle(true);
	        $_entryob->cleanVars() ;
		}	
		//-------------------------
		//set	
		include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/include/functions.php";
		//set	
		$_entryob->setVar('uid' , $edit_uid );
			if ( isset($_POST['columnID']) ) { $_entryob->setVar('columnID' , intval($_POST['columnID']) ) ; }
			//get category object
			if (!isset($_can_edit_categoryob_arr[$_entryob->getVar('columnID')])) {
				redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/index.php', 2, _NOPERM );
				exit();
			}
			$_categoryob =& $_can_edit_categoryob_arr[$_entryob->getVar('columnID')] ;
			//checkbox not post when value is false
			if ( $thisgrouptype != XOOPS_GROUP_ADMIN ) {
				$_entryob->setVar('html' , 0 ) ;
				$_entryob->setVar('smiley' , 1 ) ;
				$_entryob->setVar('xcodes' , 1 ) ;
				$_entryob->setVar('breaks' , 1 ) ;
			}

			if ( isset($_POST['weight']) ) { $_entryob->setVar('weight' , intval($_POST['weight']) ) ; }

			if ( isset($_POST['commentable']) ) { $_entryob->setVar('commentable' , intval($_POST['commentable']) ) ; }
			if ( isset($_POST['offline']) ) { $_entryob->setVar('offline' , intval($_POST['offline']) ) ; }
			if ( isset($_POST['block']) ) { $_entryob->setVar('block' , intval($_POST['block']) ) ; }
			if ( isset($_POST['notifypub']) ) { $_entryob->setVar('notifypub' , intval($_POST['notifypub']) ) ; }

			//datesub
			$datesubnochage = (isset($_POST['datesubnochage'])) ? intval($_POST['datesubnochage']) : 0;
			$datesub_date_sl = (isset($_POST['datesub'])) ? intval(strtotime($_POST['datesub']['date']))  : 0;
			$datesub_time_sl = (isset($_POST['datesub'])) ? intval($_POST['datesub']['time'])  : 0;
			$datesub = (isset($_POST['datesub'])) ? $datesub_date_sl + $datesub_time_sl  : 0;
			if (!$datesub || $_entryob->_isNew){
				$_entryob->setVar('datesub' , time() ) ;
			} else {
				if (!$datesubnochage){
					$_entryob->setVar('datesub' , $datesub ) ;
				}
			}

		if ( isset($_POST['headline']) ) { $_entryob->setVar('headline' , $_POST['headline'] ) ; }
		if ( isset($_POST['lead']) ) { $_entryob->setVar('lead' , $_POST['lead'] ) ; }
		if ( isset($_POST['bodytext']) ) { $_entryob->setVar('bodytext' , $_POST['bodytext'] ) ; }
		if ( isset($_POST['artimage']) ) { $_entryob->setVar('artimage' , $_POST['artimage'] ) ; }

		//autoapprove
		if ( $xoopsModuleConfig['autoapprove'] != 1 ||  $thisgrouptype == XOOPS_GROUP_ANONYMOUS ) {
			$_entryob->setVar('submit' , 1 ) ;
			$_entryob->setVar('offline' , 1 ) ;
		} else{
			$_entryob->setVar('submit' , 0 ) ;
			if ( isset($_POST['submit']) ) {
				$_entryob->setVar('submit' , intval( $_POST['submit'] ) ) ;
			}
			$_entryob->setVar('offline' , 0 ) ;
		}
		if ( isset($_POST['teaser']) ) { $_entryob->setVar('teaser' , $_POST['teaser'] ) ; }
		$autoteaser = (isset($_POST['autoteaser'])) ? intval($_POST['autoteaser']) : 0;
		$charlength = (isset($_POST['teaseramount'])) ? intval($_POST['teaseramount']) : 0;
		if ( $autoteaser && $charlength ){
			$_entryob->setVar('teaser' , xoops_substr($_entryob->getVar('bodytext' , 'none'), 0, $charlength) ) ;
		}
		// Save to database
		if (!$_entrydata_handler->insertArticle($_entryob)) {
			redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/index.php', 2, _MD_SB_ERRORSAVINGDB );
			exit();
			break;
		}
		if ( $xoopsModuleConfig['autoapprove'] != 1 ||  $thisgrouptype == XOOPS_GROUP_ANONYMOUS ) {
			// Notify of to admin only for approve
			$_entrydata_handler->newArticleTriggerEvent($_entryob , 'article_submit' ) ;
		} else {
			// Notify of to admin only for new_article
			$_entrydata_handler->newArticleTriggerEvent($_entryob , 'new_article' ) ;
		}
		if ( $_entryob->getVar('submit') ) {
			redirect_header( "index.php", 2, _MD_SB_RECEIVED );
		} else {
			redirect_header( "index.php", 2, _MD_SB_RECEIVEDANDAPPROVED );
		}
		exit();
		break;

	case 'form':
	case 'edit':
	default:
		$name = $xoopsUser->getVar("uname");
		//-------------------------
		if ( !empty($articleID) ){
			//articleID check
			$_entryob =& $_entrydata_handler->getArticleOnePermcheck($articleID ,true ,true);
			if (!is_object($_entryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			}
			//get category object check
			//get category object
			if (!isset($_can_edit_categoryob_arr[$_entryob->getVar('columnID')])) {
				redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/index.php', 2, _MD_SB_ERRORSAVINGDB );
				exit();
			}
			$_categoryob =& $_can_edit_categoryob_arr[$_entryob->getVar('columnID')] ;
		} else {
			 // there's no parameter, so we're adding an entry
			$_entryob =& $_entrydata_handler->createArticle(true);
	        $_entryob->cleanVars() ;
		}
		//get vars mode E
		$entry_vars = $_entryob->getVars() ;
		foreach ($entry_vars as $k=>$v) {
		    $e_articles[$k] = $_entryob->getVar($k , 'E') ;
		}
		$module_img_dir = XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/icon/";
		echo "<div id='moduleName'><img src='".$module_img_dir."open.png' width='36' height='24' />&nbsp;".$xoopsModule->name()."&nbsp;<img src='".$module_img_dir."close.png' width='36' height='24' /></div><div id='pagePath'><a href='".XOOPS_URL."'>"._MD_SB_HOME."</a> &bull; <a href='".XOOPS_URL."/modules/".$xoopsModule->dirname()."/'>".$xoopsModule->name()."</a> &bull; "._MD_SB_SUBMITART."</div>";
		echo "<div style='margin: 8px 0; line-height: 160%; width: 100%;'>" . _MD_SB_GOODDAY . "<b>" . $name . "</b>, " . _MD_SB_SUB_SNEWNAMEDESC . "</div>";
		include_once './include/storyform.inc.php';

		//$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="style.css" />');
		$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="'.XOOPS_URL.'/modules/'.$xoopsModule->dirname().'/style.css" />');
		include XOOPS_ROOT_PATH . '/footer.php';
		break;
	} 
?>