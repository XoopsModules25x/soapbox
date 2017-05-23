<?php
//

/**
 *
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
//if (!isset($_POST['submit'])) {
//    exit;
//}
//if (!isset($_POST['lid'])) {
//    exit;
//}

if (!Request::hasVar('submit', 'POST') || !Request::hasVar('lid', 'POST')) {
    exit;
}

if (Request::hasVar('submit', 'POST')) { //($_POST['submit']) {
    //-------------------------
    //    if (!$GLOBALS['xoopsSecurity']->check()) {
    if (!$xoopsGTicket->check()) {
        redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
    }
    //-------------------------
    $ratinguser = is_object($xoopsUser) ? $xoopsUser->uid() : 0;
    if (function_exists('floatval')) {
        $rating = $_POST['rating'] ? (float)$_POST['rating'] : 0;
    } else {
        $rating = $_POST['rating'] ? (int)$_POST['rating'] : 0;
    }
    $lid = $_POST['lid'] ? (int)$_POST['lid'] : 0;

    // Make sure only 1 anonymous from an IP in a single day.
    $anonwaitdays = 1;
    $ip           = getenv('REMOTE_ADDR');
    // Check if Rating is Null
    if (empty($rating) || empty($lid)) {
        redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_NORATING);
    }

    //module entry data handler
    $entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());
    //get entry object
    $_entryob = $entrydataHandler->getArticleOnePermcheck($lid, true);
    if (!is_object($_entryob)) {
        redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php', 1, _MD_SOAPBOX_CANTVOTEOWN);
    }
    // Check if Download POSTER is voting (UNLESS Anonymous users allowed to post)
    if ($ratinguser !== 0) {
        //get category object
        $_categoryob = $_entryob->_sbcolumns;
        if (!is_object($_categoryob)) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/index.php', 1, 'no column');
        }
        if ($_categoryob->getVar('author') === $ratinguser) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_CANTVOTEOWN);
        }

        //uid check
        //uid check
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('lid', $lid));
        $criteria->add(new Criteria('ratinguser', $ratinguser));
        $ratinguservotecount = $entrydataHandler->getVotedataCount($criteria);
        unset($criteria);
        if ($ratinguservotecount > 0) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_VOTEONCE);
        }
    }

    // Check if ANONYMOUS user is trying to vote more than once per day.
    if ($ratinguser === 0) {
        $yesterday = (time() - (86400 * $anonwaitdays));
        //uid check
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('lid', $lid));
        $criteria->add(new Criteria('ratinguser', 0));
        $criteria->add(new Criteria('ratinghostname', $ip));
        $criteria->add(new Criteria('ratingtimestamp', $yesterday, '>'));
        $anonvotecount = $entrydataHandler->getVotedataCount($criteria);
        unset($criteria);
        if ($anonvotecount > 0) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_VOTEONCE);
        }
    }

    $_votedataob = $entrydataHandler->createVotedata(true);
    $_votedataob->cleanVars();
    $_votedataob->setVar('lid', $lid);
    $_votedataob->setVar('ratinguser', $ratinguser);
    $_votedataob->setVar('rating', $rating);
    $_votedataob->setVar('ratinghostname', $ip);
    $_votedataob->setVar('ratingtimestamp', time());
    // Save to database
    if (!$entrydataHandler->insertVotedata($_votedataob, true)) {
        redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_CANTVOTEOWN);
    }

    // All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
    //    updaterating( $lid );
    if (!$entrydataHandler->updateRating($_entryob)) {
        redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_UNKNOWNERROR);
    } else {
        $ratemessage = _MD_SOAPBOX_VOTEAPPRE . '<br>' . sprintf(_MD_SOAPBOX_THANKYOU, $myts->htmlSpecialChars($xoopsConfig['sitename']));
        redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, $ratemessage);
    }
    //    exit();
} else {
    redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?articleID=' . $lid, 1, _MD_SOAPBOX_UNKNOWNERROR);
    //    exit();
}
