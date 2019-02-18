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

$cleantags = new Soapbox\Cleantags();

$xoopsConfig['module_cache']             = 0; //disable caching since the URL will be the same, but content different from one user to another
$GLOBALS['xoopsOption']['template_main'] = 'sb_article.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
global $xoopsModule;
//$pathIcon16 = $xoopsModule->getInfo('sysicons16');
$pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);

$moduleDirName = $myts->htmlSpecialChars(basename(__DIR__));
if ('soapbox' !== $moduleDirName && '' !== $moduleDirName && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES));
}
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
//require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/include/cleantags.php';
//for ratefile update by domifara
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
//require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/include/gtickets.php';

$articleID = Request::getInt('articleID', 0, 'GET'); //isset($_GET['articleID']) ? (int)($_GET['articleID']) : 0;
$startpage = Request::getInt('page', 0, 'GET'); //isset($_GET['page']) ? (int)($_GET['page']) : 0;
//-------------------------------------
//move here  form ratefile.php
if (\Xmf\Request::hasVar('submit', 'POST') && !empty($_POST['lid'])) {
    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/include/ratefile.inc.php')) {
        require XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/include/ratefile.inc.php';
    }
    trigger_error('not updated rate :');
    exit();
}
//-------------------------------------
//view start
$articles = [];
$category = [];
//module entry data handler
$entrydataHandler = $helper->getHandler('Entryget');
if (empty($articleID)) {
    //get entry object
    $_entryob_arr = $entrydataHandler->getArticlesAllPermcheck(1, 0, true, true, 0, 0, null, $sortname, $sortorder, null, null, true, false);
    //    $totalarts = $entrydataHandler->total_getArticlesAllPermcheck;
    if (empty($_entryob_arr) || 0 === count($_entryob_arr)) {
        redirect_header(XOOPS_URL . '/modules/' . $moduleDirName . '/index.php', 1, _MD_SOAPBOX_NOTHING);
    }
    $_entryob = $_entryob_arr[0];
} else {
    //get entry object
    $_entryob = $entrydataHandler->getArticleOnePermcheck($articleID, true, true);
    if (!is_object($_entryob)) {
        redirect_header(XOOPS_URL . '/modules/' . $moduleDirName . '/index.php', 1, 'Not Found');
    }
}
//-------------------------------------
$articles = $_entryob->toArray();
//get category object
$_categoryob = $_entryob->_sbcolumns;
//get vars
$category = $_categoryob->toArray();
//-------------------------------------
//update count
$entrydataHandler->getUpArticlecount($_entryob, true);

//assign
$articles['id']     = $articles['articleID'];
$articles['posted'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $helper->getConfig('dateformat')));

// includes code by toshimitsu
if ('' !== trim($articles['bodytext'])) {
    $articletext    = explode('[pagebreak]', $_entryob->getVar('bodytext', 'none'));
    $articles_pages = count($articletext);
    if ($articles_pages > 1) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new \XoopsPageNav($articles_pages, 1, $startpage, 'page', 'articleID=' . $articles['articleID']);
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
        if (0 === $startpage) {
            $articles['bodytext'] = $articles['lead'] . '<br><br>' . $myts->displayTarea($articletext[$startpage], $articles['html'], $articles['smiley'], $articles['xcodes'], 1, $articles['breaks']);
        } else {
            $articles['bodytext'] = &$myts->displayTarea($articletext[$startpage], $articles['html'], $articles['smiley'], $articles['xcodes'], 1, $articles['breaks']);
        }
    } else {
        $articles['bodytext'] = $articles['lead'] . '<br><br>' . $myts->displayTarea($_entryob->getVar('bodytext', 'none'), $articles['html'], $articles['smiley'], $articles['xcodes'], 1, $articles['breaks']);
    }
}
//Cleantags
$articles['bodytext'] = $cleantags->cleanTags($articles['bodytext']);

if (1 === $helper->getConfig('includerating')) {
    $xoopsTpl->assign('showrating', '1');
    //-------------------------------------
    //for ratefile update by domifara
    $xoopsTpl->assign('rate_gtickets', $GLOBALS['xoopsSecurity']->getTokenHTML());
    //-------------------------------------
    if (0.0000 != $articles['rating']) {
        $articles['rating'] = '' . _MD_SOAPBOX_RATING . ': ' . $myts->htmlSpecialChars(number_format($articles['rating'], 2));
        $articles['votes']  = '' . _MD_SOAPBOX_VOTES . ': ' . $myts->htmlSpecialChars($articles['votes']);
    } else {
        $articles['rating'] = _MD_SOAPBOX_NOTRATED;
    }
}

