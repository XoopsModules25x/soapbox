<?php

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Module: soapbox
 *
 * @category        Module
 * @package         soapbox
 * @author          XOOPS Development Team <mambax7@gmail.com> - <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @link            https://xoops.org/
 * @since           1.0.0
 */

use Xmf\Request;

require_once __DIR__ . '/admin_header.php';
//require_once __DIR__ . '/../include/config.inc.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
if ('' != Request::getString('submit', '')) {
    redirect_header(XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->dirname() . '/admin/permissions.php', 1, _MP_GPERMUPDATED);
}
// Check admin have access to this page
/*$group = $GLOBALS['xoopsUser']->getGroups ();
$groups = xoops_getModuleOption ( 'admin_groups', $thisDirname );
if (count ( array_intersect ( $group, $groups ) ) <= 0) {
    redirect_header ( 'index.php', 3, _NOPERM );
}*/

xoops_cp_header();
$adminObject = \Xmf\Module\Admin::getInstance();
$adminObject->displayNavigation(basename(__FILE__));

//$permission = soapbox_CleanVars($_POST, 'permission', 1, 'int');
$permission                = Request::getInt('permission', 1, 'POST');
$selected                  = ['', '', '', ''];
$selected[$permission - 1] = ' selected';

echo "
<form method='post' name='fselperm' action='permissions.php'>
    <table border=0>
        <tr>
            <td>
                <select name='permission' onChange='document.fselperm.submit()'>
                    <option value='1'" . $selected[0] . '>' . _AM_SOAPBOX_PERMISSIONS_GLOBAL . "</option>
                    <option value='2'" . $selected[1] . '>' . _AM_SOAPBOX_PERMISSIONS_APPROVE . "</option>
                    <option value='3'" . $selected[2] . '>' . _AM_SOAPBOX_PERMISSIONS_SUBMIT . "</option>
                    <option value='4'" . $selected[3] . '>' . _AM_SOAPBOX_PERMISSIONS_VIEW . '</option>
                </select>
            </td>
        </tr>
    </table>
</form>';

$module_id = $GLOBALS['xoopsModule']->getVar('mid');
switch ($permission) {
    case 1:
        $formTitle   = _AM_SOAPBOX_PERMISSIONS_GLOBAL;
        $permName    = 'soapbox_ac';
        $permDesc    = _AM_SOAPBOX_PERMISSIONS_GLOBAL_DESC;
        $globalPerms = [
            '4'  => _AM_SOAPBOX_PERMISSIONS_GLOBAL_4,
            '8'  => _AM_SOAPBOX_PERMISSIONS_GLOBAL_8,
            '16' => _AM_SOAPBOX_PERMISSIONS_GLOBAL_16
        ];
        break;
    case 2:
        $formTitle = _AM_SOAPBOX_PERMISSIONS_APPROVE;
        $permName  = 'soapbox_approve';
        $permDesc  = _AM_SOAPBOX_PERMISSIONS_APPROVE_DESC;
        break;
    case 3:
        $formTitle = _AM_SOAPBOX_PERMISSIONS_SUBMIT;
        $permName  = 'soapbox_submit';
        $permDesc  = _AM_SOAPBOX_PERMISSIONS_SUBMIT_DESC;
        break;
    case 4:
        $formTitle = _AM_SOAPBOX_PERMISSIONS_VIEW;
        $permName  = 'soapbox_view';
        $permDesc  = _AM_SOAPBOX_PERMISSIONS_VIEW_DESC;
        break;
}

//$Form2 = new \XoopsGroupPermForm ( "", $xoopsModule -> getVar ( 'mid'), "Column_permissions", _AM_SB_SELECT_COLS);

$permform = new \XoopsGroupPermForm($formTitle, $module_id, $permName, $permDesc, 'admin/permissions.php');
if (1 == $permission) {
    foreach ($globalPerms as $perm_id => $perm_name) {
        $permform->addItem($perm_id, $perm_name);
    }
    echo $permform->render();
    echo '<br><br>';
} else {
    $criteria = new \CriteriaCompo();
    $criteria->setSort('name');
    $criteria->setOrder('ASC');
    $cat_count = $sbColumnHandler->getCount($criteria);
    $cat_arr   = $sbColumnHandler->getObjects($criteria);
    unset($criteria);
    foreach (array_keys($cat_arr) as $i) {
        $permform->addItem($cat_arr[$i]->getVar('columnID'), $cat_arr[$i]->getVar('name'));
    }
    // Check if cat exist before rendering the form and redirect, if there aren't cat
    if ($cat_count > 0) {
        echo $permform->render();
        echo '<br><br>';
    } else {
        redirect_header('column.php', 3, _AM_SOAPBOX_PERMISSIONS_NOPERMSSET);
    }
}
unset($permform);
require_once __DIR__ . '/admin_footer.php';
