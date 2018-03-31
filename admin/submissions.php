<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Soapbox;

// ---------- General Stuff ---------- //
require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();

$op = '';
if (isset($_GET['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
}
if (isset($_POST['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
}

//-------------------------------------
$entrydataHandler = $helper->getHandler('Entrydata');

// -- Edit function -- //
/**
 * @param string $articleID
 */
function editarticle($articleID = '')
{
    global $indexAdmin;
    global $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsLogger, $xoopsOption, $xoopsUserIsAdmin;
    /** @var Soapbox\Helper $helper */
    $helper = Soapbox\Helper::getInstance();

    $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
    $myts    = \MyTextSanitizer::getInstance();

    if (file_exists(XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php')) {
        require_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/calendar.php';
    } else {
        require_once XOOPS_ROOT_PATH . '/language/english/calendar.php';
    }
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    //-------------------------------------
    $entrydataHandler = $helper->getHandler('Entrydata');

    if (!$articleID) {
        redirect_header('index.php', 1, _AM_SOAPBOX_NOARTS);
    }
    //get entry object
    $_entryob = $entrydataHandler->getArticle($articleID);
    if (!is_object($_entryob)) {
        redirect_header('index.php', 1, _AM_SOAPBOX_NOARTTOEDIT);
    }
    //get vars mode E
    $entry_vars = $_entryob->getVars();
    foreach ($entry_vars as $k => $v) {
        $e_articles[$k] = $_entryob->getVar($k, 'E');
    }
    //xoops_cp_header();
    //$adminObject->displayNavigation(basename(__FILE__));

    // Module menu
    //adminMenu(3, _AM_SOAPBOX_SUBMITS." > '". $_entryob->getVar('headline') ."'");

    //    echo "<h3 style='color: #2F5376; '>" . _AM_SOAPBOX_SUBMITSMNGMT . "</h3>";
    $sform = new \XoopsThemeForm(_AM_SOAPBOX_AUTHART . ': ' . $_entryob->getVar('headline'), 'op', xoops_getenv('PHP_SELF'), 'post', true);

    $sform->setExtra('enctype="multipart/form-data"');

    //get category object
    $_categoryob = $entrydataHandler->getColumn($e_articles['columnID']);
    if (is_object($_categoryob)) {
        $sform->addElement(new \XoopsFormLabel(_AM_SOAPBOX_COLNAME, $_categoryob->getVar('name')));
        $sform->addElement(new \XoopsFormHidden('columnID', $e_articles['columnID']));
    } else {
        $_can_editcategoryobArray = $entrydataHandler->getColumns(null, true);
        //----------------------------
        $collist = [];
        foreach ($_can_editcategoryobArray as $key => $_can_edit_categoryob) {
            $collist[$key] = $_can_edit_categoryob->getVar('name');
        }
        $col_select = new \XoopsFormSelect('', 'columnID', (int)$e_articles['columnID']);
        $col_select->addOptionArray($collist);
        $col_select_tray = new \XoopsFormElementTray(_AM_SOAPBOX_COLNAME, '<br>');
        $col_select_tray->addElement($col_select);
        $sform->addElement($col_select_tray);
    }

    if (isset($headline)) {
        $headline = $myts->htmlspecialchars(stripslashes($headline));
    }

    // HEADLINE, LEAD, BODYTEXT
    // This part is common to edit/add
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_ARTHEADLINE, 'headline', 50, 50, $e_articles['headline']), true);

    // LEAD
    //    $sform -> addElement( new \XoopsFormTextArea( _AM_SOAPBOX_ARTLEAD, 'lead', $lead, 5, 60 ) );
    //    $editor_lead=soapbox_getWysiwygForm($helper->getConfig('soapboxEditorUser') , _AM_SOAPBOX_ARTLEAD , 'lead' , $e_articles['lead'] , '100%', '200px');
    //    $sform->addElement($editor_lead,true);

    $editor_lead = new \XoopsFormElementTray(_AM_SOAPBOX_ARTLEAD, '<br>');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'lead';
        $options['value']  = $e_articles['lead'];
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '200px';
        $formmnote         = new \XoopsFormEditor('', $helper->getConfig('soapboxEditorUser'), $options, $nohtml = false, $onfailure = 'textarea');
        $editor_lead->addElement($formmnote);
    } else {
        $formmnote = new \XoopsFormDhtmlTextArea('', 'formmnote', $item->getVar('formmnote', 'e'), '100%', '100%');
        $editor_lead->addElement($formmnote);
    }
    $sform->addElement($editor_lead, false);

    // TEASER
    $sform->addElement(new \XoopsFormTextArea(_AM_SOAPBOX_ARTTEASER, 'teaser', $e_articles['teaser'], 10, 120));
    //    $editor_teaser=soapbox_getWysiwygForm($helper->getConfig('soapboxEditorUser') , _AM_SOAPBOX_ARTTEASER ,'teaser', $teaser , '100%', '120px');
    //    $sform->addElement($editor_teaser,true);
    //
    $autoteaser_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_AUTOTEASER, 'autoteaser', 0, ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($autoteaser_radio);
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100));

    // BODY
    //HACK by domifara for Wysiwyg
    //    if  (null !== ($helper->getConfig('soapboxEditorUser')) ) {
    //        $editor=soapbox_getWysiwygForm($helper->getConfig('soapboxEditorUser') , _AM_SOAPBOX_ARTBODY, 'bodytext', $e_articles['bodytext'], '100%', '400px');
    //        $sform->addElement($editor,true);
    //    } else {
    //        $sform -> addElement( new \XoopsFormDhtmlTextArea( _AM_SOAPBOX_ARTBODY, 'bodytext', $e_articles['bodytext'], 20, 120 ) );
    //    }

    $optionsTrayNote = new \XoopsFormElementTray(_AM_SOAPBOX_ARTBODY, '<br>');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'bodytext';
        $options['value']  = $e_articles['bodytext'];
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '400px';
        $bodynote          = new \XoopsFormEditor('', $helper->getConfig('soapboxEditorUser'), $options, $nohtml = false, $onfailure = 'textarea');
        $optionsTrayNote->addElement($bodynote);
    } else {
        $bodynote = new \XoopsFormDhtmlTextArea('', 'formmnote', $item->getVar('formmnote', 'e'), '100%', '100%');
        $optionsTrayNote->addElement($bodynote);
    }
    $sform->addElement($optionsTrayNote, false);

    // IMAGE
    // The article CAN have its own image :)
    // First, if the article's image doesn't exist, set its value to the blank file

    if (empty($e_articles['artimage'])
        || !file_exists(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $e_articles['artimage'])) {
        $artimage = 'blank.png';
    }

    // Code to create the image selector
    $graph_array     = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));
    $artimage_select = new \XoopsFormSelect('', 'artimage', $e_articles['artimage']);
    $artimage_select->addOptionArray($graph_array);
    $artimage_select->setExtra("onchange='showImgSelected(\"image5\", \"artimage\", \"" . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '", "", "' . XOOPS_URL . "\")'");
    $artimage_tray = new \XoopsFormElementTray(_AM_SOAPBOX_SELECT_IMG, '&nbsp;');
    $artimage_tray->addElement($artimage_select);
    $artimage_tray->addElement(new \XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $e_articles['artimage'] . "' name='image5' id='image5' alt=''>"));
    $sform->addElement($artimage_tray);

    // Code to call the file browser to select an image to upload
    $sform->addElement(new \XoopsFormFile(_AM_SOAPBOX_UPLOADIMAGE, 'cimage', (int)$helper->getConfig('maxfilesize')), false);

    // WEIGHT
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_WEIGHT, 'weight', 4, 4, $e_articles['weight']));
    //----------
    // datesub
    //----------
    $datesub_caption = $myts->htmlSpecialChars(formatTimestamp($e_articles['datesub'], $helper->getConfig('dateformat')) . '=>');
    $datesub_tray    = new \XoopsFormDateTime(_AM_SOAPBOX_POSTED . '<br>' . $datesub_caption, 'datesub', 15, time());
    // you don't want to change datesub
    $datesubnochage_checkbox = new \XoopsFormCheckBox(_AM_SOAPBOX_DATESUBNOCHANGE, 'datesubnochage', 0);
    $datesubnochage_checkbox->addOption(1, _AM_SOAPBOX_YES);
    $datesub_tray->addElement($datesubnochage_checkbox);
    $sform->addElement($datesub_tray);
    //-----------

    // COMMENTS
    if (isset($GLOBALS['xoopsModuleConfig']['globaldisplaycomments'])
        && 1 === $GLOBALS['xoopsModuleConfig']['globaldisplaycomments']) {
        // COMMENTS
        // Code to allow comments
        $addcommentable_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_ALLOWCOMMENTS, 'commentable', $e_articles['commentable'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
        $sform->addElement($addcommentable_radio);
    }

    // OFFLINE
    // Code to take article offline, for maintenance purposes
    $offline_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_SWITCHOFFLINE, 'offline', $e_articles['offline'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($offline_radio);

    // ARTICLE IN BLOCK
    // Code to put article in block
    $block_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_BLOCK, 'block', $e_articles['block'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($block_radio);

    // notification public
    $notifypub_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_NOTIFY, 'notifypub', $e_articles['notifypub'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($notifypub_radio);

    // VARIOUS OPTIONS
    //----------
    $options_tray = new \XoopsFormElementTray(_AM_SOAPBOX_OPTIONS, '<br>');

    $html_checkbox = new \XoopsFormCheckBox('', 'html', $e_articles['html']);
    $html_checkbox->addOption(1, _AM_SOAPBOX_DOHTML);
    $options_tray->addElement($html_checkbox);

    $smiley_checkbox = new \XoopsFormCheckBox('', 'smiley', $e_articles['smiley']);
    $smiley_checkbox->addOption(1, _AM_SOAPBOX_DOSMILEY);
    $options_tray->addElement($smiley_checkbox);

    $xcodes_checkbox = new \XoopsFormCheckBox('', 'xcodes', $e_articles['xcodes']);
    $xcodes_checkbox->addOption(1, _AM_SOAPBOX_DOXCODE);
    $options_tray->addElement($xcodes_checkbox);

    $breaks_checkbox = new \XoopsFormCheckBox('', 'breaks', $e_articles['breaks']);
    $breaks_checkbox->addOption(1, _AM_SOAPBOX_BREAKS);
    $options_tray->addElement($breaks_checkbox);

    $sform->addElement($options_tray);
    //----------

    $sform->addElement(new \XoopsFormHidden('articleID', $e_articles['articleID']));

    $button_tray = new \XoopsFormElementTray('', '');
    $hidden      = new \XoopsFormHidden('op', 'authart');
    $button_tray->addElement($hidden);

    $butt_save = new \XoopsFormButton('', '', _AM_SOAPBOX_AUTHORIZE, 'submit');
    $butt_save->setExtra('onclick="this.form.elements.op.value=\'authart\'"');
    $button_tray->addElement($butt_save);

    $butt_cancel = new \XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
    $butt_cancel->setExtra('onclick="history.go(-1)"');
    $button_tray->addElement($butt_cancel);

    $sform->addElement($button_tray);
    //-----------
    //    $xoopsGTicket->addTicketXoopsFormElement($sform, __LINE__);
    //-----------
    $sform->display();
    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        $articleID = Request::getInt('articleID', Request::getInt('articleID', 0, 'GET'), 'POST');// (isset($_POST['articleID'])) ? (int)($_POST['articleID']) : (int)($_GET['articleID']);
        editarticle($articleID);
        Soapbox\Utility::showSubmissions();
        break;

    case 'authart':
        //-------------------------
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
        }
        //-------------------------
        //articleID check
        if (Request::hasVar('articleID', 'POST')) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $articleID = Request::getInt('articleID', 0, 'POST');
        }
        //articleID check
        if (Request::hasVar('columnID', 'POST')) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $columnID = Request::getInt('columnID', 0, 'POST');
        }
        
        //get category object
        $_categoryob = $entrydataHandler->getColumn($columnID);
        if (!is_object($_categoryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_NEEDONECOLUMN);
        }

        $_entryob = $entrydataHandler->getArticle($articleID);
        //new data or edit
        if (!is_object($_entryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTAUTHORIZED);
        }

        if (Request::hasVar('articleID', 'POST')) {
            $_entryob->setVar('articleID', $articleID);
        }

        if (Request::hasVar('columnID', 'POST')) {
            $_entryob->setVar('columnID', $columnID);
        }

        if (Request::hasVar('weight', 'POST')) {
            $_entryob->setVar('weight', Request::getInt('weight', 0, 'POST'));
        }

        if (Request::hasVar('commentable', 'POST')) {
            $_entryob->setVar('commentable', Request::getInt('commentable', 0, 'POST'));
        }

        if (Request::hasVar('block', 'POST')) {
            $_entryob->setVar('block', Request::getInt('block', 0, 'POST'));
        }

        if (Request::hasVar('offline', 'POST')) {
            $_entryob->setVar('offline', Request::getInt('offline', 0, 'POST'));
        }

        if (Request::hasVar('notifypub', 'POST')) {
            $_entryob->setVar('notifypub', Request::getInt('notifypub', 0, 'POST'));
        }

        //datesub

        $datesubnochage = Request::getInt('datesubnochage', 0, 'POST');

        //        $datesub_date_sl = isset($_POST['datesub']) ? (int)strtotime($_POST['datesub']['date']) : 0;
        //        $datesub_time_sl = isset($_POST['datesub']) ? (int)$_POST['datesub']['time'] : 0;
        //        $datesub         = isset($_POST['datesub']) ? $datesub_date_sl + $datesub_time_sl : 0;
        $temp    = Request::getArray('datesub', null, 'POST'); // get a clean array
        $datesub = null !== $temp ? strtotime($temp['date']) + $temp['time'] : 0; // put the pieces back together

        if (!$datesub || $_entryob->_isNew) {
            $_entryob->setVar('datesub', time());
        } else {
            if (!$datesubnochage) {
                $_entryob->setVar('datesub', $datesub);
            }
        }

        if (Request::hasVar('html', 'POST')) {
            $_entryob->setVar('html', Request::getInt('html', 0, 'POST'));
        }

        if (Request::hasVar('smiley', 'POST')) {
            $_entryob->setVar('smiley',  Request::getInt('smiley', 0, 'POST'));
        }

        if (Request::hasVar('xcodes', 'POST')) {
            $_entryob->setVar('xcodes',  Request::getInt('xcodes', 0, 'POST'));
        }
        if (Request::hasVar('breaks', 'POST')) {
            $_entryob->setVar('breaks',  Request::getInt('breaks', 0, 'POST'));
        }
        if (Request::hasVar('artimage', 'POST')) {
            $_entryob->setVar('artimage',  Request::getInt('artimage', 0, 'POST'));
        }

        if (Request::hasVar('headline', 'POST')) {
            $_entryob->setVar('headline',  Request::getString('headline', '', 'POST'));
        }
        if (Request::hasVar('lead', 'POST')) {
            $_entryob->setVar('lead',  Request::getText('lead', '', 'POST'));
        }
        if (Request::hasVar('bodytext', 'POST')) {
            $_entryob->setVar('bodytext',  Request::getText('bodytext', '', 'POST'));
        }
        if (Request::hasVar('votes', 'POST')) {
            $_entryob->setVar('votes',  Request::getInt('votes', 0, 'POST'));
        }
        
        if (Request::hasVar('rating', 'POST')) {
            $_entryob->setVar('rating',  Request::getInt('rating', 0, 'POST'));
        }
        if (Request::hasVar('teaser', 'POST')) {
            $_entryob->setVar('teaser',  Request::getInt('teaser', 0, 'POST'));
        }


        $autoteaser = Request::getInt('autoteaser', 0, 'POST');
        $charlength = Request::getInt('teaseramount', 0, 'POST');
        if ($autoteaser && $charlength) {
            $_entryob->setVar('teaser', xoops_substr($_entryob->getVar('bodytext', 'none'), 0, $charlength));
        }

        $_entryob->setVar('submit', 0);
        // Save to database
        if (!$entrydataHandler->insertArticle($_entryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTUPDATED);
        } else {
            // Notify of to admin only for approve
            $entrydataHandler->newArticleTriggerEvent($_entryob, 'approve');
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTAUTHORIZED);
        }
        break;

    case 'del':

        $confirm = Request::getInt('confirm', 0, 'POST');

        // confirmed, so delete
        if (1 === $confirm) {
            //-------------------------
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
            }
            //-------------------------
            //articleID check
            if (!Request::hasVar('articleID', 'POST')) {
                redirect_header('index.php', 1, _NOPERM);
            } else {
                $articleID = Request::getInt('articleID', 0, 'POST');
            } 
            
            $_entryob = $entrydataHandler->getArticle($articleID);
            if (!is_object($_entryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            //-------------
            if (!$entrydataHandler->deleteArticle($_entryob)) {
                trigger_error('ERROR:not deleted from database');
                exit();
            } else {
                $headline = $myts->htmlSpecialChars($_entryob->getVar('headline'));
                redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_ARTISDELETED, $headline));
            }
        } else {
            $articleID = Request::getInt('articleID', Request::getInt('articleID', 0, 'GET'), 'POST');
            $_entryob  = $entrydataHandler->getArticle($articleID);
            if (!is_object($_entryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            $headline = $myts->htmlSpecialChars($_entryob->getVar('headline'));
            xoops_cp_header();
            $adminObject->displayNavigation(basename(__FILE__));
            xoops_confirm([
                              'op'        => 'del',
                              'articleID' => $articleID,
                              'confirm'   => 1,
                              'headline'  => $headline
                          ], 'article.php', _AM_SOAPBOX_DELETETHISARTICLE . '<br><br>' . $headline, _AM_SOAPBOX_DELETE);
            require_once __DIR__ . '/admin_footer.php';
        }
        exit();
        break;

    case 'default':
    default:

        xoops_cp_header();
        //adminMenu(3, _AM_SOAPBOX_SUBMITS);
        $adminObject->displayNavigation(basename(__FILE__));
        echo '<br>';
        Soapbox\Utility::showSubmissions();
        require_once __DIR__ . '/admin_footer.php';
        exit();
        break;
}
require_once __DIR__ . '/admin_footer.php';
