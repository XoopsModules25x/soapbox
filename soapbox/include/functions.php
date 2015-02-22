<?php
// $Id: functions.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: functions.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */
if (!defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL")) {
    exit();
}

//TinyD spaw
global $xoopsModuleConfig, $xoopsModule;
if (is_object($xoopsModule) && $xoopsModule->dirname() == "soapbox" && !empty($xoopsModuleConfig)) {
    if ($xoopsModuleConfig['form_options'] == "spaw") {
        if (is_readable(XOOPS_ROOT_PATH . '/common/spaw/spaw_control.class.php')) {
            include_once XOOPS_ROOT_PATH . '/common/spaw/spaw_control.class.php';
        }
    }
}

/**
 * getLinkedUnameFromId()
 *
 * @param integer $userid Userid of author etc
 * @param integer $name   :  0 Use Usenamer 1 Use realname
 * @return
 **/
function getLinkedUnameFromId($userid = 0, $name = 0) {
    if (!is_numeric($userid)) {
        return $userid;
    }
    $myts   =& MyTextSanitizer::getInstance();
    $userid = intval($userid);
    if ($userid > 0) {
        $member_handler =& xoops_gethandler('member');
        $user           =& $member_handler->getUser($userid);

        if (is_object($user)) {
            $username  = $user->getVar('uname');
            $usernameu = $user->getVar('name');

            if (($name) && !empty($usernameu)) {
                $username = $user->getVar('name');
            }
            if (!empty($usernameu)) {
                $linkeduser
                    = $myts->htmlSpecialChars($usernameu) . " [<a href='" . XOOPS_URL . "/userinfo.php?uid=" . $userid
                    . "'>" . $myts->htmlSpecialChars($username) . "</a>]";
            } else {
//					$linkeduser = "<a href='".XOOPS_URL."/userinfo.php?uid=".$userid."'>". ucfirst($ts->htmlSpecialChars($username)) ."</a>";
                $linkeduser
                    =
                    "<a href='" . XOOPS_URL . "/userinfo.php?uid=" . $userid . "'>" . $myts->htmlSpecialChars($username)
                        . "</a>";
            }
            return $linkeduser;
        }
    }
    return $myts->htmlSpecialChars($GLOBALS['xoopsConfig']['anonymous']);
}

/*
function displayimage( $image = 'blank.gif', $path = '', $imgsource = '', $alttext = '' )
{
	global $xoopsConfig, $xoopsUser, $xoopsModule;
	$myts =& MyTextSanitizer::getInstance();
	$showimage = '';

	if ($path) {
		$showimage = "<a href='" . $myts->htmlSpecialChars(strip_tags($path)) . "'>";
	} 

	if (!is_dir(XOOPS_ROOT_PATH."/".$imgsource."/".$image) && file_exists(XOOPS_ROOT_PATH."/".$imgsource."/".$image)) {
		$showimage .= "<img src='".XOOPS_URL."/".$myts->htmlSpecialChars(strip_tags($imgsource))."/".$myts->htmlSpecialChars(strip_tags($image))."' border='0' alt=".$myts->htmlSpecialChars(strip_tags($alttext))." /></a>";
	} else {
		if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
			$showimage .= "<img src='".XOOPS_URL.'/modules/'.$xoopsModule->dirname()."/images/brokenimg.png' border='0' alt='"._AM_SB_ISADMINNOTICE."' /></a>";
		} else {
			$showimage .= "<img src='".XOOPS_URL.'/modules/'.$xoopsModule->dirname()."/images/blank.png' border='0' alt=".$myts->htmlSpecialChars(strip_tags($alttext))." /></a>";
		} 
	} 
	// clearstatcache();
	return $showimage;
} 
*/
function uploading(
    $allowed_mimetypes, $httppostfiles, $redirecturl = "index.php", $num = 0, $dir = "uploads", $redirect = 0
) {
    include_once XOOPS_ROOT_PATH . "/class/uploader.php";
    $myts =& MyTextSanitizer::getInstance();

    global $xoopsConfig, $xoopsModuleConfig, $_POST;

    $maxfilesize   = intval($xoopsModuleConfig['maxfilesize']);
    $maxfilewidth  = intval($xoopsModuleConfig['maximgwidth']);
    $maxfileheight = intval($xoopsModuleConfig['maximgheight']);
    $uploaddir     = XOOPS_ROOT_PATH . "/" . $myts->htmlSpecialChars(strip_tags($dir)) . "/";

    $uploader = new XoopsMediaUploader($uploaddir, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);

    if ($uploader->fetchMedia($myts->htmlSpecialChars(strip_tags($_POST['xoops_upload_file'][$num])))) {
        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();
            redirect_header($redirecturl, 1, $errors);
        } else {
            if ($redirect) {
                redirect_header($redirecturl, '1', "Image Uploaded");
            }
        }
    } else {
        $errors = $uploader->getErrors();
        redirect_header($redirecturl, 1, $errors);
    }
}

