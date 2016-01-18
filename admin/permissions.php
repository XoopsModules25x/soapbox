<?php
// $Id: permissions.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: permissions.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

include_once __DIR__ . '/admin_header.php';
//include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

include_once 'mygrouppermform.php';

$indexAdmin = new ModuleAdmin();
$op         = '';
if (isset($_GET['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
}
if (isset($_POST['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
}

switch ($op) {
    case "default":
    default:
        $item_list2 = array();
        $block2     = array();

        xoops_cp_header();
        echo $indexAdmin->addNavigation('permissions.php');
        //adminMenu(4, _AM_SOAPBOX_PERMS);
        //        echo "<h3 style='color: #2F5376; '>". _AM_SOAPBOX_PERMSMNGMT . "</h3>";

        //-------------------------------------
        //get category object
        $_hcategory_handler = &xoops_getmodulehandler('sbcolumns', $xoopsModule->dirname());
        $totalcols          = $_hcategory_handler->getCount();
        if (!empty($totalcols)) {
            //----------------------------
            $criteria = new CriteriaCompo();
            $criteria->setSort('weight');
            $_categoryob_arr =& $_hcategory_handler->getObjects($criteria);
            unset($criteria);
            foreach ($_categoryob_arr as $_categoryob) {
                $item_list2['cid']   = $_categoryob->getVar('columnID');
                $item_list2['title'] = $_categoryob->getVar('name');
                $form2               = new MyXoopsGroupPermForm("", $xoopsModule->getVar('mid'), "Column permissions", _AM_SOAPBOX_SELECT_COLS, 'admin/permissions.php');
                $block2[]            = $item_list2;
                foreach ($block2 as $itemlists) {
                    $form2->addItem($itemlists['cid'], $itemlists['title']);
                }
            }
            echo $form2->render();
        } else {
            echo '<p><div style="text-align:center;"><b>' . _AM_SOAPBOX_NOPERMSSET . '</b></div></p>';
        }
        echo _AM_SOAPBOX_PERMSNOTE;
}
include_once __DIR__ . '/admin_footer.php';
