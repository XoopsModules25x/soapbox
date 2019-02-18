<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

use XoopsModules\Soapbox;

//require_once __DIR__ . '/../class/Helper.php';
//require_once __DIR__ . '/../include/common.php';
$helper = Soapbox\Helper::getInstance();

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');


$adminmenu = [];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ADMENU1,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png'
];

//$adminmenu[] = [
//'title' =>   _MI_SOAPBOX_ADMENU2,
//'link' =>  'admin/column.php',
//'icon' =>  $pathIcon32 . '/categoryadd.png',
//];

//$adminmenu[] = [
//'title' =>  _MI_SOAPBOX_ADMENU3,
//'link' =>  'admin/article.php',
//'icon' =>  $pathIcon32 . '/add.png',
//];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_SUBMITS,
    'link'  => 'admin/submissions.php',
    'icon'  => $pathIcon32 . '/button_ok.png'
];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ADMENU4,
    'link'  => 'admin/permissions.php',
    'icon'  => $pathIcon32 . '/permissions.png'
];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];