/*
function htmlarray( $thishtmlpage, $thepath )
{
    global $xoopsConfig, $wfsConfig;

    $file_array = filesarray( $thepath );

    echo "<select size='1' name='htmlpage'>";
    echo "<option value='-1'>------</option>";
    foreach( $file_array as $htmlpage )
    {
        if ( $htmlpage == $thishtmlpage )
        {
            $opt_selected = "selected='selected'";
        } 
        else
        {
            $opt_selected = "";
        } 
        echo "<option value='" . $htmlpage . "' $opt_selected>" . $htmlpage . "</option>";
    } 
    echo "</select>";
    return $htmlpage;
} 
*/
/*
function filesarray( $filearray )
{
    $files = array();
    $dir = opendir( $filearray );

    while ( ( $file = readdir( $dir ) ) !== false )
    {
        if ( ( !preg_match( "/^[.]{1,2}$/", $file ) && preg_match( "/[.htm|.html|.xhtml]$/i", $file ) && !is_dir( $file ) ) )
        {
            if ( strtolower( $file ) != 'cvs' && !is_dir( $file ) )
            {
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
function getuserForm($user) 
{
	global $xoopsDB, $xoopsConfig;
	$myts =& MyTextSanitizer::getInstance();

	echo "<select name='author'>";
	echo "<option value='-1'>------</option>";
	$result = $xoopsDB->query("SELECT uid, uname FROM ".$xoopsDB->prefix("users")." ORDER BY uname");

	while(list($uid, $uname) = $xoopsDB->fetchRow($result)) {
		if ( $uid == $user ){
			$opt_selected = "selected='selected'";
		} else {
			$opt_selected = "";
		}
		echo "<option value='".intval($uid)."' $opt_selected>".$myts->htmlSpecialChars($uname)."</option>";
	}
	echo "</select>";
}
*/

function getauthorName($author) {
    $ret = "";
    //get author
    $_authoruser_handler =& xoops_gethandler('user');
    $_authoruser         =& $_authoruser_handler->get($author);
    if (!is_object($_authoruser)) {
        $name3      = "";
        $uname3     = "";
        $authorname = "";
    } else {
        $name3      = $_authoruser->getVar('name');
        $uname3     = $_authoruser->getVar('uname');
        $authorname = $name3;
    }
    //-------------------------------------
    if (empty($authorname) || $authorname == "") {
        $ret = $uname3;
    } else {
        $ret = $authorname;
    }
    return $ret;
    //-------------------------------------
}

