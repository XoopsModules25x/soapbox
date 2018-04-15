<?php

use XoopsModules\Soapbox;

require_once  dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
require_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$adminObject = \Xmf\Module\Admin::getInstance();

/** @var Soapbox\Utility $utility */
$utility =  new Soapbox\Utility();


//get category count
//----------------------------
$entrydataHandler = $helper->getHandler('Entrydata');
$totcol           = $entrydataHandler->getColumnCount();
//----------------------------
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('submit', 0));
$criteria->add(new \Criteria('offline', 0));
$totpub = $entrydataHandler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('submit', 0));
$criteria->add(new \Criteria('offline', 1));
$totoff = $entrydataHandler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('submit', 1));
$totsub = $entrydataHandler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('submit', 0));
$totall = $entrydataHandler->getArticleCount($criteria);
unset($criteria);

$adminObject->addInfoBox(_AM_SOAPBOX_MODCONTENT);
if ($totcol > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SOAPBOX_TOTCOL . '</a>' . '</infolabel>', '<span class="green">' . $totcol . '</span>'), '', 'green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SOAPBOX_TOTCOL . '</infolabel>', '<span class="green">' . $totcol . '</span>'), '', 'Green');
}
if ($totpub > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SOAPBOX_TOTART . '</a>' . '</infolabel>', '<span class="green">' . $totpub . '</span>'), '', 'green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SOAPBOX_TOTART . '</infolabel>', '<span class="green">' . $totpub . '</span>'), '', 'green');
}
if ($totoff > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SOAPBOX_TOTOFF . '</a>' . '</infolabel>', '<span class="red">' . $totoff . '</span>'), '', 'red');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SOAPBOX_TOTOFF . '</infolabel>', '<span class="green">' . $totoff . '</span>'), '', 'green');
}
if ($totall > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="main.php">' . _AM_SOAPBOX_TOTSUB . '</a>' . '</infolabel>', '<span class="green">' . $totall . '</span>'), '', 'green');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SOAPBOX_TOTSUB . '</infolabel>', '<span class="green">' . $totall . '</span>'), '', 'green');
}

if ($totsub > 0) {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . '<a href="submissions.php">' . _AM_SOAPBOX_NEED_APPROVAL . '</a>' . '</infolabel>', '<span class="green">' . $totsub . '</span>'), '', 'red');
} else {
    $adminObject->addInfoBoxLine(sprintf('<infolabel>' . _AM_SOAPBOX_NEED_APPROVAL . '</infolabel>', '<span class="green">' . $totsub . '</span>'), '', 'green');
}
$adminObject->displayNavigation(basename(__FILE__));

//------------- Test Data ----------------------------

if ($helper->getConfig('displaySampleButton')) {
    xoops_loadLanguage('admin/modulesadmin', 'system');
    require_once  dirname(__DIR__) . '/testdata/index.php';

    $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'ADD_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=load', 'add');

    $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'SAVE_SAMPLEDATA'), '__DIR__ . /../../testdata/index.php?op=save', 'add');

    //    $adminObject->addItemButton(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA'), '__DIR__ . /../../testdata/index.php?op=exportschema', 'add');

    $adminObject->displayButton('left', '');
}

//------------- End Test Data ----------------------------

$adminObject->displayIndex();


echo $utility::getServerStats();

require_once __DIR__ . '/admin_footer.php';
