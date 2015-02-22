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

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/mainfile.php';

$dirname         = basename(dirname(dirname(__FILE__)));
$module_handler  = xoops_gethandler('module');
$module          = $module_handler->getByDirname($dirname);
$pathIcon32      = $module->getInfo('icons32');
$pathModuleAdmin = $module->getInfo('dirmoduleadmin');
$pathLanguage    = $path . $pathModuleAdmin;


if (!file_exists($fileinc = $pathLanguage . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $pathLanguage . '/language/english/main.php';
}

include_once $fileinc;

$adminmenu = array();

$i = 1;
$adminmenu[$i]["title"] = _AM_MODULEADMIN_HOME;
$adminmenu[$i]["link"]  = "admin/index.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/home.png';

$i++;
$adminmenu[$i]["title"] =  _MI_SB_ADMENU1;
$adminmenu[$i]["link"]  = "admin/main.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/manage.png';

//$i++;
//$adminmenu[$i]["title"] =  _MI_SB_ADMENU2;
//$adminmenu[$i]["link"]  = "admin/column.php";
//$adminmenu[$i]["icon"]  = $pathIcon32 . '/categoryadd.png';

//$i++;
//$adminmenu[$i]["title"] = _MI_SB_ADMENU3;
//$adminmenu[$i]["link"]  = "admin/article.php";
//$adminmenu[$i]["icon"]  = $pathIcon32 . '/add.png';

$i++;
$adminmenu[$i]["title"] = _MI_SB_SUBMITS;
$adminmenu[$i]["link"]  = "admin/submissions.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/button_ok.png';

$i++;
$adminmenu[$i]["title"] = _MI_SB_ADMENU4;
$adminmenu[$i]["link"]  = "admin/permissions.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/permissions.png';

$i++;
$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/about.png';