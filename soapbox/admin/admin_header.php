<?php
/**
 * ****************************************************************************
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright         XOOPS Project
 * @license           http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @package
 * @author            XOOPS Development Team
 *
 * Version : $Id:
 * ****************************************************************************
 */

$moduleDirName = basename(dirname(__DIR__));
include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
include_once $GLOBALS['xoops']->path('www/include/cp_functions.php');
include_once $GLOBALS['xoops']->path('www/include/cp_header.php');
include_once $GLOBALS['xoops']->path('www/class/xoopsformloader.php');
require dirname(__DIR__) . '/include/gtickets.php';

include_once $GLOBALS['xoops']->path('www/kernel/module.php');
include_once $GLOBALS['xoops']->path('www/class/xoopstree.php');
include_once $GLOBALS['xoops']->path('www/class/xoopslists.php');

global $xoopsModule;

require_once dirname(__DIR__) . '/include/functions.php';

// Load language files
// Load language files
xoops_loadLanguage('admin', $moduleDirName);
xoops_loadLanguage('modinfo', $moduleDirName);
xoops_loadLanguage('main', $moduleDirName);

xoops_load('XoopsRequest');

$pathIcon16           = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons16'));
$pathIcon32           = $GLOBALS['xoops']->url('www/' . $GLOBALS['xoopsModule']->getInfo('icons32'));
$xoopsModuleAdminPath = $GLOBALS['xoops']->path('www/' . $GLOBALS['xoopsModule']->getInfo('dirmoduleadmin'));

require_once "{$xoopsModuleAdminPath}/moduleadmin.php";

$myts =& MyTextSanitizer::getInstance();
