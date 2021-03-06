<?php
/**
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Soapbox;

require_once __DIR__ . '/header.php';

/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

$op = '';
//HACK for cache by domifara
if (is_object($xoopsUser)) {
    $xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
}

$GLOBALS['xoopsOption']['template_main'] = 'sb_column.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$moduleDirName = $myts->htmlSpecialChars(basename(__DIR__));
if ('soapbox' !== $moduleDirName && '' !== $moduleDirName && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES));
}

$columnID = Request::getInt('columnID', 0, 'GET');
//---GET view sort --
$sortname = isset($_GET['sortname']) ? mb_strtolower(trim(strip_tags($myts->stripSlashesGPC($_GET['sortname'])))) : 'datesub';
if (!in_array($sortname, ['datesub', 'weight', 'counter', 'rating', 'headline'], true)) {
    $sortname = 'datesub';
}
$sortorder = isset($_GET['sortorder']) ? mb_strtoupper(trim(strip_tags($myts->stripSlashesGPC($_GET['sortorder'])))) : 'DESC';
if (!in_array($sortorder, ['ASC', 'DESC'], true)) {
    $sortorder = 'DESC';
}
//---------------
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$start = Request::getInt('start', 0, 'GET');

//-------------------------------------
/** @var \XoopsModules\Soapbox\EntrygetHandler $entrydataHandler */
$entrydataHandler = new \XoopsModules\Soapbox\EntrygetHandler();
//-------------------------------------
$entryobArray = $entrydataHandler->getArticlesAllPermcheck((int)$helper->getConfig('indexperpage'), $start, true, true, 0, 0, null, $sortname, $sortorder, $columnID, null, true, false);
$totalarts    = $entrydataHandler->total_getArticlesAllPermcheck;
if (empty($entryobArray) || 0 === $totalarts) {
    redirect_header(XOOPS_URL . '/modules/' . $moduleDirName . '/index.php', 1, _MD_SOAPBOX_MAINNOTOPICS);
}
//get category object
$_categoryob = $entryobArray[0]->_sbcolumns;
//get vars

$category = [];
$category = $_categoryob->toArray(); //all assign

$category['colid']      = $columnID;
$category['author']     = Soapbox\Utility::getLinkedUnameFromId($category['author'], 0);
$category['authorname'] = Soapbox\Utility::getAuthorName($category['author']);
$category['image']      = $category['colimage'];
$category['total']      = $totalarts;
$xoopsTpl->assign('category', $category);

//------------------------------------------------------
foreach ($entryobArray as $_entryob) {
    //-----------
    unset($articles);
    $articles = [];
    //get vars
    $articles = $_entryob->toArray();
    //--------------------
    $articles['id']      = $articles['articleID'];
    $articles['datesub'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $helper->getConfig('dateformat')));
    //        $articles['poster'] = XoopsUserUtility::getUnameFromId( $articles['uid'] );
    $articles['poster']   = Soapbox\Utility::getLinkedUnameFromId($category['author']);
    $articles['bodytext'] = xoops_substr($articles['bodytext'], 0, 255);
    //--------------------
    if (0 !== $articles['submit']) {
        $articles['headline'] = '[' . _MD_SOAPBOX_SELSUBMITS . ']' . $articles['headline'];
        $articles['teaser']   = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
        $articles['lead']     = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
    } elseif (0 === $_entryob->getVar('datesub') || $_entryob->getVar('datesub') > time()) {
        $articles['headline'] = '[' . _MD_SOAPBOX_SELWAITEPUBLISH . ']' . $articles['headline'];
        $articles['teaser']   = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
        $articles['lead']     = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
    }
    //--------------------
    if (!empty($articles['artimage']) && 'blank.png' !== $articles['artimage']
        && file_exists(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $articles['artimage'])) {
        $articles['image'] = XOOPS_URL . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $articles['artimage'];
    } else {
        $articles['image'] = '';
    }

    if (1 === $helper->getConfig('includerating')) {
        $xoopsTpl->assign('showrating', 1);
        $rating = $articles['rating'];
        $votes  = $articles['votes'];
        if (0.00 != $rating) {
            $articles['rating'] = _MD_SOAPBOX_RATING . ': ' . $myts->htmlSpecialChars(number_format($rating, 2));
            $articles['votes']  = _MD_SOAPBOX_VOTES . ': ' . $myts->htmlSpecialChars($votes);
        } else {
            $articles['rating'] = _MD_SOAPBOX_RATING . ': 0.00';
            $articles['votes']  = _MD_SOAPBOX_VOTES . ': 0';
        }
    }
    //--------------------
    // Functional links
    $articles['adminlinks'] = $entrydataHandler->getadminlinks($_entryob, $_categoryob);
    $articles['userlinks']  = $entrydataHandler->getuserlinks($_entryob);

    $xoopsTpl->append('articles', $articles);
}

$pagenav            = new \XoopsPageNav($totalarts, (int)$helper->getConfig('indexperpage'), $start, 'start', 'columnID=' . $articles['columnID'] . '&sortname=' . $sortname . '&sortorder=' . $sortorder);
$category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

$xoopsTpl->assign('xoops_pagetitle', $category['name']);
$xoopsTpl->assign('category', $category);

$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
$xoopsTpl->assign('lang_moduledirname', $moduleDirName);
$xoopsTpl->assign('imgdir', $myts->htmlSpecialChars($helper->getConfig('sbimgdir')));
$xoopsTpl->assign('uploaddir', $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));

$xoopsTpl->assign('sortname', $sortname);
$xoopsTpl->assign('sortorder', $sortorder);

$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $moduleDirName . '/assets/css/style.css">');

require_once XOOPS_ROOT_PATH . '/footer.php';