function showColumns($showCreate = 0) {
    global $xoopsGTicket, $pathIcon16;
    global $xoopsModuleConfig, $xoopsModule;
    $myts =& MyTextSanitizer::getInstance();
    include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
    include_once XOOPS_ROOT_PATH . "/class/xoopsform/grouppermform.php";
    include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/cleantags.php";
    $module_id = $xoopsModule->getVar('mid');
    $startcol  = isset($_GET['startcol']) ? intval($_GET['startcol']) : 0;

    /* Code to show existing columns */
    echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SB_SHOWCOLS . "</h3>";
    echo"<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SB_COLSTEXT
        . "</span>";

    if ($showCreate == 1) {
        echo
            "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='column.php'>"
            . _AM_SB_CREATECOL . "</a><br /><br />";
    }
    // To create existing columns table
    //----------------------------
    //get category object
    $_entrydata_handler =& xoops_getmodulehandler('entrydata', $xoopsModule->dirname());
    $numrows            = $_entrydata_handler->getColumnCount();
    $criteria           = new CriteriaCompo();
    $criteria->setSort('weight');
    $criteria->setLimit(intval($xoopsModuleConfig['perpage']));
    $criteria->setStart(intval($startcol));
    $_categoryob_arr =& $_entrydata_handler->getColumns($criteria);
    unset($criteria);
    if ($numrows > 0) {
        echo "<form action=\"column.php\" method=\"post\" name=\"reordercols\">";
    }
    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
    echo "<tr>";
	echo '<th class="txtcenter"><b>'._AM_SB_ID.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_WEIGHT.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_AUTHOR.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ARTCOLNAME.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_DESCRIP.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ACTION.'</b></td>';
    echo "</tr>";

    if ($numrows > 0) { // That is, if there ARE columns in the system
        //----------------------------
		$cont=0;
        foreach ($_categoryob_arr as $_categoryob) {
            //----------------------------
            //get vars
			$cont++;
            $category      = $_categoryob->toArray(); //all assign
            $category_vars = $_categoryob->getVars();
            foreach ($category_vars as $k=> $v) {
                ${$k} = $_categoryob->getVar($k);
            }
            //----------------------------

            $author = getLinkedUnameFromId($author, 0);
            $modify = "<a href='column.php?op=mod&columnID=" . $category['columnID'] . "'><img src='" . $pathIcon16 . "/edit.png' ALT='" . _AM_SB_EDITCOL . "'></a>";
            $delete = "<a href='column.php?op=del&columnID=" . $category['columnID'] . "'><img src='" . $pathIcon16 . "/delete.png' ALT='" . _AM_SB_DELETECOL . "'></a>";
			$style=(($cont%2)==0)?"even":"odd";
			echo '<tr class="'.$style.'">';
			echo '<td class="txtcenter">'.$category['columnID'].'</td>';
			echo '<td class="txtcenter"><input type="text" name="columnweight[' . $category['columnID'] . ']" value="'.$weight.'" size="3" maxlength="3" style="text-align: center;"></td>';
			echo '<td class="txtcenter">'. $category['author'] .'</td>';
			echo '<td class="txtcenter">'. $category['name'] .'</td>';
			echo '<td class="txtcenter">'. $category['description'] .'</td>';
			echo '<td class="txtcenter">'. $modify." ". $delete. '</td>';
            echo "</tr>";
        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo "<tr>";
        echo "<td class='head' align='center' colspan= '7'>" . _AM_SB_NOCOLS . "</td>";
        echo "</tr>";
        $category['columnID'] = '0';
    }
    echo "</table>\n";
    $pagenav = new XoopsPageNav($numrows, intval($xoopsModuleConfig['perpage']), $startcol, 'startcol', 'columnID=' . $category['columnID']);
    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
    echo "<br />\n";

    if ($numrows > 0) {
        echo "<input type='hidden' name='op' value='reorder' />";
        //--------------------
        echo $xoopsGTicket->getTicketHtml(__LINE__);
        //--------------------
        echo"<div style=\"margin-bottom: 18px;\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\"". _AM_SB_REORDERCOL . "\" /></div>";
        echo "</form>";
    }
}

function showArticles($showCreate = 0) {
    global $xoopsGTicket, $pathIcon16;
    global $xoopsModuleConfig, $xoopsModule;
    $myts =& MyTextSanitizer::getInstance();
    include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
    include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
    include_once XOOPS_ROOT_PATH . "/class/xoopsform/grouppermform.php";
    include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/cleantags.php";

    $module_id = $xoopsModule->getVar('mid');
    $startart  = isset($_GET['startart']) ? intval($_GET['startart']) : 0;
    if (isset($_POST['entries'])) {
        $entries = intval($_POST['entries']);
    } else {
        $entries = isset($_GET['entries']) ? intval($_GET['entries']) : 0;
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
    echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SB_SHOWARTS . "</h3>";
    echo"<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SB_ARTSTEXT
        . "</span>";

    if ($showCreate == 1) {
        echo
            "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='article.php'>"
            . _AM_SB_CREATEART . "</a><br /><br />";
    }
    // Articles count
    $_entrydata_handler =& xoops_getmodulehandler('entrydata', $xoopsModule->dirname());
    //----------------------------
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('submit', 0));
    $criteria->add(new Criteria('offline', 0));
    $tot_published = $_entrydata_handler->getArticleCount($criteria);
    unset($criteria);
    //----------------------------
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('submit', 0));
    $criteria->add(new Criteria('offline', 1));
    $tot_offline = $_entrydata_handler->getArticleCount($criteria);
    unset($criteria);
    //----------------------------
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('submit', 1));
    $tot_submitted = $_entrydata_handler->getArticleCount($criteria);
    unset($criteria);
    //----------------------------
    $tot_all = $_entrydata_handler->getArticleCount();
    //----------------------------
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('submit', 0));
    $tot_ok = $_entrydata_handler->getArticleCount($criteria);
    unset($criteria);
    //----------------------------

    // Prepare string for table head
    if ($entries == 0) {
        $string = _AM_SB_SHWALL;
    }
    if ($entries == 1) {
        $string = _AM_SB_SHWONL;
    }
    if ($entries == 2) {
        $string = _AM_SB_SHWOFF;
    }
    if ($entries == 3) {
        $string = _AM_SB_SHWSUB;
    }
    if ($entries == 4) {
        $string = _AM_SB_SHWAPV;
    }

    /* Code to show selected articles */
    echo"<form name='pick' id='pick' action='" . $myts->htmlSpecialChars(xoops_getenv('PHP_SELF'))
        . "' method='POST' style='margin: 0;'>";

    ?>
