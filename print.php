<?php
/**
 *
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;

include __DIR__ . '/header.php';
global $moduleDirName;
$moduleDirName = $myts->htmlSpecialChars(basename(__DIR__));
if ($moduleDirName !== 'soapbox' && $moduleDirName !== '' && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES));
}

require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/include/cleantags.php';

$articleID = Request::getInt('$articleID', Request::getInt('$articleID', 0, 'POST'), 'GET');

if (0 === $articleID) {
    redirect_header('index.php');
}

/**
 * @param $articleID
 */
function PrintPage($articleID)
{
    global $moduleDirName;
    global $xoopsConfig, $xoopsModule, $xoopsModuleConfig;
    $myts      = MyTextSanitizer:: getInstance();
    $articleID = (int)$articleID;
    //get entry object
    $entrydataHandler = xoops_getModuleHandler('entryget', $moduleDirName);
    $_entryob         = $entrydataHandler->getArticleOnePermcheck($articleID, true, true);
    if (!is_object($_entryob)) {
        redirect_header(XOOPS_URL . '/modules/' . $moduleDirName . '/index.php', 1, 'Not Found');
    }
    //-------------------------------------
    $articles = $_entryob->toArray();
    //get category object
    $_categoryob = $_entryob->_sbcolumns;
    //get vars
    $category = $_categoryob->toArray();
    //-------------------------------------
    //get author
    $authorname = SoapboxUtility::getAuthorName($category['author']);
    //-------------------------------------

    $datetime = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $xoopsModuleConfig['dateformat']));
    //    $lead = $myts->htmlSpecialChars($lead);
    //    $bodytext = str_replace("[pagebreak]","<br style=\"page-break-after:always;\">",$bodytext);
    //    $bodytext = $myts->displayTarea($bodytext, $html, $smiley, $xcodes, '', $breaks);
    $bodytext = str_replace('[pagebreak]', '<br style="page-break-after:always;">', $_entryob->getVar('bodytext', 'none'));
    $bodytext = $GLOBALS['SoapboxCleantags']->cleanTags($myts->displayTarea($bodytext, $articles['html'], $articles['smiley'], $articles['xcodes'], '', $articles['breaks']));

    $sitename = $myts->htmlSpecialChars($xoopsConfig['sitename']);
    $slogan   = $myts->htmlSpecialChars($xoopsConfig['slogan']);

    echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";
    echo "<html>\n<head>\n";
    echo '<title>' . $sitename . "</title>\n";
    echo "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "' />\n";
    echo "<meta name='AUTHOR' content='" . $sitename . "' />\n";
    echo "<meta name='COPYRIGHT' content='Copyright (c) 2004 by " . $sitename . "' />\n";
    echo "<meta name='DESCRIPTION' content='" . $slogan . "' />\n";
    echo "<meta name='GENERATOR' content='" . XOOPS_VERSION . "' />\n\n\n";

    //hack start 2003-3-18 by toshimitsu
    //Column: --> _MD_SOAPBOX_COLUMNPRN , Author: --> _MD_SOAPBOX_AUTHORPRN
    echo "<body bgcolor='#ffffff' text='#000000'>
            <div style='width: 600px; border: 1px solid #000; padding: 20px;'>
                <div style='text-align: center; display: block; padding-bottom: 12px; margin: 0 0 6px 0; border-bottom: 2px solid #ccc;'><img src='"
         . XOOPS_URL
         . '/modules/'
         . $xoopsModule->dirname()
         . "/assets/images/sb_slogo.png' border='0' alt='' /><h2 style='margin: 0;'>"
         . $articles['headline']
         . '</h2></div>
                <div></div>
                <div>'
         . _MD_SOAPBOX_COLUMNPRN
         . '<b>'
         . $category['name']
         . "</b></div>
                <div style='padding-bottom: 6px; border-bottom: 1px solid #ccc;'>"
         . _MD_SOAPBOX_AUTHORPRN
         . ' <b>'
         . $authorname
         . '</b></div>
                <p>'
         . $articles['lead']
         . '</p>
                <p>'
         . $articles['bodytext']
         . "</p>
                <div style='padding-top: 12px; border-top: 2px solid #ccc;'><small><b>Published: </b>&nbsp;"
         . $datetime
         . '<br></div>
            </div>
            <br>
          </body>
          </html>';
}

PrintPage($articleID);
