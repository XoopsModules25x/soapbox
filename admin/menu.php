<?php
/**
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

include dirname(__DIR__) . '/preloads/autoloader.php';

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

//require_once  dirname(__DIR__) . '/include/common.php';
/** @var \XoopsModules\Soapbox\Helper $helper */
$helper = \XoopsModules\Soapbox\Helper::getInstance();
$helper->loadLanguage('common');

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
if (is_object($helper->getModule())) {
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');
}

$adminmenu = [];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_HOME,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ADMENU1,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png',
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
    'icon'  => $pathIcon32 . '/button_ok.png',
];

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ADMENU4,
    'link'  => 'admin/permissions.php',
    'icon'  => $pathIcon32 . '/permissions.png',
];

// Blocks Admin
$adminmenu[] = [
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS'),
    'link'  => 'admin/blocksadmin.php',
    'icon'  => $pathIcon32 . '/block.png',
];

//Feedback
$adminmenu[] = [
    'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_FEEDBACK'),
    'link'  => 'admin/feedback.php',
    'icon'  => $pathIcon32 . '/mail_foward.png',
];

if ($helper->getConfig('displayDeveloperTools')) {
    $adminmenu[] = [
        'title' => constant('CO_' . $moduleDirNameUpper . '_' . 'ADMENU_MIGRATE'),
        'link'  => 'admin/migrate.php',
        'icon'  => $pathIcon32 . '/database_go.png',
    ];
}

$adminmenu[] = [
    'title' => _MI_SOAPBOX_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