<table width='100%' cellspacing='1' cellpadding='2' border='0' style='border-left: 1px solid silver; border-top: 1px solid silver; border-right: 1px solid silver;'>
    <tr>
        <td class='odd'><span style='font-weight: bold; font-variant: small-caps;'><?php echo $string ?></span></td>
        <td class='odd' width='40%' align='right'><?php echo _AM_SB_SELECTSTATUS; ?>
            <select name='entries' onchange='submit()'>
                <option value='0'
                    <?php
                    if ($entries == 0) {
                        echo "selected='selected'";}
                    ?>>
                    <?php echo _AM_SB_SELALL; ?>
                    [<?php echo $tot_all; ?>]
                </option>
                <option value='1' <?php if ($entries == 1) {echo "selected='selected'";}
                    ?>><?php echo _AM_SB_SELONL; ?>
                    [<?php echo $tot_published; ?>]
                </option>
                <option value='2' <?php if ($entries == 2) {echo "selected='selected'";} ?>>
                    <?php echo _AM_SB_SELOFF; ?>
                    [<?php echo $tot_offline; ?>]
                </option>
                <option value='3' <?php if ($entries == 3) {echo "selected='selected'";} ?>>
                    <?php echo _AM_SB_SELSUB; ?>
                    [<?php echo $tot_submitted; ?>]
                </option>
                <option value='4' <?php if ($entries == 4) {echo "selected='selected'";} ?>><?php echo _AM_SB_SELAPV; ?>
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
    case 1 :
        $submit  = 0;
        $offline = 0;
        break;
    case 2 :
        //----------------------------
        $submit  = 0;
        $offline = 1;
        break;
    case 3 :
        //----------------------------
        $submit  = 1;
        $offline = NULL;
        break;
    case 4 :
        //----------------------------
        $submit = 0;
        break;
    case 0 :
    default:
        $submit  = NULL;
        $offline = NULL;
        break;
    }
