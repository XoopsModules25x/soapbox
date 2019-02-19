<?php
/**
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

use XoopsModules\Soapbox;

// -- General Stuff -- //
require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();

/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

$op = '';
if (\Xmf\Request::hasVar('op', 'GET')) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
}
if (\Xmf\Request::hasVar('op', 'POST')) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
}

/** @var \XoopsModules\Soapbox\EntrydataHandler $entrydataHandler */
$entrydataHandler = new \XoopsModules\Soapbox\EntrydataHandler();
$totalcats        = $entrydataHandler->getColumnCount();
if (0 === $totalcats) {
    redirect_header('index.php', 1, _AM_SOAPBOX_NEEDONECOLUMN);
}

// -- Edit function -- //
/**
 * @param int $articleID
 */
function editarticle($articleID = 0)
{
    global $xoopsGTicket, $indexAdmin;
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

    $articleID = (int)$articleID;
    /** @var \XoopsModules\Soapbox\EntrydataHandler $entrydataHandler */
    $entrydataHandler = new \XoopsModules\Soapbox\EntrydataHandler();
    if (0 !== $articleID) {
        //articleID check
        $_entryob = $entrydataHandler->getArticleOnePermcheck($articleID, false, false);
        if (!is_object($_entryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_NOARTS);
        }

        //adminMenu(2, _AM_SOAPBOX_ARTS._AM_SOAPBOX_EDITING. $_entryob->getVar('headline') ."'");
        //echo "<h3 style='color: #2F5376; '>" . _AM_SOAPBOX_ADMINARTMNGMT . "</h3>";
        $sform = new \XoopsThemeForm(_AM_SOAPBOX_MODART . ': ' . $_entryob->getVar('headline'), 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
    } else {
        //create new entry object
        $_entryob = $entrydataHandler->createArticle(true);
        $_entryob->cleanVars();

        /**
         *initial first variables before we start
         */
        $columnID = 1;
        if (null !== $helper->getConfig('editorUser') && 'dhtml' !== $helper->getConfig('editorUser')) {
            $html   = 1;
            $breaks = 0;
        }
        //adminMenu(2, _AM_SOAPBOX_ARTS._AM_SOAPBOX_CREATINGART);
        //echo "<h3 style='color: #2F5376; '>" . _AM_SOAPBOX_ADMINARTMNGMT . "</h3>";
        $sform = new \XoopsThemeForm(_AM_SOAPBOX_NEWART, 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
    }

    //get vars mode E
    $entry_vars = $_entryob->getVars();
    foreach ($entry_vars as $k => $v) {
        $e_articles[$k] = $_entryob->getVar($k, 'E');
    }

    $sform->setExtra('enctype="multipart/form-data"');

    // COLUMN
    /*
    * Get information for pulldown menu using XoopsTree.
    * First var is the database table
    * Second var is the unique field ID for the categories
    * Last one is not set as we do not have sub menus in WF-FAQ
    */
    $canEditCategoryobArray = $entrydataHandler->getColumns(null, true);
    $collist                = [];
    foreach ($canEditCategoryobArray as $key => $_can_edit_categoryob) {
        $collist[$key] = $_can_edit_categoryob->getVar('name');
    }
    $col_select = new \XoopsFormSelect('', 'columnID', (int)$e_articles['columnID']);
    $col_select->addOptionArray($collist);
    $col_select_tray = new \XoopsFormElementTray(_AM_SOAPBOX_COLNAME, '<br>');
    $col_select_tray->addElement($col_select);
    $sform->addElement($col_select_tray);

    // HEADLINE, LEAD, BODYTEXT
    // This part is common to edit/add
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_ARTHEADLINE, 'headline', 50, 50, $e_articles['headline']), true);

    // LEAD
    //    $sform -> addElement( new \XoopsFormTextArea( _AM_SOAPBOX_ARTLEAD, 'lead', $lead, 5, 60 ) );
    //    $editor_lead=soapbox_getWysiwygForm($helper->getConfig('editorUser') , _AM_SOAPBOX_ARTLEAD , 'lead' , $e_articles['lead'] , '100%', '200px');
    //    $sform->addElement($editor_lead,TRUE);

    $editor_lead = new \XoopsFormElementTray(_AM_SOAPBOX_ARTLEAD, '<br>');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'lead';
        $options['value']  = $e_articles['lead'];
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '200px';
        $formmnote         = new \XoopsFormEditor('', $helper->getConfig('editorUser'), $options, $nohtml = false, $onfailure = 'textarea');
        $editor_lead->addElement($formmnote);
    } else {
        $formmnote = new \XoopsFormDhtmlTextArea('', 'formmnote', $item->getVar('formmnote', 'e'), '100%', '100%');
        $editor_lead->addElement($formmnote);
    }
    $sform->addElement($editor_lead, false);

    // TEASER
    $sform->addElement(new \XoopsFormTextArea(_AM_SOAPBOX_ARTTEASER, 'teaser', $e_articles['teaser'], 10, 120));
    //    $editor_teaser=soapbox_getWysiwygForm($helper->getConfig('editorUser') , _AM_SOAPBOX_ARTTEASER ,'teaser', $teaser , '100%', '120px');
    //    $sform->addElement($editor_teaser,true);
    //
    // $autoteaser_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_AUTOTEASER, 'autoteaser', 0, ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    // $sform->addElement($autoteaser_radio);
    // $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100));

    // BODY
    //HACK by domifara for Wysiwyg
    //    if  (null !== ($helper->getConfig('editorUser')) ) {
    //        $editor=soapbox_getWysiwygForm($helper->getConfig('editorUser') , _AM_SOAPBOX_ARTBODY, 'bodytext', $e_articles['bodytext'], '100%', '400px');
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
        $bodynote          = new \XoopsFormEditor('', $helper->getConfig('editorUser'), $options, $nohtml = false, $onfailure = 'textarea');
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
    $graph_array     = \XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));
    $artimage_select = new \XoopsFormSelect('', 'artimage', $e_articles['artimage']);
    $artimage_select->addOptionArray($graph_array);
    $artimage_select->setExtra("onchange='showImgSelected(\"image5\", \"artimage\", \"" . $helper->getConfig('sbuploaddir') . '", "", "' . XOOPS_URL . "\")'");
    $artimage_tray = new \XoopsFormElementTray(_AM_SOAPBOX_SELECT_IMG, '&nbsp;');
    $artimage_tray->addElement($artimage_select);
    $artimage_tray->addElement(new \XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $e_articles['artimage'] . "' name='image5' id='image5' alt='' />"));
    $sform->addElement($artimage_tray);

    // Code to call the file browser to select an image to upload
    $sform->addElement(new \XoopsFormFile(_AM_SOAPBOX_UPLOADIMAGE, 'cimage', (int)$helper->getConfig('maxfilesize')), false);

    // WEIGHT
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_WEIGHT, 'weight', 4, 4, $e_articles['weight']));
    //----------
    // datesub
    //----------
    //$datesub_caption = $myts->htmlSpecialChars( formatTimestamp( $e_articles['datesub'] , $helper->getConfig('dateformat')) . "=>");
    //$datesub_tray = new \XoopsFormDateTime( _AM_SOAPBOX_POSTED.'<br>' . $datesub_caption ,'datesub' , 15, time()) ;
    $datesub_tray = new \XoopsFormDateTime(_AM_SOAPBOX_POSTED . '<br>', 'datesub', 15, $e_articles['datesub']);

    // you don't want to change datesub
    $datesubnochage_checkbox = new \XoopsFormCheckBox(_AM_SOAPBOX_DATESUBNOCHANGE, 'datesubnochage', 1);
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

    $buttonTray = new \XoopsFormElementTray('', '');
    $hidden     = new \XoopsFormHidden('op', 'addart');
    $buttonTray->addElement($hidden);

    if (!$e_articles['articleID']) { // there's no articleID? Then it's a new article
        $butt_create = new \XoopsFormButton('', '', _AM_SOAPBOX_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');
        $buttonTray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SOAPBOX_CLEAR, 'reset');
        $buttonTray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    } else { // else, we're editing an existing article
        $butt_create = new \XoopsFormButton('', '', _AM_SOAPBOX_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');
        $buttonTray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    }

    $sform->addElement($buttonTray);
    //-----------
    $xoopsGTicket->addTicketXoopsFormElement($sform, __LINE__);
    //-----------
    $sform->display();
    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        $articleID = \Xmf\Request::getInt('articleID', \Xmf\Request::getInt('articleID', 0, 'GET'), 'POST');
        editarticle($articleID);
        break;
    case 'addart':
        //-------------------------
        if (!$xoopsGTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
        }
        //-------------------------

        //articleID check
        if (!isset($_POST['articleID'])) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $articleID = \Xmf\Request::getInt('articleID', 0, 'POST');
        }
        //articleID check
        if (!isset($_POST['columnID'])) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $columnID = \Xmf\Request::getInt('columnID', 0, 'POST');
        }

        //get category object
        $_categoryob = $entrydataHandler->getColumn($columnID);
        if (!is_object($_categoryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_NEEDONECOLUMN);
        }

        $_entryob = $entrydataHandler->getArticle($articleID);
        //new data or edit
        if (!is_object($_entryob)) {
            $_entryob = $entrydataHandler->createArticle(true);
            $_entryob->cleanVars();
        }
        //set

        // new data post uid
        if (is_object($xoopsUser)) {
            $_entryob->setVar('uid', $xoopsUser->getVar('uid'));
        } else {
            //trigger_error ("Why:uid no mach") ;
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        }

        if (\Xmf\Request::hasVar('articleID', 'POST')) {
            $_entryob->setVar('articleID', $articleID);
        }
        if (\Xmf\Request::hasVar('columnID', 'POST')) {
            $_entryob->setVar('columnID', $columnID);
        }

        if (\Xmf\Request::hasVar('weight', 'POST')) {
            $_entryob->setVar('weight', \Xmf\Request::getInt('weight', 0, 'POST'));
        }

        if (\Xmf\Request::hasVar('commentable', 'POST')) {
            $_entryob->setVar('commentable', \Xmf\Request::getInt('commentable', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('block', 'POST')) {
            $_entryob->setVar('block', \Xmf\Request::getInt('block', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('offline', 'POST')) {
            $_entryob->setVar('offline', \Xmf\Request::getInt('offline', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('notifypub', 'POST')) {
            $_entryob->setVar('notifypub', \Xmf\Request::getInt('notifypub', 0, 'POST'));
        }

        if (\Xmf\Request::hasVar('breaks', 'POST')) {
            $_entryob->setVar('breaks', \Xmf\Request::getInt('breaks', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('html', 'POST')) {
            $_entryob->setVar('html', \Xmf\Request::getInt('html', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('smiley', 'POST')) {
            $_entryob->setVar('smiley', \Xmf\Request::getInt('smiley', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('xcodes', 'POST')) {
            $_entryob->setVar('xcodes', \Xmf\Request::getInt('xcodes', 0, 'POST'));
        }

        if (\Xmf\Request::hasVar('headline', 'POST')) {
            $_entryob->setVar('headline', $_POST['headline']);
        }
        if (\Xmf\Request::hasVar('lead', 'POST')) {
            $_entryob->setVar('lead', $_POST['lead']);
        }
        if (\Xmf\Request::hasVar('bodytext', 'POST')) {
            $_entryob->setVar('bodytext', $_POST['bodytext']);
        }
        if (\Xmf\Request::hasVar('votes', 'POST')) {
            $_entryob->setVar('votes', \Xmf\Request::getInt('votes', 0, 'POST'));
        }
        if (\Xmf\Request::hasVar('rating', 'POST')) {
            $_entryob->setVar('rating', \Xmf\Request::getInt('rating', 0, 'POST'));
        }

        if (\Xmf\Request::hasVar('teaser', 'POST')) {
            $_entryob->setVar('teaser', $_POST['teaser']);
        }

        $autoteaser = \Xmf\Request::getInt('autoteaser', 0, 'POST');
        $charlength = \Xmf\Request::getInt('teaseramount', 0, 'POST');
        if ($autoteaser && $charlength) {
            $_entryob->setVar('teaser', xoops_substr($_entryob->getVar('bodytext', 'none'), 0, $charlength));
        }
        //datesub
        $datesubnochage  = \Xmf\Request::getInt('datesubnochage', 0, 'POST');
        $datesub_date_sl = isset($_POST['datesub']) ? (int)strtotime($_POST['datesub']['date']) : 0;
        $datesub_time_sl = \Xmf\Request::getInt('datesub', 0, 'POST');
        $datesub         = isset($_POST['datesub']) ? $datesub_date_sl + $datesub_time_sl : 0;
        //if (!$datesub || $_entryob->_isNew) {
        if (!$datesub) {
            $_entryob->setVar('datesub', time());
        } else {
            if (!$datesubnochage) {
                $_entryob->setVar('datesub', $datesub);
            }
        }

        $_entryob->setVar('submit', 0);

        // ARTICLE IMAGE
        // Define variables
        $error  = 0;
        $word   = null;
        $uid    = $xoopsUser->uid();
        $submit = 1;
        $date   = time();
        //-----------------
        //artimage
        if (\Xmf\Request::hasVar('artimage', 'POST')) {
            $_entryob->setVar('artimage', $_POST['artimage']);
        }
        if (isset($_FILES['cimage']['name'])) {
            $artimage_name = trim(strip_tags($myts->stripSlashesGPC($_FILES['cimage']['name'])));
            if ('' !== $artimage_name) {
                require_once XOOPS_ROOT_PATH . '/class/uploader.php';
                if (file_exists(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/' . $artimage_name)) {
                    redirect_header('index.php', 1, _AM_SOAPBOX_FILEEXISTS);
                }
                $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'];

                Soapbox\Utility::uploadFile($allowed_mimetypes, $artimage_name, 'index.php', 0, $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));

                $_entryob->setVar('artimage', $artimage_name);
            }
        }
        if ('' === $_entryob->getVar('artimage')) {
            $_entryob->setVar('artimage', 'blank.png');
        }
        //-----------------

        // Save to database
        if ($_entryob->_isNew) {
            if (!$entrydataHandler->insertArticle($_entryob)) {
                xoops_cp_header();
                $adminObject->displayNavigation(basename(__FILE__));
                //                print_r($_entryob->getErrors());
                xoops_cp_footer();
                //                exit();
                redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
            } else {
                // Notify of to admin only for approve
                $entrydataHandler->newArticleTriggerEvent($_entryob, 'new_article');
                redirect_header('index.php', 1, _AM_SOAPBOX_ARTCREATEDOK);
            }
        } else {
            if (!$entrydataHandler->insertArticle($_entryob)) {
                redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTUPDATED);
            } else {
                $entrydataHandler->newArticleTriggerEvent($_entryob, 'new_article');
                redirect_header('index.php', 1, _AM_SOAPBOX_ARTMODIFIED);
            }
        }
        exit();
        break;
    case 'del':

        $confirm = \Xmf\Request::getInt('confirm', 0, 'POST');

        // confirmed, so delete
        if (1 === $confirm) {
            //-------------------------
            if (!$xoopsGTicket->check()) {
                redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
            }
            //-------------------------
            //articleID check
            if (!isset($_POST['articleID'])) {
                redirect_header('index.php', 1, _NOPERM);
            } else {
                $articleID = \Xmf\Request::getInt('articleID', 0, 'POST');
            }

            $_entryob = $entrydataHandler->getArticle($articleID);
            if (!is_object($_entryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }

            if (!$entrydataHandler->deleteArticle($_entryob)) {
                trigger_error('ERROR:not deleted from database');
                exit();
            }
            $headline = $myts->htmlSpecialChars($_entryob->getVar('headline'));
            redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_ARTISDELETED, $headline));
        } else {
            $articleID = \Xmf\Request::getInt('articleID', \Xmf\Request::getInt('articleID', 0, 'GET'), 'POST');
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
                              'headline'  => $headline,
                          ] + $xoopsGTicket->getTicketArray(__LINE__), 'article.php', _AM_SOAPBOX_DELETETHISARTICLE . '<br><br>' . $headline, _AM_SOAPBOX_DELETE);
            xoops_cp_footer();
        }
        exit();
        break;
    case 'reorder':
        //-------------------------
        if (!$xoopsGTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
        }
        $entrydataHandler->reorderArticlesUpdate($_POST['articleweight']);
        redirect_header('index.php', 1, _AM_SOAPBOX_ORDERUPDATED);
        break;
    case 'default':
    default:
        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        editarticle(0);
        //showArticles (0);
        break;
}
require_once __DIR__ . '/admin_footer.php';
