<?php

/**
 * Class MyalbumUtility
 */
class SoapboxUtility extends XoopsObject
{
    /**
     * Function responsible for checking if a directory exists, we can also write in and create an index.html file
     *
     * @param string $folder The full path of the directory to check
     *
     * @return void
     */
    public static function createFolder($folder)
    {
        try {
            if (!file_exists($folder)) {
                if (!mkdir($folder) && !is_dir($folder)) {
                    throw new \RuntimeException(sprintf('Unable to create the %s directory', $folder));
                } else {
                    file_put_contents($folder . '/index.html', '<script>history.go(-1);</script>');
                }
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n", '<br>';
        }
    }

    /**
     * @param $file
     * @param $folder
     * @return bool
     */
    public static function copyFile($file, $folder)
    {
        return copy($file, $folder);
        //        try {
        //            if (!is_dir($folder)) {
        //                throw new \RuntimeException(sprintf('Unable to copy file as: %s ', $folder));
        //            } else {
        //                return copy($file, $folder);
        //            }
        //        } catch (Exception $e) {
        //            echo 'Caught exception: ', $e->getMessage(), "\n", "<br>";
        //        }
        //        return false;
    }

    /**
     * @param $src
     * @param $dst
     */
    public static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        //    @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @param null|string $requiredVer
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(XoopsModule $module = null, $requiredVer = null)
    {
        $moduleDirName = basename(dirname(__DIR__));
        if (null === $module) {
            $module = XoopsModule::getByDirname($moduleDirName);
        }
        xoops_loadLanguage('admin', $moduleDirName);
        //check for minimum XOOPS version
        $currentVer = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray  = explode('.', $currentVer);
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $reqArray = explode('.', $requiredVer);
        $success  = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int)$v > 0) { // handles things like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $module->setErrors(sprintf(_AM_SOAPBOX_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }

    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // check for minimum PHP version
        $success = true;
        $verNum  = PHP_VERSION;
        $reqVer  = $module->getInfo('min_php');
        if (false !== $reqVer && '' !== $reqVer) {
            if (version_compare($verNum, $reqVer, '<')) {
                $module->setErrors(sprintf(_AM_SOAPBOX_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }

        return $success;
    }

    /**
     * getLinkedUnameFromId()
     *
     * @param  integer $userid Userid of author etc
     * @param  integer $name   :  0 Use Usenamer 1 Use realname
     * @return string
     */
    public static function getLinkedUnameFromId($userid = 0, $name = 0)
    {
        if (!is_numeric($userid)) {
            return $userid;
        }
        $myts   = MyTextSanitizer::getInstance();
        $userid = (int)$userid;
        if ($userid > 0) {
            $memberHandler = xoops_getHandler('member');
            $user          = $memberHandler->getUser($userid);

            if (is_object($user)) {
                $username  = $user->getVar('uname');
                $usernameu = $user->getVar('name');

                if ($name && !empty($usernameu)) {
                    $username = $user->getVar('name');
                }
                if (!empty($usernameu)) {
                    $linkeduser = $myts->htmlSpecialChars($usernameu) . " [<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . $myts->htmlSpecialChars($username) . '</a>]';
                } else {
                    //                    $linkeduser = "<a href='".XOOPS_URL."/userinfo.php?uid=".$userid."'>". ucfirst($ts->htmlSpecialChars($username)) .'</a>';
                    $linkeduser = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . $myts->htmlSpecialChars($username) . '</a>';
                }

                return $linkeduser;
            }
        }

        return $myts->htmlSpecialChars($GLOBALS['xoopsConfig']['anonymous']);
    }

    /*
    public static function displayimage($image = 'blank.gif', $path = '', $imgsource = '', $alttext = '')
    {
        global $xoopsConfig, $xoopsUser, $xoopsModule;
        $myts = MyTextSanitizer::getInstance();
        $showimage = '';

        if ($path) {
            $showimage = "<a href='" . $myts->htmlSpecialChars(strip_tags($path)) . "'>";
        }

        if (!is_dir(XOOPS_ROOT_PATH."/".$imgsource."/".$image) && file_exists(XOOPS_ROOT_PATH."/".$imgsource."/".$image)) {
            $showimage .= "<img src='".XOOPS_URL."/".$myts->htmlSpecialChars(strip_tags($imgsource))."/".$myts->htmlSpecialChars(strip_tags($image))."' border='0' alt=".$myts->htmlSpecialChars(strip_tags($alttext))."></a>";
        } else {
            if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
                $showimage .= "<img src='".XOOPS_URL.'/modules/'.$xoopsModule->dirname()."/assets/images/brokenimg.png' border='0' alt='"._AM_SOAPBOX_ISADMINNOTICE."'></a>";
            } else {
                $showimage .= "<img src='".XOOPS_URL.'/modules/'.$xoopsModule->dirname()."/assets/images/blank.png' border='0' alt=".$myts->htmlSpecialChars(strip_tags($alttext))."></a>";
            }
        }
        // clearstatcache();
        return $showimage;
    }
    */
    /**
     * @param        $allowed_mimetypes
     * @param        $httppostfiles
     * @param string $redirecturl
     * @param int    $num
     * @param string $dir
     * @param int    $redirect
     */
    public static function uploadFile(
        $allowed_mimetypes,
        $httppostfiles,
        $redirecturl = 'index.php',
        $num = 0,
        $dir = 'uploads',
        $redirect = 0
    ) {
        require_once XOOPS_ROOT_PATH . '/class/uploader.php';
        $myts = MyTextSanitizer::getInstance();

        global $xoopsConfig, $xoopsModuleConfig, $_POST;

        $maxfilesize   = (int)$xoopsModuleConfig['maxfilesize'];
        $maxfilewidth  = (int)$xoopsModuleConfig['maximgwidth'];
        $maxfileheight = (int)$xoopsModuleConfig['maximgheight'];
        $uploaddir     = XOOPS_ROOT_PATH . '/' . $myts->htmlSpecialChars(strip_tags($dir)) . '/';

        $uploader = new XoopsMediaUploader($uploaddir, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);

        if ($uploader->fetchMedia($myts->htmlSpecialChars(strip_tags($_POST['xoops_upload_file'][$num])))) {
            if (!$uploader->upload()) {
                $errors = $uploader->getErrors();
                redirect_header($redirecturl, 1, $errors);
            } else {
                if ($redirect) {
                    redirect_header($redirecturl, '1', 'Image Uploaded');
                }
            }
        } else {
            $errors = $uploader->getErrors();
            redirect_header($redirecturl, 1, $errors);
        }
    }

    /*
    public static function htmlarray($thishtmlpage, $thepath)
    {
        global $xoopsConfig, $wfsConfig;

        $file_array = filesarray( $thepath );

        echo "<select size='1' name='htmlpage'>";
        echo "<option value='-1'>------</option>";
        foreach ($file_array as $htmlpage) {
            if ($htmlpage == $thishtmlpage) {
                $opt_selected = "selected";
            } else {
                $opt_selected = "";
            }
            echo "<option value='" . $htmlpage . "' $opt_selected>" . $htmlpage . "</option>";
        }
        echo "</select>";

        return $htmlpage;
    }
    */
    /*
    public static function filesarray($filearray)
    {
        $files = array();
        $dir = opendir( $filearray );

        while ( ( $file = readdir( $dir ) ) !== false ) {
            if ( ( !preg_match( "/^[.]{1,2}$/", $file ) && preg_match( "/[.htm|.html|.xhtml]$/i", $file ) && !is_dir( $file ) ) ) {
                if ( strtolower( $file ) != 'cvs' && !is_dir( $file ) ) {
                    $files[$file] = $file;
                }
            }
        }
        closedir( $dir );
        asort( $files );
        reset( $files );

        return $files;
    }
    */
    /*
    public static function getuserForm($user)
    {
        global $xoopsDB, $xoopsConfig;
        $myts = MyTextSanitizer::getInstance();

        echo "<select name='author'>";
        echo "<option value='-1'>------</option>";
        $result = $xoopsDB->query("SELECT uid, uname FROM ".$xoopsDB->prefix("users")." ORDER BY uname");

        while (list($uid, $uname) = $xoopsDB->fetchRow($result)) {
            if ($uid == $user) {
                $opt_selected = "selected";
            } else {
                $opt_selected = "";
            }
            echo "<option value='".(int)($uid)."' $opt_selected>".$myts->htmlSpecialChars($uname)."</option>";
        }
        echo "</select>";
    }
    */

    /**
     * @param $author
     * @return string
     */
    public static function getAuthorName($author)
    {
        $ret = '';
        //get author
        $_authoruserHandler = xoops_getHandler('user');
        $_authoruser        = $_authoruserHandler->get($author);
        if (!is_object($_authoruser)) {
            $name3      = '';
            $uname3     = '';
            $authorname = '';
        } else {
            $name3      = $_authoruser->getVar('name');
            $uname3     = $_authoruser->getVar('uname');
            $authorname = $name3;
        }
        //-------------------------------------
        $ret = $authorname;
        if (empty($authorname) || $authorname === '') {
            $ret = $uname3;
        }

        return $ret;
        //-------------------------------------
    }

    /**
     * @param int $showCreate
     */
    public static function showColumns($showCreate = 0)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
        $myts       = MyTextSanitizer::getInstance();
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';
        $module_id = $xoopsModule->getVar('mid');
        $startcol  = isset($_GET['startcol']) ? (int)$_GET['startcol'] : 0;

        /* Code to show existing columns */
        echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SOAPBOX_SHOWCOLS . '</h3>';
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SOAPBOX_COLSTEXT . '</span>';

        //    if ($showCreate == 1) {
        //        echo
        //            "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='column.php'>"
        //            . _AM_SOAPBOX_CREATECOL . "</a><br><br>";
        //    }
        // To create existing columns table
        //----------------------------
        //get category object
        $entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());
        $numrows          = $entrydataHandler->getColumnCount();
        $criteria         = new CriteriaCompo();
        $criteria->setSort('weight');
        $criteria->setLimit((int)$xoopsModuleConfig['perpage']);
        $criteria->setStart((int)$startcol);
        $categoryobArray = $entrydataHandler->getColumns($criteria);
        unset($criteria);
        if ($numrows > 0) {
            echo '<form action="column.php" method="post" name="reordercols">';
        }
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo '<tr>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ID . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_WEIGHT . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_AUTHOR . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ARTCOLNAME . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_DESCRIP . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ACTION . '</b></td>';
        echo '</tr>';

        if ($numrows > 0) { // That is, if there ARE columns in the system
            //----------------------------
            $cont = 0;
            foreach ($categoryobArray as $_categoryob) {
                //----------------------------
                //get vars
                ++$cont;
                $category      = $_categoryob->toArray(); //all assign
                $category_vars = $_categoryob->getVars();
                foreach ($category_vars as $k => $v) {
                    ${$k} = $_categoryob->getVar($k);
                }
                //----------------------------

                $author = SoapboxUtility::getLinkedUnameFromId($author, 0);
                $modify = "<a href='column.php?op=mod&columnID=" . $category['columnID'] . "'><img src='" . $pathIcon16 . "/edit.png' ALT='" . _AM_SOAPBOX_EDITCOL . "'></a>";
                $delete = "<a href='column.php?op=del&columnID=" . $category['columnID'] . "'><img src='" . $pathIcon16 . "/delete.png' ALT='" . _AM_SOAPBOX_DELETECOL . "'></a>";
                $style  = (($cont % 2) === 0) ? 'even' : 'odd';
                echo '<tr class="' . $style . '">';
                echo '<td class="txtcenter">' . $category['columnID'] . '</td>';
                echo '<td class="txtcenter"><input type="text" name="columnweight[' . $category['columnID'] . ']" value="' . $weight . '" size="3" maxlength="3" style="text-align: center;"></td>';
                echo '<td class="txtcenter">' . $category['author'] . '</td>';
                echo '<td class="txtcenter">' . $category['name'] . '</td>';
                echo '<td class="txtcenter">' . $category['description'] . '</td>';
                echo '<td class="txtcenter">' . $modify . ' ' . $delete . '</td>';
                echo '</tr>';
            }
        } else { // that is, $numrows = 0, there's no columns yet
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SOAPBOX_NOCOLS . '</td>';
            echo '</tr>';
            $category['columnID'] = '0';
        }
        echo "</table>\n";
        $pagenav = new XoopsPageNav($numrows, (int)$xoopsModuleConfig['perpage'], $startcol, 'startcol', 'columnID=' . $category['columnID']);
        echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
        echo "<br>\n";

        if ($numrows > 0) {
            echo "<input type='hidden' name='op' value='reorder'>";
            //--------------------
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            //--------------------
            echo '<div style="margin-bottom: 18px;"><input type="submit" name="submit" class="formButton" value="' . _AM_SOAPBOX_REORDERCOL . '"></div>';
            echo '</form>';
        }
    }

    /**
     * @param int $showCreate
     */
    public static function showArticles($showCreate = 0)
    {
        global $xoopsModuleConfig, $xoopsModule;
        $myts = MyTextSanitizer::getInstance();

        $pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

        $module_id = $xoopsModule->getVar('mid');
        $startart  = isset($_GET['startart']) ? (int)$_GET['startart'] : 0;
        if (isset($_POST['entries'])) {
            $entries = (int)$_POST['entries'];
        } else {
            $entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 0;
        }
        //---GET view sort --
        $sortname = isset($_GET['sortname']) ? strtolower(trim(strip_tags($myts->stripSlashesGPC($_GET['sortname'])))) : 'datesub';
        if (!in_array($sortname, array('datesub', 'weight', 'counter', 'rating', 'headline'))) {
            $sortname = 'datesub';
        }
        $sortorder = isset($_GET['sortorder']) ? strtoupper(trim(strip_tags($myts->stripSlashesGPC($_GET['sortorder'])))) : 'DESC';
        if (!in_array($sortorder, array('ASC', 'DESC'))) {
            $sortorder = 'DESC';
        }
        //---------------
        /* Code to show existing articles */
        echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SOAPBOX_SHOWARTS . '</h3>';
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SOAPBOX_ARTSTEXT . '</span>';

        //    if ($showCreate == 1) {
        //        echo
        //            "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='article.php'>"
        //            . _AM_SOAPBOX_CREATEART . "</a><br><br>";
        //    }
        // Articles count
        $entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());
        //----------------------------
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('submit', 0));
        $criteria->add(new Criteria('offline', 0));
        $tot_published = $entrydataHandler->getArticleCount($criteria);
        unset($criteria);
        //----------------------------
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('submit', 0));
        $criteria->add(new Criteria('offline', 1));
        $tot_offline = $entrydataHandler->getArticleCount($criteria);
        unset($criteria);
        //----------------------------
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('submit', 1));
        $tot_submitted = $entrydataHandler->getArticleCount($criteria);
        unset($criteria);
        //----------------------------
        $tot_all = $entrydataHandler->getArticleCount();
        //----------------------------
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('submit', 0));
        $tot_ok = $entrydataHandler->getArticleCount($criteria);
        unset($criteria);
        //----------------------------

        // Prepare string for table head
        if ($entries === 0) {
            $string = _AM_SOAPBOX_SHWALL;
        }
        if ($entries === 1) {
            $string = _AM_SOAPBOX_SHWONL;
        }
        if ($entries === 2) {
            $string = _AM_SOAPBOX_SHWOFF;
        }
        if ($entries === 3) {
            $string = _AM_SOAPBOX_SHWSUB;
        }
        if ($entries === 4) {
            $string = _AM_SOAPBOX_SHWAPV;
        }

        /* Code to show selected articles */
        echo "<form name='pick' id='pick' action='" . $myts->htmlSpecialChars(xoops_getenv('PHP_SELF')) . "' method='POST' style='margin: 0;'>"; ?>
        <table width='100%' cellspacing='1' cellpadding='2' border='0'
               style='border-left: 1px solid silver; border-top: 1px solid silver; border-right: 1px solid silver;'>
            <tr>
                <td class='odd'><span style='font-weight: bold; font-variant: small-caps;'><?php echo $string ?></span></td>
                <td class='odd' width='40%' align='right'><?php echo _AM_SOAPBOX_SELECTSTATUS; ?>
                    <select name='entries' onchange='submit()'>
                        <option value='0'
                            <?php
                            if ($entries === 0) {
                                echo 'selected';
                            } ?>>
                            <?php echo _AM_SOAPBOX_SELALL; ?>
                            [<?php echo $tot_all; ?>]
                        </option>
                        <option value='1' <?php if ($entries === 1) {
                                echo 'selected';
                            } ?>><?php echo _AM_SOAPBOX_SELONL; ?>
                            [<?php echo $tot_published; ?>]
                        </option>
                        <option value='2' <?php if ($entries === 2) {
                                echo 'selected';
                            } ?>>
                            <?php echo _AM_SOAPBOX_SELOFF; ?>
                            [<?php echo $tot_offline; ?>]
                        </option>
                        <option value='3' <?php if ($entries === 3) {
                                echo 'selected';
                            } ?>>
                            <?php echo _AM_SOAPBOX_SELSUB; ?>
                            [<?php echo $tot_submitted; ?>]
                        </option>
                        <option value='4' <?php if ($entries === 4) {
                                echo 'selected';
                            } ?>><?php echo _AM_SOAPBOX_SELAPV; ?>
                            [<?php echo $tot_ok; ?>]
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        </form>
        <?php

        //----------------------------
        // Put column names in an array, to avoid a query in the while loop further ahead
        switch ($entries) {
            case 1:
                $submit  = 0;
                $offline = 0;
                break;
            case 2:
                //----------------------------
                $submit  = 0;
                $offline = 1;
                break;
            case 3:
                //----------------------------
                $submit  = 1;
                $offline = null;
                break;
            case 4:
                //----------------------------
                $submit = 0;
                break;
            case 0:
            default:
                $submit  = null;
                $offline = null;
                break;
        }
        //    function &getArticlesAllPermcheck(
        //         $limit=0, $start=0,
        //         $checkRight = true, $published = true, $submit = 0, $offline = 0, $block = null ,
        //         $sortname = 'datesub', $sortorder = 'DESC',
        //         $select_sbcolumns = null , $NOTarticleIDs = null ,
        //         $approve_submit = false ,
        //         $id_as_key = false )
        //-------------------------------------
        $_entryob_arr = $entrydataHandler->getArticlesAllPermcheck((int)$xoopsModuleConfig['perpage'], $startart, false, false, $submit, $offline, null, $sortname, $sortorder, null, null, false, true);
        // Get number of articles in the selected condition ($cond)
        $numrows = $entrydataHandler->total_getArticlesAllPermcheck;
        if ($numrows > 0) {
            echo '<form action="article.php" method="post" name="reorderarticles\">';
        }
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo '<tr>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ARTID . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_WEIGHT . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ARTCOLNAME . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ARTHEADLINE . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ARTCREATED . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_STATUS . '</b></td>';
        echo '<th class="txtcenter"><b>' . _AM_SOAPBOX_ACTION . '</b></td>';
        echo '</tr>';

        if ($numrows > 0) { // That is, if there ARE articles in the said condition
            // Retrieve rows for those items

            $colarray = array();
            $cont     = 0;

            foreach ($_entryob_arr as $key => $_entryob) {
                //get vars
                ++$cont;
                //-------------------------------------
                $articles = $_entryob->toArray();
                //--------------------
                $colname = !empty($_entryob->_sbcolumns) ? $_entryob->_sbcolumns->getVar('name') : '';
                //--------------------
                $created = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $xoopsModuleConfig['dateformat']));
                $modify  = "<a href='article.php?op=mod&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/edit.png' ALT='" . _AM_SOAPBOX_EDITART . "'></a>";
                $delete  = "<a href='article.php?op=del&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/delete.png' ALT='" . _AM_SOAPBOX_DELETEART . "'></a>";

                //if ($offline == 0) {
                if ($articles['offline'] === 0) {
                    $status = "<img src='" . $pathIcon16 . "/1.png' alt='" . _AM_SOAPBOX_ARTISON . "'>";
                } else {
                    //if ($offline == 1 && $submit == 0) {
                    if ($submit === 0 && $articles['offline'] === 1) {
                        $status = "<img src='" . $pathIcon16 . "/0.png' alt='" . _AM_SOAPBOX_ARTISOFF . "'>";
                    } else {
                        if ($submit === 1) {
                            $status = '<img src=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/sub.gif alt='" . _AM_SOAPBOX_ARTISSUB . "'>";
                        }
                    }
                }

                //mb ----------------------------
                //echo $cont.' - '.$offline.': '.$status.'</br>';

                $style = (($cont % 2) === 0) ? 'even' : 'odd';
                echo '<tr class="' . $style . '">';
                echo '<td align="center"><a href="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleID=' . $articles['articleID'] . '" title="' . $articles['headline'] . '" target="_blank">' . $articles['articleID'] . '</a></td>';
                echo '<td class="txtcenter"><input type="text" name="articleweight[' . $articles['articleID'] . ']" value="' . $articles['weight'] . '" size="3" maxlength="3" style="text-align: center;"></td>';
                echo '<td class="txtcenter">' . $colname . '</td>';
                echo '<td>' . $articles['headline'] . '</td>';
                echo '<td class="txtcenter">' . $created . '</td>';
                echo '<td class="txtcenter">' . $status . '</td>';
                echo '<td class="txtcenter">' . $modify . $delete . '</td>';
                echo '</tr>';
            }
        } else { // that is, $numrows = 0, there's no columns yet
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SOAPBOX_NOARTS . '</td>';
            echo '</tr>';
        }
        echo "</table>\n";
        $pagenav = new XoopsPageNav($numrows, (int)$xoopsModuleConfig['perpage'], $startart, 'startart', 'entries=' . $entries . '&sortname=' . $sortname . '&sortorder=' . $sortorder);
        echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

        if ($numrows > 0) {
            echo "<input type='hidden' name='op' value='reorder'>";
            //--------------------
            echo $GLOBALS['xoopsSecurity']->getTokenHTML();
            //--------------------
            echo '<div style="margin-bottom: 18px;"><input type="submit" name="submit" class="formButton" value="' . _AM_SOAPBOX_REORDERART . '"></div>';
            echo '</form>';
        }
        echo "<br>\n";
    }

    public static function showSubmissions()
    {
        global $xoopsModuleConfig, $xoopsModule;

        $pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
        $myts       = MyTextSanitizer::getInstance();
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';
        $module_id = $xoopsModule->getVar('mid');
        $startsub  = isset($_GET['startsub']) ? (int)$_GET['startsub'] : 0;
        $datesub   = isset($_GET['datesub']) ? (int)$_GET['datesub'] : 0;

        //---GET view sort --
        $sortname = isset($_GET['sortname']) ? strtolower(trim(strip_tags($myts->stripSlashesGPC($_GET['sortname'])))) : 'datesub';
        if (!in_array($sortname, array('datesub', 'weight', 'counter', 'rating', 'headline'))) {
            $sortname = 'datesub';
        }
        $sortorder = isset($_GET['sortorder']) ? strtoupper(trim(strip_tags($myts->stripSlashesGPC($_GET['sortorder'])))) : 'DESC';
        if (!in_array($sortorder, array('ASC', 'DESC'))) {
            $sortorder = 'DESC';
        }
        //---------------
        /* Code to show submitted articles */
        echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SOAPBOX_SHOWSUBMISSIONS . '</h3>';
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SOAPBOX_SUBTEXT . '</span>';
        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td width='40' class='bg3' align='center'><b>" . _AM_SOAPBOX_ARTID . '</b></td>';
        echo "<td width='20%' class='bg3' align='center'><b>" . _AM_SOAPBOX_ARTCOLNAME . '</b></td>';
        echo "<td width='45%' class='bg3' align='center'><b>" . _AM_SOAPBOX_ARTHEADLINE . '</b></td>';
        echo "<td width='90' class='bg3' align='center'><b>" . _AM_SOAPBOX_ARTCREATED . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SOAPBOX_ACTION . '</b></td>';
        echo '</tr>';

        // Put column names in an array, to avoid a query in the while loop farther ahead
        /* Code to show submitted articles */
        // Articles count
        //    function &getArticlesAllPermcheck(
        //         $limit=0, $start=0,
        //         $checkRight = true, $published = true, $submit = 0, $offline = 0, $block = null ,
        //         $sortname = 'datesub', $sortorder = 'DESC',
        //         $select_sbcolumns = null , $NOTarticleIDs = null ,
        //         $approve_submit = false ,
        //         $id_as_key = false )
        // Articles count
        $entrydataHandler = xoops_getModuleHandler('entrydata', $xoopsModule->dirname());
        //-------------------------------------
        $_entryob_arr = $entrydataHandler->getArticlesAllPermcheck((int)$xoopsModuleConfig['perpage'], $startsub, false, false, 1, null, null, $sortname, $sortorder, null, null, false);
        // Get number of articles in the selected condition ($cond)
        $numrows = $entrydataHandler->total_getArticlesAllPermcheck;

        if ($numrows > 0) { // That is, if there ARE unauthorized articles in the system
            foreach ($_entryob_arr as $_entryob) {
                //get vars
                //-------------------------------------
                $articles = $_entryob->toArray();
                //--------------------
                $colname = !empty($_entryob->_sbcolumns) ? $_entryob->_sbcolumns->getVar('name') : '';
                $created = $myts->htmlSpecialChars(formatTimestamp($datesub, $xoopsModuleConfig['dateformat']));
                $modify  = "<a href='submissions.php?op=mod&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/edit.png' ALT='" . _AM_SOAPBOX_EDITSUBM . "'></a>";
                $delete  = "<a href='submissions.php?op=del&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/delete.png' ALT='" . _AM_SOAPBOX_DELETESUBM . "'></a>";

                echo '<tr>';
                echo "<td class='head' align='center'>" . $articles['articleID'] . '</td>';
                echo "<td class='even' align='left'>" . $colname . '</td>';
                echo "<td class='even' align='left'>" . $articles['headline'] . '</td>';
                echo "<td class='even' align='center'>" . $created . '</td>';
                echo "<td class='even' align='center'>" . $modify . $delete . '</td>';
                echo '</tr>';
            }
        } else { // that is, $numrows = 0, there's no columns yet
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SOAPBOX_NOSUBMISSYET . '</td>';
            echo '</tr>';
        }
        echo "</table>\n";
        $pagenav = new XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startsub, 'startsub', '&sortname=' . $sortname . '&sortorder=' . $sortorder);
        echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
        echo "<br>\n";
    }

    //HACK bydomifara for add method

    /**
     * @return string
     */
    public static function getAcceptLang()
    {
        //---access language
        $al = 'en';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($accept_langs as $al) {
                $al     = strtolower($al);
                $al_len = strlen($al);
                if ($al_len > 2) {
                    if (preg_match('/([a-z]{2});q=[0-9.]+$/', $al, $al_match)) {
                        $al = $al_match[1];
                        break;
                    } else {
                        continue;
                    }
                }
            }
        }

        return $al;
    }
}