//    function &getArticlesAllPermcheck(
//		 $limit=0, $start=0,
//		 $checkRight = true, $published = true, $submit = 0, $offline = 0, $block = null ,
//		 $sortname = 'datesub', $sortorder = 'DESC',
//		 $select_sbcolumns = null , $NOTarticleIDs = null ,
//		 $approve_submit = false ,
//		 $id_as_key = false )
    //-------------------------------------
    $_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
        intval($xoopsModuleConfig['perpage']), $startart, FALSE, FALSE, $submit, $offline, NULL, $sortname, $sortorder, NULL, NULL, FALSE, TRUE
    );
    // Get number of articles in the selected condition ($cond)
    $numrows = $_entrydata_handler->total_getArticlesAllPermcheck;
    if ($numrows > 0) {
        echo '<form action="article.php" method="post" name="reorderarticles\">';
    }
    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
	echo '<tr>';
	echo '<th class="txtcenter"><b>'._AM_SB_ARTID.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_WEIGHT.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ARTCOLNAME.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ARTHEADLINE.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ARTCREATED.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_STATUS.'</b></td>';
	echo '<th class="txtcenter"><b>'._AM_SB_ACTION.'</b></td>';
	echo '</tr>';

    if ($numrows > 0) { // That is, if there ARE articles in the said condition
        // Retrieve rows for those items

        $colarray = array();
		$cont=0;

        foreach ($_entryob_arr as $key=> $_entryob) {
            //get vars
			$cont++;
            //-------------------------------------
            $articles = $_entryob->toArray();
            //--------------------
            $colname = !empty($_entryob->_sbcolumns) ? $_entryob->_sbcolumns->getVar('name') : '';
            //--------------------
            $created = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $xoopsModuleConfig['dateformat']));
            $modify  = "<a href='article.php?op=mod&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16
                . "/edit.png' ALT='" . _AM_SB_EDITART . "'></a>";
            $delete  = "<a href='article.php?op=del&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16
                . "/delete.png' ALT='" . _AM_SB_DELETEART . "'></a>";



            //if ($offline == 0) {
            if ($articles['offline'] == 0) {
                $status = "<img src='" . $pathIcon16 . "/1.png' alt='" . _AM_SB_ARTISON . "'>";
            } else {
                //if ($offline == 1 && $submit == 0) {
                if ($articles['offline'] == 1 && $submit == 0) {
                    $status
                        = "<img src='" . $pathIcon16 . "/0.png' alt='" . _AM_SB_ARTISOFF . "'>";
                } else {
                    if ($submit == 1) {
                        $status
                            = "<img src=" . XOOPS_URL . "/modules/" . $xoopsModule->dirname()
                            . "/images/icon/sub.gif alt='" . _AM_SB_ARTISSUB . "'>";
                    }
                }
            }

//mb ----------------------------
//echo $cont.' - '.$offline.': '.$status.'</br>';

$style=(($cont%2)==0)?"even":"odd";
			echo '<tr class="'.$style.'">';
			echo '<td align="center"><a href="'.XOOPS_URL.'/modules/'.$xoopsModule->dirname().'/article.php?articleID='. $articles['articleID'] .'" title="'.$articles['headline'].'" target="_blank">'. $articles['articleID'] .'</a></td>';
			echo '<td class="txtcenter"><input type="text" name="articleweight['.$articles['articleID'].']" value="' . $articles['weight'] .'" size="3" maxlength="3" style="text-align: center;"></td>';
			echo '<td class="txtcenter">'. $colname .'</td>';
			echo '<td>'. $articles['headline'] .'</td>';
			echo '<td class="txtcenter">'. $created .'</td>';
			echo '<td class="txtcenter">'. $status .'</td>';
			echo '<td class="txtcenter">'. $modify . $delete .'</td>';
			echo '</tr>';


        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo "<tr>";
        echo "<td class='head' align='center' colspan= '7'>" . _AM_SB_NOARTS . "</td>";
        echo "</tr>";
    }
    echo "</table>\n";
    $pagenav = new XoopsPageNav($numrows, intval($xoopsModuleConfig['perpage']), $startart, 'startart',
        'entries=' . $entries . '&sortname=' . $sortname . '&sortorder=' . $sortorder);
    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

    if ($numrows > 0) {
        echo "<input type='hidden' name='op' value='reorder' />";
        //--------------------
        echo $xoopsGTicket->getTicketHtml(__LINE__);
        //--------------------
        echo "<div style=\"margin-bottom: 18px;\"><input type=\"submit\" name=\"submit\" class=\"formButton\" value=\"" . _AM_SB_REORDERART . "\" /></div>";
        echo "</form>";
    }
    echo "<br />\n";
}