if (is_object($xoopsUser)) {
    $xoopsTpl->assign('authorpm_link', "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . '/pmlite.php?send2=1&amp;to_userid=' . $category['author'] . "', 'pmlite', 450, 380);\"><img src='" . $pathIcon16 . "/mail_new.png' alt=\"" . _MD_SOAPBOX_WRITEAUTHOR . '"></a>');
} else {
    $xoopsTpl->assign('user_pmlink', '');
}
// Теги
if (xoops_getModuleOption('usetag', 'soapbox')) {
    $moduleHandler = xoops_getHandler('module');
    $tagsModule    = $moduleHandler->getByDirname('tag');
    if (is_object($tagsModule)) {
        require_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';

        $itemid = \Xmf\Request::getInt('articleID', 0, 'GET');
        $catid  = 0;
        $tagbar = tagBar($itemid, $catid);
        if ($tagbar) {
            $xoopsTpl->assign('tagbar', $tagbar);
            $tagsmeta = implode(' ', $tagbar['tags']);
        } else {
            $tagsmeta = '';
        }
    } else {
        $xoopsTpl->assign('tagbar', false);
        $tagsmeta = '';
    }
}
//if ( xoops_getModuleOption( 'usetag', 'soapbox') ){
//  require_once XOOPS_ROOT_PATH . '/modules/tag/include/tagbar.php';
//  $xoopsTpl->assign( 'tags', true );
//  $xoopsTpl->assign( 'tagbar', tagBar( $_REQUEST['articleID'], 0 ) );
//} else {
//  $xoopsTpl->assign( 'tags', false );
//}

// Functional links
$articles['adminlinks'] = $entrydataHandler->getadminlinks($_entryob, $_categoryob);
$articles['userlinks']  = $entrydataHandler->getuserlinks($_entryob);

$articles['author']     = Soapbox\Utility::getLinkedUnameFromId($category['author'], 0);
$articles['authorname'] = Soapbox\Utility::getAuthorName($category['author']);
$articles['colname']    = $category['name'];
$articles['coldesc']    = $category['description'];
$articles['colimage']   = $category['colimage'];

$xoopsTpl->assign('xoops_pagetitle', $articles['headline']);
$xoopsTpl->assign('story', $articles);
//-----------------------------
$mbmail_subject = sprintf(_MD_SOAPBOX_INTART, $xoopsConfig['sitename']);
$mbmail_body    = sprintf(_MD_SOAPBOX_INTARTFOUND, $xoopsConfig['sitename']);
$al             = Soapbox\Utility::getAcceptLang();
if ('ja' === $al) {
    if (function_exists('mb_convert_encoding') && function_exists('mb_encode_mimeheader')
        && @mb_internal_encoding(_CHARSET)) {
        $mbmail_subject = mb_convert_encoding($mbmail_subject, 'SJIS', _CHARSET);
        $mbmail_body    = mb_convert_encoding($mbmail_body, 'SJIS', _CHARSET);
    }
}
$mbmail_subject = rawurlencode($mbmail_subject);
$mbmail_body    = rawurlencode($mbmail_body);
//-----------------------------
$xoopsTpl->assign('mail_link', 'mailto:?subject=' . $myts->htmlSpecialChars($mbmail_subject) . '&amp;body=' . $myts->htmlSpecialChars($mbmail_body) . ':  ' . XOOPS_URL . '/modules/' . $moduleDirName . '/article.php?articleID=' . $articles['articleID']);
$xoopsTpl->assign('articleID', $articles['articleID']);
$xoopsTpl->assign('lang_ratethis', _MD_SOAPBOX_RATETHIS);
$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
$xoopsTpl->assign('lang_moduledirname', $moduleDirName);
$xoopsTpl->assign('imgdir', $myts->htmlSpecialChars($helper->getConfig('sbimgdir')));
$xoopsTpl->assign('uploaddir', $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));

//-------------------------------------
//box view
$listarts = [];
//-------------------------------------
$_other_entryob_arr = $entrydataHandler->getArticlesAllPermcheck((int)$helper->getConfig('morearts'), 0, true, true, 0, 0, null, $sortname, $sortorder, $_categoryob, $articles['articleID'], true, false);
$totalartsbyauthor  = (int)$entrydataHandler->total_getArticlesAllPermcheck + 1;

if (!empty($_other_entryob_arr)) {
    foreach ($_other_entryob_arr as $_other_entryob) {
        $link = [];
        $link = $_other_entryob->toArray();
        //--------------------
        $link['id']        = $link['articleID'];
        $link['arttitle']  = $_other_entryob->getVar('headline');
        $link['published'] = $myts->htmlSpecialChars(formatTimestamp($_other_entryob->getVar('datesub'), $helper->getConfig('dateformat')));
        //        $link['poster'] = XoopsUserUtility::getUnameFromId( $link['uid'] );
        $link['poster']      = Soapbox\Utility::getLinkedUnameFromId($category['author']);
        $link['bodytext']    = xoops_substr($link['bodytext'], 0, 255);
        $listarts['links'][] = $link;
    }
    $xoopsTpl->assign('listarts', $listarts);
    $xoopsTpl->assign('readmore', "<a style='font-size: 9px;' href=" . XOOPS_URL . '/modules/' . $moduleDirName . '/column.php?columnID=' . $articles['columnID'] . '>' . _MD_SOAPBOX_READMORE . '[' . $totalartsbyauthor . ']</a> ');
}

if (isset($GLOBALS['xoopsModuleConfig']['globaldisplaycomments'])
    && 1 === $GLOBALS['xoopsModuleConfig']['globaldisplaycomments']) {
    if (1 === $articles['commentable']) {
        require XOOPS_ROOT_PATH . '/include/comment_view.php';
    }
} else {
    require XOOPS_ROOT_PATH . '/include/comment_view.php';
}
$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $moduleDirName . '/assets/css/style.css">');

require_once XOOPS_ROOT_PATH . '/footer.php';
