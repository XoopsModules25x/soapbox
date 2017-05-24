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

/* General Stuff */
require_once __DIR__ . '/admin_header.php';
$adminObject = \Xmf\Module\Admin::getInstance();

$op = '';
if (isset($_GET['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_GET['op'])));
}
if (isset($_POST['op'])) {
    $op = trim(strip_tags($myts->stripSlashesGPC($_POST['op'])));
}

$entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());

/**
 * @param int|string $columnID
 */
function editcol($columnID = '')
{
    global $xoopsGTicket, $indexAdmin;
    global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsLogger, $xoopsOption, $xoopsUserIsAdmin;
    $adminObject = Xmf\Module\Admin::getInstance();
    $xoopsDB     = XoopsDatabaseFactory::getDatabaseConnection();
    $myts        = MyTextSanitizer::getInstance();

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    $columnID         = (int)$columnID;
    $entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());
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

        $sform = new XoopsThemeForm(_AM_SOAPBOX_MODCOL . ': ' . $_categoryob->getVar('name'), 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
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
        //        echo "<h3 style='color: #2F5376; '>"._AM_SOAPBOX_ADMINCOLMNGMT."</h3>";

        //editcol(0);

        $sform = new XoopsThemeForm(_AM_SOAPBOX_NEWCOL, 'op', $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')), 'post', true);
    }

    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new XoopsFormText(_AM_SOAPBOX_COLNAME, 'name', 50, 80, $e_category['name']), true);

    /*
        ob_start();
        getuserForm((int)($e_category['author']));
        $sform->addElement(new XoopsFormLabel(_AM_SOAPBOX_AUTHOR, ob_get_contents()));
        ob_end_clean();
    */
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    $userstart = isset($_GET['userstart']) ? (int)$_GET['userstart'] : 0;

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
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('uid', $authorid, '!='));
    $criteria->setSort('uname');
    $criteria->setOrder('ASC');
    $criteria->setLimit(199);
    $criteria->setStart($userstart);
    $user_list_arr = array($authorid => $authoruname) + $memberHandler->getUserList($criteria);

    $nav = new XoopsPageNav($usercount, 200, $userstart, 'userstart', $myts->htmlSpecialChars('op=mod&columnID=' . $columnID));

    $user_select = new XoopsFormSelect('', 'author', (int)$authorid);
    $user_select->addOptionArray($user_list_arr);
    $user_select_tray = new XoopsFormElementTray(_AM_SOAPBOX_AUTHOR, '<br>');
    $user_select_tray->addElement($user_select);
    $user_select_nav = new XoopsFormLabel('', $nav->renderNav(4));
    $user_select_tray->addElement($user_select_nav);
    $sform->addElement($user_select_tray);

    //HACK by domifara for Wysiwyg
    $sform->addElement(new XoopsFormTextArea(_AM_SOAPBOX_COLDESCRIPT, 'description', $e_category['description'], 7, 60));
    //    $editor=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _AM_SOAPBOX_COLDESCRIPT, 'description',  $e_category['description'], '100%', '300px');
    //    $sform->addElement($editor,true);

    $sform->addElement(new XoopsFormText(_AM_SOAPBOX_COLPOSIT, 'weight', 4, 4, $e_category['weight']));

    // notification public
    $notifypub_radio = new XoopsFormRadioYN(_AM_SOAPBOX_NOTIFY, 'notifypub', $e_category['notifypub'], ' ' . _AM_SOAPBOX_YES . '', ' ' . _AM_SOAPBOX_NO . '');
    $sform->addElement($notifypub_radio);

    if (!isset($e_category['colimage']) || empty($e_category['colimage']) || $e_category['colimage'] === '') {
        $e_category['colimage'] = 'nopicture.png';
    }
    $graph_array     = XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']));
    $colimage_select = new XoopsFormSelect('', 'colimage', $e_category['colimage']);
    $colimage_select->addOptionArray($graph_array);
    $colimage_select->setExtra("onchange='showImgSelected(\"image3\", \"colimage\", \"" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . '", "", "' . XOOPS_URL . "\")'");
    $colimage_tray = new XoopsFormElementTray(_AM_SOAPBOX_COLIMAGE, '&nbsp;');
    $colimage_tray->addElement($colimage_select);
    $colimage_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . '/' . $e_category['colimage'] . "' name='image3' id='image3' alt='' />"));
    $sform->addElement($colimage_tray);

    // Code to call the file browser to select an image to upload
    $sform->addElement(new XoopsFormFile(_AM_SOAPBOX_COLIMAGEUPLOAD, 'cimage', (int)$xoopsModuleConfig['maxfilesize']), false);

    $sform->addElement(new XoopsFormHidden('columnID', $e_category['columnID']));

    $button_tray = new XoopsFormElementTray('', '');
    $hidden      = new XoopsFormHidden('op', 'addcol');
    $button_tray->addElement($hidden);

    // No ID for column -- then it's new column, button says 'Create'
    if (empty($e_category['columnID'])) {
        $butt_create = new XoopsFormButton('', '', _AM_SOAPBOX_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
        $button_tray->addElement($butt_create);

        $butt_clear = new XoopsFormButton('', '', _AM_SOAPBOX_CLEAR, 'reset');
        $button_tray->addElement($butt_clear);

        $butt_cancel = new XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    } else {        // button says 'Update'
        $butt_create = new XoopsFormButton('', '', _AM_SOAPBOX_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
        $button_tray->addElement($butt_create);

        $butt_cancel = new XoopsFormButton('', '', _AM_SOAPBOX_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);
    //-----------
    $xoopsGTicket->addTicketXoopsFormElement($sform, __LINE__);
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
        if (!$xoopsGTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
        }
        //-------------------------
        //articleID check
        if (!isset($_POST['columnID'])) {
            redirect_header('index.php', 1, _AM_SOAPBOX_ARTNOTCREATED);
        } else {
            $columnID = (int)$_POST['columnID'];
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
            $_categoryob->setVar('weight', (int)$_POST['weight']);
        }
        if (isset($_POST['notifypub'])) {
            $_categoryob->setVar('notifypub', (int)$_POST['notifypub']);
        }

        if (isset($_POST['author'])) {
            if ($_POST['author'] === '-1' && isset($_POST['authorinput'])) {
                $author = (int)$_POST['authorinput'];
            } else {
                $author = (int)$_POST['author'];
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
            if ($colimage_name !== '') {
                if (file_exists(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $colimage_name)) {
                    redirect_header('column.php', 1, _AM_SOAPBOX_FILEEXISTS);
                }
                $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
                SoapboxUtility::uploadFile($allowed_mimetypes, $colimage_name, 'index.php', 0, $xoopsModuleConfig['sbuploaddir']);
                $_categoryob->setVar('colimage', $colimage_name);
            }
        }
        if ($_categoryob->getVar('colimage') === '') {
            $_categoryob->setVar('colimage', 'blank.png');
        }
        //-----------------

        // Save to database
        if ($_categoryob->_isNew) {
            if (!$entrydataHandler->insertColumn($_categoryob)) {
                xoops_cp_header();
                $adminObject->displayNavigation(basename(__FILE__));
                //                print_r($_categoryob->getErrors());
                xoops_cp_footer();
                //                exit();
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

        $confirm = isset($_POST['confirm']) ? (int)$_POST['confirm'] : 0;

        // confirmed, so delete
        if ($confirm === 1) {
            //-------------------------
            if (!$xoopsGTicket->check()) {
                redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
            }
            //-------------------------
            //columnID check
            if (!isset($_POST['columnID'])) {
                redirect_header('index.php', 1, _NOPERM);
            } else {
                $columnID = (int)$_POST['columnID'];
            }
            //get category object
            $_categoryob = $entrydataHandler->getColumn($columnID);
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
                $gpermHandler = xoops_getHandler('groupperm');

                $name = $myts->htmlSpecialChars($_categoryob->getVar('name'));
                xoops_groupperm_deletebymoditem($module_id, _AM_SOAPBOX_COLPERMS, $columnID);
                redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_COLISDELETED, $name));
            }
        } else {
            $columnID = isset($_POST['columnID']) ? (int)$_POST['columnID'] : (int)$_GET['columnID'];
            //get category object
            $_categoryob = $entrydataHandler->getColumn($columnID);
            if (!is_object($_categoryob)) {
                redirect_header('index.php', 1, _NOPERM);
            }
            $name = $myts->htmlSpecialChars($_categoryob->getVar('name'));
            xoops_cp_header();
            $adminObject->displayNavigation(basename(__FILE__));
            xoops_confirm(array(
                              'op'       => 'del',
                              'columnID' => $columnID,
                              'confirm'  => 1,
                              'name'     => $name
                          ) + $xoopsGTicket->getTicketArray(__LINE__), 'column.php', _AM_SOAPBOX_DELETETHISCOL . '<br><br>' . $name, _AM_SOAPBOX_DELETE);
            xoops_cp_footer();
        }
        exit();
        break;

    case 'cancel':
        redirect_header('index.php', 1, sprintf(_AM_SOAPBOX_BACK2IDX, ''));
        break;

    case 'reorder':
        //-------------------------
        if (!$xoopsGTicket->check()) {
            redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
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
