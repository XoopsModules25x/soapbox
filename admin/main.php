<?php
/**
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Soapbox;

require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();

/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

$op = '';
//if (\Xmf\Request::hasVar('op', 'GET')) {
//    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
//}
//if (\Xmf\Request::hasVar('op', 'POST')) {
//    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
//}

$op = Request::getString('op', Request::getCmd('op', '', 'POST'), 'GET');

$entries = Request::getInt('entries', 0, 'POST'); //isset($_POST['entries']) ? (int)($_POST['entries']) : 0;

/* Available operations */
switch ($op) {
    case 'default':
    default:
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        $startart = Request::getInt('startart', 0, 'GET'); //isset($_GET['startart']) ? (int)($_GET['startart']) : 0;
        $startcol = Request::getInt('startcol', 0, 'GET'); //isset($_GET['startcol']) ? (int)($_GET['startcol']) : 0;
        $startsub = Request::getInt('startsub', 0, 'GET'); //isset($_GET['startsub']) ? (int)($_GET['startsub']) : 0;
        $datesub  = Request::getInt('datesub', 0, 'GET'); //isset($_GET['datesub']) ? (int)($_GET['datesub']) : 0;

        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        $adminObject->addItemButton(_MI_SOAPBOX_ADD_ARTICLE, 'article.php', 'add', '');
        $adminObject->addItemButton(_MI_SOAPBOX_ADD_COLUMN, 'column.php', 'add', '');
        $adminObject->displayButton('left', '');

        require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        //        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';
        $module_id = $xoopsModule->getVar('mid');

        Soapbox\Utility::showArticles($helper->getConfig('buttonsadmin'));
        Soapbox\Utility::showColumns($helper->getConfig('buttonsadmin'));
}

require_once __DIR__ . '/admin_footer.php';
