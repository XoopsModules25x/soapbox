<?php
/**
 *
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;

require_once __DIR__ . '/header.php';
$op = '';
if (is_object($xoopsUser)) {
    $xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
}
$GLOBALS['xoopsOption']['template_main'] = 'sb_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$moduleDirName = basename(__DIR__);

//$moduleDirName =  & $myts->htmlSpecialChars(basename(__DIR__));
//if ($moduleDirName !== "soapbox" && $moduleDirName !== "" && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
//    echo("invalid dirname: " . htmlspecialchars($moduleDirName, ENT_QUOTES));
//}

//---GET view sort --
$sortname = isset($_GET['sortname']) ? strtolower(trim(strip_tags($myts->stripSlashesGPC($_GET['sortname'])))) : 'datesub';
if (!in_array($sortname, ['datesub', 'weight', 'counter', 'rating', 'headline'])) {
    $sortname = 'datesub';
}
$sortorder = isset($_GET['sortorder']) ? strtoupper(trim(strip_tags($myts->stripSlashesGPC($_GET['sortorder'])))) : 'DESC';
if (!in_array($sortorder, ['ASC', 'DESC'])) {
    $sortorder = 'DESC';
}
//---------------
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$start = Request::getInt('start', 0, 'GET'); //$start = isset($_GET['start']) ? (int)($_GET['start']) : 0;
//---------------

$columna = [];
// Options
switch ($op) {
    case 'default':
    default:
        //-------------------------------------
        $entrydataHandler = xoops_getModuleHandler('entryget', $moduleDirName);
        //-------------------------------------

        $author = isset($_GET['author']) ? (int)$_GET['author'] : 0;
        //get category object
        if (!empty($author)) {
            $categoryobArray = $entrydataHandler->getColumnsByAuthor($author, true, (int)$xoopsModuleConfig['colsperindex'], $start, 'weight', 'ASC');
            $totalcols       = $entrydataHandler->total_getColumnsByAuthor;
        } else {
            //get category object
            $categoryobArray = $entrydataHandler->getColumnsAllPermcheck((int)$xoopsModuleConfig['colsperindex'], $start, true, 'weight', 'ASC', null, null, true, false);
            $totalcols       = $entrydataHandler->total_getColumnsAllPermcheck;
        }
        $xoopsTpl->assign('lang_mainhead', $myts->htmlSpecialChars($xoopsModuleConfig['introtitle']));
        $xoopsTpl->assign('lang_maintext', $myts->htmlSpecialChars($xoopsModuleConfig['introtext']));
        $xoopsTpl->assign('lang_modulename', $xoopsModule->name());
        $xoopsTpl->assign('lang_moduledirname', $moduleDirName);
        $xoopsTpl->assign('imgdir', $myts->htmlSpecialChars($xoopsModuleConfig['sbimgdir']));
        $xoopsTpl->assign('uploaddir', $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']));

        //----------------------------
        if ($totalcols === 0) {
            $xoopsTpl->assign('lang_nothing', _MD_SOAPBOX_NOTHING);
        }
        //----------------------------
        foreach ($categoryobArray as $_categoryob) {
            //----------------------------
            $category = $_categoryob->toArray(); //all assign
            //-------------------------------------
            //get author
            $category['authorname'] = SoapboxUtility::getAuthorName($category['author']);
            //-------------------------------------
            if ($category['colimage'] !== '') {
                $category['imagespan'] = '<span class="picleft"><img class="pic" src="' . XOOPS_URL . '/' . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . '/' . $category['colimage'] . '"></span>';
            } else {
                $category['imagespan'] = '';
            }
            //-------------------------------------
            $_entryob_arr          = $entrydataHandler->getArticlesAllPermcheck(1, 0, true, true, 0, 0, null, $sortname, $sortorder, $_categoryob, null, true, false);
            $totalarts             = $entrydataHandler->total_getArticlesAllPermcheck;
            $category['totalarts'] = $totalarts;
            //------------------------------------------------------
            foreach ($_entryob_arr as $_entryob) {
                //-----------
                unset($articles);
                $articles = [];
                //get vars
                $articles            = $_entryob->toArray();
                $articles['id']      = $articles['articleID'];
                $articles['datesub'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $xoopsModuleConfig['dateformat']));
                //        $articles['poster'] = XoopsUserUtility::getUnameFromId( $articles['uid'] );
                $articles['poster']   = SoapboxUtility::getLinkedUnameFromId($category['author']);
                $articles['bodytext'] = xoops_substr($articles['bodytext'], 0, 255);
                //--------------------
                if ($articles['submit'] !== 0) {
                    $articles['headline'] = '[' . _MD_SOAPBOX_SELSUBMITS . ']' . $articles['headline'];
                    $articles['teaser']   = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                    $articles['lead']     = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                } elseif (0 === $_entryob->getVar('datesub') || $_entryob->getVar('datesub') > time()) {
                    $articles['headline'] = '[' . _MD_SOAPBOX_SELWAITEPUBLISH . ']' . $articles['headline'];
                    $articles['teaser']   = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                    $articles['lead']     = $xoopsUser->getVar('uname') . _MD_SOAPBOX_SUB_SNEWNAMEDESC;
                }

                // Functional links
                $articles['adminlinks'] = $entrydataHandler->getadminlinks($_entryob, $_categoryob);
                $articles['userlinks']  = $entrydataHandler->getuserlinks($_entryob);
                //loop tail
                $category['content'][] = $articles;
            }

            $category['total']  = $totalcols;
            $pagenav            = new XoopsPageNav($category['total'], (int)$xoopsModuleConfig['colsperindex'], $start, 'start', '');
            $category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

            $xoopsTpl->append_by_ref('cols', $category);
            unset($category);
        }
}
//$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="style.css">');
$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $moduleDirName . '/assets/css/style.css">');

include XOOPS_ROOT_PATH . '/footer.php';
