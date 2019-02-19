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

require __DIR__ . '/header.php';

$moduleDirName = basename(__DIR__);

/** @var \XoopsModules\Soapbox\Helper $helper */
$helper = \XoopsModules\Soapbox\Helper::getInstance();

$op = '';
if (is_object($xoopsUser)) {
    $xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
}
$GLOBALS['xoopsOption']['template_main'] = 'sb_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

//$moduleDirName =  & $myts->htmlSpecialChars(basename(__DIR__));
//if ($moduleDirName !== "soapbox" && $moduleDirName !== "" && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
//    echo("invalid dirname: " . htmlspecialchars($moduleDirName, ENT_QUOTES));
//}

//---GET view sort --
$sortname = \Xmf\Request::getString('sortname', 'datesub', 'GET');
if (!in_array($sortname, ['datesub', 'weight', 'counter', 'rating', 'headline'], true)) {
    $sortname = 'datesub';
}
$sortorder = \Xmf\Request::getString('sortorder', 'DESC', 'GET');
if (!in_array($sortorder, ['ASC', 'DESC'], true)) {
    $sortorder = 'DESC';
}
//---------------
require XOOPS_ROOT_PATH . '/class/pagenav.php';
$start = \Xmf\Request::getInt('start', 0, 'GET'); //$start = isset($_GET['start']) ? (int)($_GET['start']) : 0;
//---------------

$category = [];
$articles = [];
// Options
switch ($op) {
    case 'default':
    default:
        //-------------------------------------
        $entrydataHandler = $helper->getHandler('Entryget');
        //-------------------------------------

        $author = \Xmf\Request::getInt('author', 0, 'GET');
        //get category object
        if (!empty($author)) {
            $categoryobArray = $entrydataHandler->getColumnsByAuthor($author, true, (int)$helper->getConfig('colsperindex'), $start, 'weight', 'ASC');
            $totalcols       = $entrydataHandler->total_getColumnsByAuthor;
        } else {
            //get category object
            $categoryobArray = $entrydataHandler->getColumnsAllPermcheck((int)$helper->getConfig('colsperindex'), $start, true, 'weight', 'ASC', null, null, true, false);
            $totalcols       = $entrydataHandler->total_getColumnsAllPermcheck;
        }
        $xoopsTpl->assign('lang_mainhead', $myts->htmlSpecialChars($helper->getConfig('introtitle')));
        $xoopsTpl->assign('lang_maintext', $myts->htmlSpecialChars($helper->getConfig('introtext')));
        $xoopsTpl->assign('lang_modulename', $xoopsModule->name());
        $xoopsTpl->assign('lang_moduledirname', $moduleDirName);
        $xoopsTpl->assign('imgdir', $myts->htmlSpecialChars($helper->getConfig('sbimgdir')));
        $xoopsTpl->assign('uploaddir', $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));

        //----------------------------
        if (0 === $totalcols) {
            $xoopsTpl->assign('lang_nothing', _MD_SOAPBOX_NOTHING);
        }
        //----------------------------
        foreach ($categoryobArray as $categoryob) {
            //----------------------------
            $category = $categoryob->toArray(); //all assign
            //-------------------------------------
            //get author
            $category['authorname'] = Soapbox\Utility::getAuthorName($category['author']);
            //-------------------------------------
            if ('' !== $category['colimage']) {
                $category['imagespan'] = '<span class="picleft"><img class="pic" src="' . XOOPS_URL . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $category['colimage'] . '"></span>';
            } else {
                $category['imagespan'] = '';
            }
            //-------------------------------------
            $entryobArray          = $entrydataHandler->getArticlesAllPermcheck(1, 0, true, true, 0, 0, null, $sortname, $sortorder, $categoryob, null, true, false);
            $totalarts             = $entrydataHandler->total_getArticlesAllPermcheck;
            $category['totalarts'] = $totalarts;
            //------------------------------------------------------
            foreach ($entryobArray as $entryob) {
                //-----------
                unset($articles);
                //get vars
                $articles            = $entryob->toArray();
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
                } elseif (0 === $entryob->getVar('datesub') || $entryob->getVar('datesub') > time()) {
                    $articles['headline'] = '[' . _MD_SOAPBOX_SELWAITEPUBLISH . ']' . $articles['headline'];
                    $articles['teaser']   = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                    $articles['lead']     = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                }

                // Functional links
                $articles['adminlinks'] = $entrydataHandler->getadminlinks($entryob, $categoryob);
                $articles['userlinks']  = $entrydataHandler->getuserlinks($entryob);
                //loop tail
                $category['content'][] = $articles;
                //                unset($articles);
            }

            $category['total']  = $totalcols;
            $pagenav            = new \XoopsPageNav($category['total'], (int)$helper->getConfig('colsperindex'), $start, 'start', '');
            $category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

            $xoopsTpl->append_by_ref('cols', $category);
            unset($category);
        }
}
//$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="style.css">');
$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $moduleDirName . '/assets/css/style.css">');

require XOOPS_ROOT_PATH . '/footer.php';
