<?php
// $Id: ratefile.inc.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: ratefile.inc.php v 1.5 2005/2/25 domifara Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
if (!isset( $_POST['submit'] ) ) exit ;
if (!isset( $_POST['lid'] ) ) exit ;

if ( $_POST['submit'] ){
	//-------------------------
	if ( ! $xoopsGTicket->check() ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	//-------------------------
	$ratinguser = ( is_object( $xoopsUser ) ) ? $xoopsUser -> uid() : 0;
	if ( function_exists('floatval') ) {
		$rating = ( $_POST['rating'] ) ? floatval($_POST['rating']) : 0; 
	} else {
		$rating = ( $_POST['rating'] ) ? intval($_POST['rating']) : 0; 
	}
	$lid = ( $_POST['lid'] ) ? intval($_POST['lid']) : 0; 

	// Make sure only 1 anonymous from an IP in a single day.
	$anonwaitdays = 1;
	$ip = getenv( "REMOTE_ADDR" ) ;
	// Check if Rating is Null
	if ( empty($rating) || empty($lid) ){
		redirect_header(XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_NORATING );
		exit();
	} 

	//module entry data handler 
	$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());
	//get entry object
	$_entryob =& $_entrydata_handler->getArticleOnePermcheck($lid ,true);
	if (!is_object($_entryob) ) {
		redirect_header(XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php', 1 , _MD_SB_CANTVOTEOWN );
		exit();
	}
	// Check if Download POSTER is voting (UNLESS Anonymous users allowed to post)
	if ( $ratinguser != 0 ){
		//get category object
		$_categoryob =& $_entryob->_sbcolumns;
		if (!is_object($_categoryob) ){
			redirect_header( XOOPS_URL."/modules/".$xoopsModule->dirname()."/index.php", 1, "no column" );
			exit();
		}
        if ( $_categoryob->getVar('author') == $ratinguser) {
			redirect_header(XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_CANTVOTEOWN );
			exit();
        }

		//uid check
		//uid check
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('lid', $lid ));
		$criteria->add(new Criteria('ratinguser', $ratinguser ));
		$ratinguservotecount =& $_entrydata_handler->getVotedataCount($criteria) ;
		unset($criteria);
		if ( $ratinguservotecount > 0) {
					redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_VOTEONCE );
		}
	}

	// Check if ANONYMOUS user is trying to vote more than once per day.
	if ( $ratinguser == 0 ){
		$yesterday = ( time() - ( 86400 * $anonwaitdays ) );
		//uid check
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('lid', $lid ));
		$criteria->add(new Criteria('ratinguser', 0 ));
		$criteria->add(new Criteria('ratinghostname', $ip ));
		$criteria->add(new Criteria('ratingtimestamp', $yesterday , '>'));
		$anonvotecount =& $_entrydata_handler->getVotedataCount($criteria) ;
		unset($criteria);
		if ( $anonvotecount > 0 ){
			redirect_header(XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_VOTEONCE );
			exit();
		} 
	} 

	$_votedataob =& $_entrydata_handler->createVotedata(true);
    $_votedataob->cleanVars() ;
	$_votedataob->setVar('lid' , $lid ) ;
	$_votedataob->setVar('ratinguser' , $ratinguser ) ;
	$_votedataob->setVar('rating' , $rating ) ;
	$_votedataob->setVar('ratinghostname' , $ip ) ;
	$_votedataob->setVar('ratingtimestamp' , time() ) ;
	// Save to database
	if (!$_entrydata_handler->insertVotedata($_votedataob , true)) {
		redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_CANTVOTEOWN );
		exit();
	}

	// All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
//	updaterating( $lid );
	if (!$_entrydata_handler->updateRating($_entryob) ){
		redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_UNKNOWNERROR );
	} else {
		$ratemessage = _MD_SB_VOTEAPPRE . "<br>" . sprintf( _MD_SB_THANKYOU, $myts->htmlSpecialChars($xoopsConfig['sitename']) );
		redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , $ratemessage );
	}
	exit();
} else {
	redirect_header( XOOPS_URL . '/modules/' . $xoopsModule -> getVar( 'dirname' ) . '/article.php?articleID='.$lid, 1 , _MD_SB_UNKNOWNERROR );
	exit();
} 
?>