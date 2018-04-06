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
use XoopsModules\Soapbox;

include __DIR__ . '/../../mainfile.php';

/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

//global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;
//----------------------------------------------
//allowsubmit
if (null !== $helper->getConfig('allowsubmit') || 1 !== $helper->getConfig('allowsubmit')) {
    redirect_header('index.php', 1, _NOPERM);
}
//guest
if (!is_object($xoopsUser)) {
    redirect_header('index.php', 1, _NOPERM);
}

//include XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/gtickets.php';

$xoopsConfig['module_cache'] = 0; //disable caching since the URL will be the same, but content different from one user to another
include XOOPS_ROOT_PATH . '/header.php';
$myts = \MyTextSanitizer:: getInstance();
//----------------------------------------------
//post op check
$op = 'form';
if (isset($_POST['post'])) {
    $op = 'post';
} elseif (isset($_POST['edit'])) {
    $op = 'edit';
}

$op = Request::getCmd('op', 'check', 'POST');

//----------------------------------------------
//post or get articleID check
$articleID = 0;
if (\Xmf\Request::hasVar('articleID', 'GET')) {
 $articleID = \Xmf\Request::getInt('articleID', 0, 'GET');
}
if (\Xmf\Request::hasVar('articleID', 'POST')) {
 $articleID = \Xmf\Request::getInt('articleID', 0, 'POST');
}
//----------------------------------------------
//user group , edit_uid
$thisgrouptype = XOOPS_GROUP_USERS;
if ($xoopsUser->isAdmin($xoopsModule->mid())) {
    $thisgrouptype = XOOPS_GROUP_ADMIN;
}
$edit_uid = $xoopsUser->getVar('uid');
$name     = $xoopsUser->getVar('uname');
//-------------------------------------
$entrydataHandler = $helper->getHandler('Entrydata');
//-------------------------------------
//get can edit category object
if (XOOPS_GROUP_ADMIN === $thisgrouptype) {
    $canEditCategoryobArray = $entrydataHandler->getColumns(null, true);
} else {
    $canEditCategoryobArray = $entrydataHandler->getColumnsByAuthor($edit_uid, true);
}
if (empty($canEditCategoryobArray) || 0 === count($canEditCategoryobArray)) {
    redirect_header('index.php', 1, _MD_SOAPBOX_NOCOLEXISTS);
}
//----------------------------------------------
//main
switch ($op) {
    case 'post':
        //-------------------------
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
        }
        //-------------------------
        //articleID check
        if (isset($_POST['articleID'])) {
            $_entryob = $entrydataHandler->getArticleOnePermcheck($articleID, true, true);
            if (!is_object($_entryob)) {
                redirect_header('index.php', 1, _NOPERM);

                break;
            }
        } else {
            $_entryob = $entrydataHandler->createArticle(true);
            $_entryob->cleanVars();
        }

        //set
        $_entryob->setVar('uid', $edit_uid);
        if (isset($_POST['columnID'])) {
            $_entryob->setVar('columnID', \Xmf\Request::getInt('columnID', 0, 'POST'));
        }
        //get category object
        if (!isset($canEditCategoryobArray[$_entryob->getVar('columnID')])) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php', 2, _NOPERM);
        }
        $_categoryob = $canEditCategoryobArray[$_entryob->getVar('columnID')];
        //checkbox not post when value is false
        if (XOOPS_GROUP_ADMIN !== $thisgrouptype) {
            $_entryob->setVar('html', 0);
            $_entryob->setVar('smiley', 1);
            $_entryob->setVar('xcodes', 1);
            $_entryob->setVar('breaks', 1);
        }

        if (isset($_POST['weight'])) {
            $_entryob->setVar('weight', \Xmf\Request::getInt('weight', 0, 'POST'));
        }

        if (isset($_POST['commentable'])) {
            $_entryob->setVar('commentable', \Xmf\Request::getInt('commentable', 0, 'POST'));
        }
        if (isset($_POST['offline'])) {
            $_entryob->setVar('offline', \Xmf\Request::getInt('offline', 0, 'POST'));
        }
        if (isset($_POST['block'])) {
            $_entryob->setVar('block', \Xmf\Request::getInt('block', 0, 'POST'));
        }
        if (isset($_POST['notifypub'])) {
            $_entryob->setVar('notifypub', \Xmf\Request::getInt('notifypub', 0, 'POST'));
        }

        //datesub
        $datesubnochage  = \Xmf\Request::getInt('datesubnochage', 0, 'POST');
        $datesub_date_sl = isset($_POST['datesub']) ? (int)strtotime($_POST['datesub']['date']) : 0;
        $datesub_time_sl = \Xmf\Request::getInt('datesub', 0, 'POST');
        $datesub         = isset($_POST['datesub']) ? $datesub_date_sl + $datesub_time_sl : 0;
        if (!$datesub || $_entryob->_isNew) {
            $_entryob->setVar('datesub', time());
        } else {
            if (!$datesubnochage) {
                $_entryob->setVar('datesub', $datesub);
            }
        }

        if (isset($_POST['headline'])) {
            $_entryob->setVar('headline', $_POST['headline']);
        }
        if (isset($_POST['lead'])) {
            $_entryob->setVar('lead', $_POST['lead']);
        }
        if (isset($_POST['bodytext'])) {
            $_entryob->setVar('bodytext', $_POST['bodytext']);
        }
        if (isset($_POST['artimage'])) {
            $_entryob->setVar('artimage', $_POST['artimage']);
        }

        //autoapprove
        if (XOOPS_GROUP_ANONYMOUS === $thisgrouptype || 1 !== $helper->getConfig('autoapprove')) {
            $_entryob->setVar('submit', 1);
            $_entryob->setVar('offline', 1);
        } else {
            $_entryob->setVar('submit', 0);
            if (isset($_POST['submit'])) {
                $_entryob->setVar('submit', \Xmf\Request::getInt('submit', 0, 'POST'));
            }
            $_entryob->setVar('offline', 0);
        }
        if (isset($_POST['teaser'])) {
            $_entryob->setVar('teaser', $_POST['teaser']);
        }
        $autoteaser = \Xmf\Request::getInt('autoteaser', 0, 'POST');
        $charlength = \Xmf\Request::getInt('teaseramount', 0, 'POST');
        if ($autoteaser && $charlength) {
            $_entryob->setVar('teaser', xoops_substr($_entryob->getVar('bodytext', 'none'), 0, $charlength));
        }
        // Save to database
        if (!$entrydataHandler->insertArticle($_entryob)) {
            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php', 2, _MD_SOAPBOX_ERRORSAVINGDB);

            break;
        }
        if (XOOPS_GROUP_ANONYMOUS === $thisgrouptype || 1 !== $helper->getConfig('autoapprove')) {
            // Notify of to admin only for approve
            $entrydataHandler->newArticleTriggerEvent($_entryob, 'article_submit');
        } else {
            // Notify of to admin only for new_article
            $entrydataHandler->newArticleTriggerEvent($_entryob, 'new_article');
        }
        if ($_entryob->getVar('submit')) {
            redirect_header('index.php', 2, _MD_SOAPBOX_RECEIVED);
        } else {
            redirect_header('index.php', 2, _MD_SOAPBOX_RECEIVEDANDAPPROVED);
        }
        exit();
        break;

    case 'form':
    case 'edit':
    default:
        $name = $xoopsUser->getVar('uname');
        //-------------------------
        if (!empty($articleID)) {
            //articleID check
            $_entryob = $entrydataHandler->getArticleOnePermcheck($articleID, true, true);
            if (!is_object($_entryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            //get category object check
            //get category object
            if (!isset($canEditCategoryobArray[$_entryob->getVar('columnID')])) {
                redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php', 2, _MD_SOAPBOX_ERRORSAVINGDB);
            }
            $_categoryob = $canEditCategoryobArray[$_entryob->getVar('columnID')];
        } else {
            // there's no parameter, so we're adding an entry
            $_entryob = $entrydataHandler->createArticle(true);
            $_entryob->cleanVars();
        }
        //get vars mode E
        $entry_vars = $_entryob->getVars();
        foreach ($entry_vars as $k => $v) {
            $e_articles[$k] = $_entryob->getVar($k, 'E');
        }
        $module_img_dir = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/assets/images/icon/';
        echo "<div id='moduleName'><img src='"
             . $module_img_dir
             . "open.png' width='36' height='24'>&nbsp;"
             . $xoopsModule->name()
             . "&nbsp;<img src='"
             . $module_img_dir
             . "close.png' width='36' height='24'></div><div id='pagePath'><a href='"
             . XOOPS_URL
             . "'>"
             . _MD_SOAPBOX_HOME
             . "</a> &bull; <a href='"
             . XOOPS_URL
             . '/modules/'
             . $xoopsModule->dirname()
             . "/'>"
             . $xoopsModule->name()
             . '</a> &bull; '
             . _MD_SOAPBOX_SUBMITART
             . '</div>';
        echo "<div style='margin: 8px 0; line-height: 160%; width: 100%;'>" . _MD_SOAPBOX_GOODDAY . '<b>' . $name . '</b>, ' . _MD_SOAPBOX_SUB_SNEWNAMEDESC . '</div>';
        require_once __DIR__ . '/include/storyform.inc.php';

        //$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="style.css">');
        $xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/assets/css/style.css">');
        include XOOPS_ROOT_PATH . '/footer.php';
        break;
}