function showSubmissions() {
    global $xoopsGTicket, $pathIcon16;
    global $xoopsModuleConfig, $xoopsModule;
    $myts =& MyTextSanitizer::getInstance();
    include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
    include_once XOOPS_ROOT_PATH . "/class/pagenav.php";
    include_once XOOPS_ROOT_PATH . "/class/xoopsform/grouppermform.php";
    include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->dirname() . "/include/cleantags.php";
    $module_id = $xoopsModule->getVar('mid');
    $startsub  = isset($_GET['startsub']) ? intval($_GET['startsub']) : 0;
    $datesub   = isset($_GET['datesub']) ? intval($_GET['datesub']) : 0;

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
    echo "<h3 style='color: #2F5376; margin: 0 0 4px 0;'>" . _AM_SB_SHOWSUBMISSIONS . "</h3>";
    echo "<span style=\"color: #567; margin: 3px 0 12px 0; font-size: small; display: block; \">" . _AM_SB_SUBTEXT . "</span>";
    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
    echo "<tr>";
    echo "<td width='40' class='bg3' align='center'><b>" . _AM_SB_ARTID . "</b></td>";
    echo "<td width='20%' class='bg3' align='center'><b>" . _AM_SB_ARTCOLNAME . "</b></td>";
    echo "<td width='45%' class='bg3' align='center'><b>" . _AM_SB_ARTHEADLINE . "</b></td>";
    echo "<td width='90' class='bg3' align='center'><b>" . _AM_SB_ARTCREATED . "</b></td>";
    echo "<td width='60' class='bg3' align='center'><b>" . _AM_SB_ACTION . "</b></td>";
    echo "</tr>";

    // Put column names in an array, to avoid a query in the while loop farther ahead
    /* Code to show submitted articles */
    // Articles count
//    function &getArticlesAllPermcheck(
//		 $limit=0, $start=0,
//		 $checkRight = true, $published = true, $submit = 0, $offline = 0, $block = null ,
//		 $sortname = 'datesub', $sortorder = 'DESC',
//		 $select_sbcolumns = null , $NOTarticleIDs = null ,
//		 $approve_submit = false ,
//		 $id_as_key = false )
    // Articles count
    $_entrydata_handler =& xoops_getmodulehandler('entrydata', $xoopsModule->dirname());
    //-------------------------------------
    $_entryob_arr =& $_entrydata_handler->getArticlesAllPermcheck(
        intval($xoopsModuleConfig['perpage']), $startsub, FALSE, FALSE, 1, NULL, NULL, $sortname, $sortorder, NULL, NULL, FALSE
    );
    // Get number of articles in the selected condition ($cond)
    $numrows = $_entrydata_handler->total_getArticlesAllPermcheck;

    if ($numrows > 0) { // That is, if there ARE unauthorized articles in the system
        foreach ($_entryob_arr as $_entryob) {
            //get vars
            //-------------------------------------
            $articles = $_entryob->toArray();
            //--------------------
            $colname = !empty($_entryob->_sbcolumns) ? $_entryob->_sbcolumns->getVar('name') : '';
            $created = $myts->htmlSpecialChars(formatTimestamp($datesub, $xoopsModuleConfig['dateformat']));
            $modify = "<a href='submissions.php?op=mod&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/edit.png' ALT='" . _AM_SB_EDITSUBM . "'></a>";
            $delete = "<a href='submissions.php?op=del&articleID=" . $articles['articleID'] . "'><img src='" . $pathIcon16 . "/delete.png' ALT='" . _AM_SB_DELETESUBM . "'></a>";

            echo "<tr>";
            echo "<td class='head' align='center'>" . $articles['articleID'] . "</td>";
            echo "<td class='even' align='left'>" . $colname . "</td>";
            echo "<td class='even' align='left'>" . $articles['headline'] . "</td>";
            echo "<td class='even' align='center'>" . $created . "</td>";
            echo "<td class='even' align='center'>" . $modify . $delete . "</td>";
            echo "</tr>";
        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo "<tr>";
        echo "<td class='head' align='center' colspan= '7'>" . _AM_SB_NOSUBMISSYET . "</td>";
        echo "</tr>";
    }
    echo "</table>\n";
    $pagenav = new XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startsub, 'startsub',
        '&sortname=' . $sortname . '&sortorder=' . $sortorder);
    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
    echo "<br />\n";
}

