<?php

require_once dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';
include_once __DIR__ . '/admin_header.php';

xoops_cp_header();

$indexAdmin = new ModuleAdmin();

//get category count
//----------------------------
$_entrydata_handler =& xoops_getmodulehandler('entrydata', $xoopsModule->dirname());
$totcol             = $_entrydata_handler->getColumnCount();
//----------------------------
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('submit', 0));
$criteria->add(new Criteria('offline', 0));
$totpub = $_entrydata_handler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('submit', 0));
$criteria->add(new Criteria('offline', 1));
$totoff = $_entrydata_handler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('submit', 1));
$totsub = $_entrydata_handler->getArticleCount($criteria);
unset($criteria);
//----------------------------
$criteria = new CriteriaCompo();
$criteria->add(new Criteria('submit', 0));
$totall = $_entrydata_handler->getArticleCount($criteria);
unset($criteria);

$indexAdmin->addInfoBox(_AM_SOAPBOX_MODCONTENT);
if ($totcol > 0) {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . '<a href="main.php">' . _AM_SOAPBOX_TOTCOL . '</a><b>' . "</infolabel>", $totcol, 'Green');
} else {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . _AM_SOAPBOX_TOTCOL . "</infolabel>", $totcol, 'Green');
}
if ($totpub > 0) {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . '<a href="main.php">' . _AM_SOAPBOX_TOTART . '</a><b>' . "</infolabel>", $totpub, 'Green');
} else {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . _AM_SOAPBOX_TOTART . "</infolabel>", $totpub, 'Green');
}
if ($totoff > 0) {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . '<a href="main.php">' . _AM_SOAPBOX_TOTOFF . '</a><b>' . "</infolabel>", $totoff, 'Red');
} else {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . _AM_SOAPBOX_TOTOFF . "</infolabel>", $totoff, 'Green');
}
if ($totall > 0) {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . '<a href="main.php">' . _AM_SOAPBOX_TOTSUB . '</a><b>' . "</infolabel>", $totall, 'Green');
} else {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . _AM_SOAPBOX_TOTSUB . "</infolabel>", $totall, 'Green');
}

if ($totsub > 0) {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . '<a href="submissions.php">' . _AM_SOAPBOX_NEED_APPROVAL . '</a><b>' . "</infolabel>", $totsub, 'Red');
} else {
    $indexAdmin->addInfoBoxLine(_AM_SOAPBOX_MODCONTENT, "<infolabel>" . _AM_SOAPBOX_NEED_APPROVAL . "</infolabel>", $totsub, 'Green');
}

echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();

include_once __DIR__ . '/admin_footer.php';
