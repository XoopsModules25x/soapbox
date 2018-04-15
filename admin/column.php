<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Soapbox;

/* General Stuff */
require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();

/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

$op = '';
if (isset($_GET['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
}
if (isset($_POST['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
}

$entrydataHandler = $helper->getHandler('Entrydata');

/**
 * @param int|string $columnID
 */
function editcol($columnID = '')
{
    global $indexAdmin;
    global $xoopsUser, $xoopsConfig,  $xoopsModule, $xoopsLogger, $xoopsOption, $xoopsUserIsAdmin;
    /** @var Soapbox\Helper $helper */
    $helper = Soapbox\Helper::getInstance();

    $adminObject = \Xmf\Module\Admin::getInstance();
    $xoopsDB     = \XoopsDatabaseFactory::getDatabaseConnection();
    $myts        = \MyTextSanitizer::getInstance();

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $columnID         = (int)$columnID;
    $entrydataHandler = $helper->getHandler('Entrydata');
    // If there is a parameter, and the id exists, retrieve data: we're editing a column
    if (0 !== $columnID) {
        //get category object
        $_categoryob = $entrydataHandler->getColumn($columnID);
        if (!is_object($_categoryob)) {
            redirect_header('index.php', 1, _AM_SOAPBOX_NOCOLTOEDIT);
        }
        //get vars
        $category_vars = $_categoryob->getVars();
        foreach ($category_vars as $k => $v) {
            $e_category[$k] = $_categoryob->getVar($k, 'E');
        }

        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        //adminMenu(1, _AM_SOAPBOX_COLS._AM_SOAPBOX_EDITING . $_categoryob->getVar('name') . "'");
        //echo "<h3 style='color: #2F5376; '>"._AM_SOAPBOX_ADMINCOLMNGMT."</h3>";

        //editcol(0);

        $sform = new \XoopsThemeForm(_AM_SOAPBOX_MODCOL . ': ' . $_categoryob->getVar('name'), 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
    } else {
        $_categoryob = $entrydataHandler->createColumn(true);
        $_categoryob->cleanVars();

        //get vars
        $category_vars = $_categoryob->getVars();
        foreach ($category_vars as $k => $v) {
            $e_category[$k] = $_categoryob->getVar($k, 'E');
        }

        $e_category['weight'] = 1;
        $e_category['author'] = $xoopsUser->uid();

        xoops_cp_header();
        $adminObject->displayNavigation(basename(__FILE__));
        //adminMenu(1, _AM_SOAPBOX_COLS._AM_SOAPBOX_CREATINGCOL);
        //echo "<h3 style='color: #2F5376; '>"._AM_SOAPBOX_ADMINCOLMNGMT."</h3>";

        //editcol(0);

        $sform = new \XoopsThemeForm(_AM_SOAPBOX_NEWCOL, 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
    }

    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_COLNAME, 'name', 50, 80, $e_category['name']), true);

    /*
        ob_start();
        getuserForm((int)($e_category['author']));
        $sform->addElement(new \XoopsFormLabel(_AM_SOAPBOX_AUTHOR, ob_get_contents()));
        ob_end_clean();
    */
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    $userstart = \Xmf\Request::getInt('userstart', 0, 'GET');

    $memberHandler = xoops_getHandler('member');
    $usercount     = $memberHandler->getUserCount();
    // Selector to get author
    if (empty($e_category['author'])) {
        $authorid    = $xoopsUser->uid();
        $authoruname = $xoopsUser->uname();
    } else {
        $author_ob   = $memberHandler->getUser($e_category['author']);
        $authorid    = $author_ob->uid();
        $authoruname = $author_ob->uname();
    }
    $criteria = new \CriteriaCompo();
    $criteria->add(new \Criteria('uid', $authorid, '!='));
    $criteria->setSort('uname');
    $criteria->setOrder('ASC');
    $criteria->setLimit(199);
    $criteria->setStart($userstart);
    $user_list_arr = [$authorid => $authoruname] + $memberHandler->getUserList($criteria);

    $nav = new \XoopsPageNav($usercount, 200, $userstart, 'userstart', $myts->htmlSpecialChars('op=mod&columnID=' . $columnID));

    $user_select      = new \XoopsFormSelect('', 'author', $authorid);
    $user_select->addOptionArray($user_list_arr);
    $user_select_tray = new \XoopsFormElementTray(_AM_SOAPBOX_AUTHOR, '<br>');
    $user_select_tray->addElement($user_select);
    $user_select_nav  = new \XoopsFormLabel('', $nav->renderNav(4));
    $user_select_tray->addElement($user_select_nav);
    $sform->addElement($user_select_tray);

    //HACK by domifara for Wysiwyg
    $sform->addElement(new \XoopsFormTextArea(_AM_SOAPBOX_COLDESCRIPT, 'description', $e_category['description'], 7, 60));
    //    $editor=soapbox_getWysiwygForm($helper->getConfig('editorUser') , _AM_SOAPBOX_COLDESCRIPT, 'description',  $e_category['description'], '100%', '300px');
    //    $sform->addElement($editor,true);

    $sform->addElement(new \XoopsFormText(_AM_SOAPBOX_COLPOSIT, 'weight', 4, 4, $e_category['weight']));

    // notification public
    $notifypub_radio = new \XoopsFormRadioYN(_AM_SOAPBOX_NOTIFY, 'notifypub', $e_category['notifypub'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($notifypub_radio);

    if (!isset($e_category['colimage']) || empty($e_category['colimage']) || '' === $e_category['colimage']) {
        $e_category['colimage'] = 'nopicture.png';
    }
    $graph_array     = \XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')));
    $colimage_select = new \XoopsFormSelect('', 'colimage', $e_category['colimage']);
    $colimage_select->addOptionArray($graph_array);
    $colimage_select->setExtra("onchange='showImgSelected(\"image3\", \"colimage\", \"" . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '", "", "' . XOOPS_URL . "\")'");
    $colimage_tray   = new \XoopsFormElementTray(_AM_SOAPBOX_COLIMAGE, '&nbsp;');
    $colimage_tray->addElement($colimage_select);
    $colimage_tray->addElement(new \XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $myts->htmlSpecialChars($helper->getConfig('sbuploaddir')) . '/'
                                                                                        . $e_category['colimage'] . "' name='image3' id='image3' alt=''>"));
    $sform->addElement($colimage_tray);

    // Code to call the file browser to select an image to upload
    $sform->addElement(new \XoopsFormFile(_AM_SOAPBOX_COLIMAGEUPLOAD, 'cimage', (int)$helper->getConfig('maxfilesize')), false);

    $sform->addElement(new \XoopsFormHidden('columnID', $e_category['columnID']));

    $button_tray = new \XoopsFormElementTray('', '');
    $hidden      = new \XoopsFormHidden('op', 'addcol');
    $button_tray->addElement($hidden);

    // No ID for column -- then it's new column, button says 'Create'
    if (empty($e_category['columnID'])) {
        $butt_create = new \XoopsFormButton('', '', _AM_SOAPBOX_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
        $button_tray->addElement($butt_create);

        $butt_clear  = new \XoopsFormButton('', '', _AM_SOAPBOX_CLEAR, 'reset');
        $button_tray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    } else {        // button says 'Update'
        $butt_create = new \XoopsFormButton('', '', _AM_SOAPBOX_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
        $button_tray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);
    //-----------
    //    $xoopsGTicket->addTicketXoopsFormElement($sform, __LINE__);
    //-----------
    $sform->display();
    unset($hidden);
}

switch ($op) {
    case 'mod':
        $columnID = Request::getInt('columnID', Request::getInt('columnID', 0, 'GET'), 'POST');//isset($_POST['columnID']) ? (int)($_POST['columnID']) : (int)($_GET['columnID']);
        editcol($columnID);
        break;

    case 'addcol':
        //-------------------------
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
        }
        //-------------------------
        //articleID check
        if (!isset($_POST['columnID'])) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $columnID = \Xmf\Request::getInt('columnID', 0, 'POST');
        }

        //get category object
        $_categoryob = $entrydataHandler->getColumn($columnID);
        //new data or edit
        if (!is_object($_categoryob)) {
            $_categoryob = $entrydataHandler->createColumn(true);
            $_categoryob->cleanVars();

            $_categoryob->setVar('created', time());
        }

        if (isset($_POST['columnID'])) {
            $_categoryob->setVar('columnID', $columnID);
        }
        if (isset($_POST['name'])) {
            $_categoryob->setVar('name', $_POST['name']);
        }
        if (isset($_POST['description'])) {
            $_categoryob->setVar('description', $_POST['description']);
        }

        if (isset($_POST['weight'])) {
            $_categoryob->setVar('weight', \Xmf\Request::getInt('weight', 0, 'POST'));
        }
        if (isset($_POST['notifypub'])) {
            $_categoryob->setVar('notifypub', \Xmf\Request::getInt('notifypub', 0, 'POST'));
        }

        if (isset($_POST['author'])) {
            if ('-1' === $_POST['author'] && isset($_POST['authorinput'])) {
                $author = \Xmf\Request::getInt('authorinput', 0, 'POST');
            } else {
                $author = \Xmf\Request::getInt('author', 0, 'POST');
            }
        } else {
            $author = $xoopsUser->uid();
        }
        $_categoryob->setVar('author', $author);

        //-----------------
        //colimage
        if (isset($_POST['colimage'])) {
            $_categoryob->setVar('colimage', $_POST['colimage']);
        }
        if (isset($_FILES['cimage']['name'])) {
            $colimage_name = trim(strip_tags($myts->stripSlashesGPC($_FILES['cimage']['name'])));
            if ('' !== $colimage_name) {
                if (file_exists(XOOPS_ROOT_PATH . '/' . $helper->getConfig('sbuploaddir') . '/' . $colimage_name)) {
                    redirect_header('column.php', 1, _AM_SOAPBOX_FILEEXISTS);
                }
                $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'];
                Soapbox\Utility::uploadFile($allowed_mimetypes, $colimage_name, 'index.php', 0, $helper->getConfig('sbuploaddir'));
                $_categoryob->setVar('colimage', $colimage_name);
            }
        }
        if ('' === $_categoryob->getVar('colimage')) {
            $_categoryob->setVar('colimage', 'blank.png');
        }
        //-----------------

        // Save to database
        if ($_categoryob->_isNew) {
            if (!$entrydataHandler->insertColumn($_categoryob)) {
                xoops_cp_header();
                $adminObject->displayNavigation(basename(__FILE__));
                // print_r($_categoryob->getErrors());
                xoops_cp_footer();
                // exit();
                redirect_header('index.php', 1, _AM_SOAPBOX_NOTUPDATED);
            } else {
                //event trigger
                $entrydataHandler->newColumnTriggerEvent($_categoryob, 'new_column');
                redirect_header('permissions.php', 1, _AM_SOAPBOX_COLCREATED);
            }
        } else {
            if (!$entrydataHandler->insertColumn($_categoryob)) {
                redirect_header('index.php', 1, _AM_SOAPBOX_NOTUPDATED);
            } else {
                redirect_header('index.php', 1, _AM_SOAPBOX_COLMODIFIED);
                //
            }
        }
        exit();
        break;

    case 'del':

        $confirm = \Xmf\Request::getInt('confirm', 0, 'POST');

        // confirmed, so delete
        if (1 === $confirm) {
            //-------------------------
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
            }
            //-------------------------
            //columnID check
            if (!isset($_POST['columnID'])) {
                redirect_header('index.php', 1, _NOPERM);
            } else {
                $columnID = \Xmf\Request::getInt('columnID', 0, 'POST');
            }
            //get category object
            $_categoryob  = $entrydataHandler->getColumn($columnID);
            if (!is_object($_categoryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            //
            if (!$entrydataHandler->deleteColumn($_categoryob)) {
                trigger_error('ERROR:not deleted from database');
                exit();
            } else {
                $groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
                $module_id    = $xoopsModule->getVar('mid');
                $grouppermHandler = xoops_getHandler('groupperm');

                $name = $myts->htmlSpecialChars($_categoryob->getVar('name'));
                xoops_groupperm_deletebymoditem($module_id, _AM_SOAPBOX_COLPERMS, $columnID);
                redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_COLISDELETED, $name));
            }
        } else {
            $columnID    = \Xmf\Request::getInt('columnID', \Xmf\Request::getInt('columnID', 0, 'GET'), 'POST');
            //get category object
            $_categoryob = $entrydataHandler->getColumn($columnID);
            if (!is_object($_categoryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            $name = $myts->htmlSpecialChars($_categoryob->getVar('name'));
            xoops_cp_header();
            $adminObject->displayNavigation(basename(__FILE__));
            xoops_confirm([
                              'op'       => 'del',
                              'columnID' => $columnID,
                              'confirm'  => 1,
                              'name'     => $name
                          ], 'column.php', _AM_SOAPBOX_DELETETHISCOL . '<br><br>' . $name, _AM_SOAPBOX_DELETE);
            xoops_cp_footer();
        }
        exit();
        break;

    case 'cancel':
        redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_BACK2IDX, ''));
        break;

    case 'reorder':
        //-------------------------
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header(XOOPS_URL . '/', 3, $GLOBALS['xoopsSecurity']->getErrors());
        }
        $entrydataHandler->reorderColumnsUpdate($_POST['columnweight']);
        redirect_header('./column.php', 1, _AM_SOAPBOX_ORDERUPDATED);

        break;

    case 'default':
    default:
        //$adminObject->displayNavigation(basename(__FILE__));
        editcol(0);
        //    SoapboxUtility::showColumns(0);

        break;
}
require_once __DIR__ . '/admin_footer.php';