//HACK bydomifara for add method
function &soapbox_getWysiwygForm($soapbox_form, $caption, $name, $value = "", $width = '100%', $height = '400px') {
    $editor = FALSE;
    $x22    = FALSE;
    $xv     = str_replace('XOOPS ', '', XOOPS_VERSION);
    if (substr($xv, 2, 1) == '2') {
        $x22 = TRUE;
    }
    $editor_configs           = array();
    $editor_configs["name"]   = $name;
    $editor_configs["value"]  = $value;
    $editor_configs["rows"]   = 35;
    $editor_configs["cols"]   = 60;
    $editor_configs["width"]  = "100%";
    $editor_configs["height"] = "400px";
    //---access language
    $al = soapbox_getacceptlang();
    if ($al !== "en") {
        if (strtolower(_CHARSET) == "utf-8") {
            $al = $al . "-utf8";
        }
    }

    switch (strtolower($soapbox_form)) {
    case "spaw":
        if (is_readable(XOOPS_ROOT_PATH . '/common/spaw/spaw_control.class.php')) {
            $myts  =& MyTextSanitizer::getInstance();
            $value = str_replace("&amp;", "&", $myts->undoHtmlSpecialChars($value));
            $value = str_replace("<BR>", "<br />", $value);
            ob_start();
            $sw = new SPAW_Wysiwyg($name, $value, $al, '', '', $width, $height);
            $sw->show();
            $editor = new XoopsFormLabel($caption, ob_get_contents());
            ob_end_clean();
            $spaw_flag = TRUE;
        } else {
            if (!$x22) {
                if (is_readable(XOOPS_ROOT_PATH . "/class/spaw/formspaw.php")) {
                    $myts =& MyTextSanitizer::getInstance();
                    include_once(XOOPS_ROOT_PATH . "/class/spaw/formspaw.php");
                    $editor = new XoopsFormSpaw($caption, $name, $value);
                }
            } else {
                $editor = new XoopsFormEditor($caption, "spaw", $editor_configs);
            }
        }
        break;

    case "fck":
        if (!$x22) {
            if (is_readable(XOOPS_ROOT_PATH . "/class/fckeditor/formfckeditor.php")) {
                include_once(XOOPS_ROOT_PATH . "/class/fckeditor/formfckeditor.php");
                $editor = new XoopsFormFckeditor($caption, $name, $value);
            }
        } else {
            $editor = new XoopsFormEditor($caption, "fckeditor", $editor_configs);
        }
        break;

    case "htmlarea":
        if (!$x22) {
            if (is_readable(XOOPS_ROOT_PATH . "/class/htmlarea/formhtmlarea.php")) {
                include_once(XOOPS_ROOT_PATH . "/class/htmlarea/formhtmlarea.php");
                $editor = new XoopsFormHtmlarea($caption, $name, $value);
            }
        } else {
            $editor = new XoopsFormEditor($caption, "htmlarea", $editor_configs);
        }
        break;

    case "dhtml":
        if (!$x22) {
            $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, intval(intval($height) / 20), $width);
        } else {
            $editor = new XoopsFormEditor($caption, "dhtmltextarea", $editor_configs);
        }
        break;

    case "textarea":
        $editor = new XoopsFormTextArea($caption, $name, $value);
        break;

    case "koivi":
        if (!$x22) {
            if (is_readable(XOOPS_ROOT_PATH . "/class/wysiwyg/formwysiwygtextarea.php")) {
                include_once(XOOPS_ROOT_PATH . "/class/wysiwyg/formwysiwygtextarea.php");
                $editor = new XoopsFormWysiwygTextArea($caption, $name, $value, '100%', '400px', '');
            }
        } else {
            $editor = new XoopsFormEditor($caption, "koivi", $editor_configs);
        }
        break;
    default:
        include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $editor = new XoopsFormDhtmlTextArea($caption, $name, $value, intval(intval($height) / 20), $width);
        break;
    }

    return $editor;
}

//HACK bydomifara for add method
function soapbox_getacceptlang() {
    //---access language
    $al = "en";
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $accept_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach ($accept_langs as $al) {
            $al     = strtolower($al);
            $al_len = strlen($al);
            if ($al_len > 2) {
                if (preg_match("/([a-z]{2});q=[0-9.]+$/", $al, $al_match)) {
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

?>