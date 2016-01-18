<?php
// $Id: menu.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: admin/menu.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$modulePath = dirname(dirname(dirname(__DIR__)));
include_once $modulePath . '/mainfile.php';

$moduleDirName   = basename(dirname(__DIR__));
$moduleHandler   = &xoops_gethandler('module');
$module          = $moduleHandler->getByDirname($moduleDirName);
$pathIcon32      = '../../' . $module->getInfo('icons32');
$pathModuleAdmin = $module->getInfo('dirmoduleadmin');
$pathLanguage    = $modulePath . $pathModuleAdmin;

$xoopsModuleAdminPath = XOOPS_ROOT_PATH . '/' . $module->getInfo('dirmoduleadmin');
if (!file_exists($fileinc = $xoopsModuleAdminPath . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $xoopsModuleAdminPath . '/language/english/main.php';
}
include_once $fileinc;

$adminmenu = array();

$i                      = 1;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_HOME;
$adminmenu[$i]['link']  = 'admin/index.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/home.png';

++$i;
$adminmenu[$i]['title'] = _MI_SOAPBOX_ADMENU1;
$adminmenu[$i]['link']  = 'admin/main.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/manage.png';

//++$i;
//$adminmenu[$i]['title'] =  _MI_SOAPBOX_ADMENU2;
//$adminmenu[$i]['link']  = 'admin/column.php';
//$adminmenu[$i]['icon']  = $pathIcon32 . '/categoryadd.png';

//++$i;
//$adminmenu[$i]['title'] = _MI_SOAPBOX_ADMENU3;
//$adminmenu[$i]['link']  = 'admin/article.php';
//$adminmenu[$i]['icon']  = $pathIcon32 . '/add.png';

++$i;
$adminmenu[$i]['title'] = _MI_SOAPBOX_SUBMITS;
$adminmenu[$i]['link']  = 'admin/submissions.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/button_ok.png';

++$i;
$adminmenu[$i]['title'] = _MI_SOAPBOX_ADMENU4;
$adminmenu[$i]['link']  = 'admin/permissions.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/permissions.png';

++$i;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
$adminmenu[$i]['link']  = 'admin/about.php';
$adminmenu[$i]['icon']  = $pathIcon32 . '/about.png';
