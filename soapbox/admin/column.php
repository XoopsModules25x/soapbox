<?php
// $Id: column.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: column.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

/* General Stuff */
require( "admin_header.php" );
$indexAdmin = new ModuleAdmin();

$op = '';
if (isset($_GET['op'])) $op = trim(strip_tags( $myts->stripSlashesGPC($_GET['op']) ));
if (isset($_POST['op'])) $op = trim(strip_tags( $myts->stripSlashesGPC($_POST['op']) ));

$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());

function editcol($columnID = '')
{
	global $xoopsGTicket,$indexAdmin ;
	global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule,
	$xoopsLogger, $xoopsOption, $xoopsUserIsAdmin ; 
	$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
	$myts =& MyTextSanitizer::getInstance();

	include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
	$columnID = intval($columnID) ;
	$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());
	// If there is a parameter, and the id exists, retrieve data: we're editing a column
	if ( !empty($columnID) ) {
		//get category object
		$_categoryob =& $_entrydata_handler->getColumn($columnID);
		if (!is_object($_categoryob) ){
			redirect_header( "index.php", 1, _AM_SB_NOCOLTOEDIT );
			exit();
		}
		//get vars 
		$category_vars = $_categoryob->getVars() ;
		foreach ($category_vars as $k=>$v) {
		    $e_category[$k] = $_categoryob->getVar($k , 'E') ;
		}

		xoops_cp_header();
        echo $indexAdmin->addNavigation('column.php');
		//adminMenu(1, _AM_SB_COLS._AM_SB_EDITING . $_categoryob->getVar('name') . "'");
		//echo "<h3 style='color: #2F5376; '>"._AM_SB_ADMINCOLMNGMT."</h3>";

        //editcol(0);


		$sform = new XoopsThemeForm(_AM_SB_MODCOL.": " . $_categoryob->getVar('name') , "op", $myts->htmlSpecialChars(xoops_getenv( 'PHP_SELF' )));

	} else	{
		$_categoryob =& $_entrydata_handler->createColumn(true);
        $_categoryob->cleanVars() ;

		//get vars 
		$category_vars = $_categoryob->getVars() ;
		foreach ($category_vars as $k=>$v) {
		    $e_category[$k] = $_categoryob->getVar($k , 'E') ;
		}

		$e_category['weight'] = 1;
		$e_category['author'] = $xoopsUser -> uid() ;

		xoops_cp_header();
        echo $indexAdmin->addNavigation('column.php');
		//adminMenu(1, _AM_SB_COLS._AM_SB_CREATINGCOL);
//		echo "<h3 style='color: #2F5376; '>"._AM_SB_ADMINCOLMNGMT."</h3>";

        //editcol(0);


		$sform = new XoopsThemeForm(_AM_SB_NEWCOL, "op", $myts->htmlSpecialChars(xoops_getenv( 'PHP_SELF' )));

	} 

	$sform->setExtra('enctype="multipart/form-data"');
	$sform->addElement(new XoopsFormText(_AM_SB_COLNAME, 'name', 50, 80, $e_category['name'] ), true);

/*
	ob_start();
	getuserForm(intval($e_category['author']));
	$sform->addElement(new XoopsFormLabel(_AM_SB_AUTHOR, ob_get_contents()));
	ob_end_clean();
*/
	include_once XOOPS_ROOT_PATH . '/class/pagenav.php';

	$userstart = isset($_GET['userstart']) ? intval($_GET['userstart']) : 0;

    $member_handler =& xoops_gethandler('member');
    $usercount = $member_handler->getUserCount();
	// Selector to get author
	if (empty($e_category['author'])) {
		$authorid = $xoopsUser->uid();
		$authoruname = $xoopsUser->uname();
	} else {
		$author_ob =& $member_handler->getUser($e_category['author']) ;
		$authorid = $author_ob->uid();
		$authoruname = $author_ob->uname();
	}
    $criteria = new CriteriaCompo();
	$criteria->add(new Criteria( 'uid', $authorid , '!=' ) );
    $criteria->setSort('uname');
    $criteria->setOrder('ASC');
    $criteria->setLimit(199);
    $criteria->setStart($userstart);
    $user_list_arr = array( $authorid =>$authoruname) + $member_handler->getUserList($criteria) ;

    $nav = new XoopsPageNav($usercount, 200, $userstart, "userstart", $myts->htmlSpecialChars("op=mod&columnID=" . $columnID) );

    $user_select = new XoopsFormSelect('' , "author" , intval($authorid));
    $user_select->addOptionArray( $user_list_arr );
    $user_select_tray = new XoopsFormElementTray(_AM_SB_AUTHOR , "<br />");
    $user_select_tray->addElement($user_select);
    $user_select_nav = new XoopsFormLabel('', $nav->renderNav(4));
    $user_select_tray->addElement($user_select_nav);
	$sform->addElement($user_select_tray);

//HACK by domifara for Wysiwyg
	$sform->addElement(new XoopsFormTextArea(_AM_SB_COLDESCRIPT, 'description', $e_category['description'], 7, 60));
//	$editor=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _AM_SB_COLDESCRIPT, 'description',  $e_category['description'], '100%', '300px');
//	$sform->addElement($editor,true);

	$sform->addElement(new XoopsFormText(_AM_SB_COLPOSIT, 'weight', 4, 4, $e_category['weight']));

	// notification public
	$notifypub_radio = new XoopsFormRadioYN( _AM_SB_NOTIFY, 'notifypub', $e_category['notifypub'] , ' ' . _AM_SB_YES . '', ' ' . _AM_SB_NO . '' );
	$sform -> addElement( $notifypub_radio );

	if ( !isset($e_category['colimage']) || empty($e_category['colimage']) || $e_category['colimage'] == '' ) {
		$e_category['colimage'] = "nopicture.png";
	}
	$graph_array = & XoopsLists :: getImgListAsArray(XOOPS_ROOT_PATH ."/". $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) );
	$colimage_select = new XoopsFormSelect('', 'colimage', $e_category['colimage']);
	$colimage_select->addOptionArray($graph_array);
	$colimage_select->setExtra("onchange='showImgSelected(\"image3\", \"colimage\", \"". $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) ."\", \"\", \"".XOOPS_URL."\")'");
	$colimage_tray = new XoopsFormElementTray(_AM_SB_COLIMAGE, '&nbsp;');
	$colimage_tray->addElement($colimage_select);
	$colimage_tray->addElement(new XoopsFormLabel('', "<br /><br /><img src='".XOOPS_URL."/". $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) ."/".$e_category['colimage']."' name='image3' id='image3' alt='' />" ));
	$sform->addElement($colimage_tray);

	// Code to call the file browser to select an image to upload
	$sform->addElement(new XoopsFormFile(_AM_SB_COLIMAGEUPLOAD, 'cimage', intval($xoopsModuleConfig['maxfilesize']) ), false);

	$sform->addElement(new XoopsFormHidden('columnID', $e_category['columnID'] ));

	$button_tray = new XoopsFormElementTray('', '');
	$hidden = new XoopsFormHidden('op', 'addcol');
	$button_tray->addElement($hidden);

	// No ID for column -- then it's new column, button says 'Create'
    if ( empty($e_category['columnID']) ){
		$butt_create = new XoopsFormButton('', '', _AM_SB_CREATE, 'submit');
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
		$button_tray->addElement($butt_create);

		$butt_clear = new XoopsFormButton('', '', _AM_SB_CLEAR, 'reset');
		$button_tray->addElement($butt_clear);

		$butt_cancel = new XoopsFormButton('', '', _AM_SB_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);
	} else {		// button says 'Update'
		$butt_create = new XoopsFormButton('', '', _AM_SB_MODIFY, 'submit');
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');
		$button_tray->addElement($butt_create);

		$butt_cancel = new XoopsFormButton('', '', _AM_SB_CANCEL, 'button');
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement($butt_cancel);
	} 

	$sform->addElement($button_tray);
	//-----------
	$xoopsGTicket->addTicketXoopsFormElement( $sform , __LINE__  ) ;
	//-----------
	$sform->display();
	unset($hidden);
} 

switch ($op)
{
	case "mod":
		$columnID = isset($_POST['columnID']) ? intval($_POST['columnID']) : intval($_GET['columnID']);
		editcol($columnID);
		break;

	case "addcol":
		//-------------------------
		if ( ! $xoopsGTicket->check() ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		//-------------------------
		//articleID check
		if (!isset($_POST['columnID']) ) {
			redirect_header( "index.php", 1, _AM_SB_ARTNOTCREATED );
			exit();
		} else  {
			$columnID = intval($_POST['columnID']);
		}

		//get category object
		$_categoryob =& $_entrydata_handler->getColumn($columnID);
		//new data or edit
		if (!is_object($_categoryob) ) {
			$_categoryob =& $_entrydata_handler->createColumn(true);
	        $_categoryob->cleanVars() ;

			$_categoryob->setVar('created' , time() ) ;
		}	

		if ( isset($_POST['columnID']) ) { $_categoryob->setVar('columnID' , $columnID ) ; }
		if ( isset($_POST['name']) ) { $_categoryob->setVar('name' , $_POST['name'] ) ; }
		if ( isset($_POST['description']) ) { $_categoryob->setVar('description' , $_POST['description'] ) ; }

		if ( isset($_POST['weight']) ) { $_categoryob->setVar('weight' , intval($_POST['weight']) ) ; }
		if ( isset($_POST['notifypub']) ) { $_categoryob->setVar('notifypub' , intval($_POST['notifypub']) ) ; }

		if ( isset($_POST['author']) ) {
			if ($_POST['author'] == '-1' && isset($_POST['authorinput'])) {
				$author = intval($_POST['authorinput']);
			} else {
				$author = intval($_POST['author']);
			}
		} else {
			$author = $xoopsUser -> uid() ;
		}
		$_categoryob->setVar('author' , $author ) ;

		//-----------------
		//colimage
		if ( isset($_POST['colimage']) ) { $_categoryob->setVar('colimage' , $_POST['colimage'] ) ; }
		if ( isset($_FILES['cimage']['name']) ){
			$colimage_name = trim( strip_tags( $myts->stripSlashesGPC($_FILES['cimage']['name']) ) );
			if ( $colimage_name != "" ){
				if (file_exists(XOOPS_ROOT_PATH."/".$xoopsModuleConfig['sbuploaddir']."/".$colimage_name)){
					redirect_header("column.php", 1, _AM_SB_FILEEXISTS);
				} 
				$allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
				uploading($allowed_mimetypes, $colimage_name , "index.php", 0, $xoopsModuleConfig['sbuploaddir']);
				$_categoryob->setVar('colimage' , $colimage_name ) ;
			}
		}
		if ($_categoryob->getVar('colimage') == ''){
			$_categoryob->setVar('colimage' , 'blank.png' ) ;
		} 
		//-----------------

		// Save to database
		if ($_categoryob->_isNew) {
			if (!$_entrydata_handler->insertColumn($_categoryob)) {
				xoops_cp_header();
                echo $indexAdmin->addNavigation('column.php');
				print_r($_categoryob->getErrors()) ;
				xoops_cp_footer();
				exit();
				redirect_header( "index.php", 1, _AM_SB_NOTUPDATED );
			} else {
				//event trigger
				$_entrydata_handler->newColumnTriggerEvent($_categoryob ,'new_column');
				redirect_header("permissions.php", 1, _AM_SB_COLCREATED);
			}
		} else {
			if (!$_entrydata_handler->insertColumn($_categoryob)) {
				redirect_header( "index.php", 1, _AM_SB_NOTUPDATED );
			} else {
				redirect_header( "index.php", 1, _AM_SB_COLMODIFIED );
				exit();
			}
		}
		exit();
		break;

	case "del":

		$confirm = isset($_POST['confirm']) ? intval($_POST['confirm']) : 0;
	
		// confirmed, so delete 
		if ( $confirm == 1 ){
			//-------------------------
			if ( ! $xoopsGTicket->check() ) {
				redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
			}
			//-------------------------
			//columnID check
			if (!isset($_POST['columnID']) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			} else  {
				$columnID = intval($_POST['columnID']);
			}
			//get category object
			$_categoryob =& $_entrydata_handler->getColumn($columnID);
			if (!is_object($_categoryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			}
			//
			if (!$_entrydata_handler->deleteColumn($_categoryob) ){
				trigger_error ("ERROR:not deleted from database") ;
				exit() ;
			} else {
		
				$groups = ($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
				$module_id = $xoopsModule->getVar('mid');
				$gperm_handler = & xoops_gethandler('groupperm');

				$name = $myts->htmlSpecialChars($_categoryob->getVar('name') ) ;
				xoops_groupperm_deletebymoditem ($module_id, _AM_SB_COLPERMS, $columnID);
				redirect_header("index.php", 1, sprintf(_AM_SB_COLISDELETED, $name));
				exit();
			}
		} else {
			$columnID = (isset($_POST['columnID'])) ? intval($_POST['columnID']) : intval($_GET['columnID']);
			//get category object
			$_categoryob =& $_entrydata_handler->getColumn($columnID);
			if (!is_object($_categoryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			}
			$name = $myts->htmlSpecialChars($_categoryob->getVar('name') ) ;
			xoops_cp_header();
            echo $indexAdmin->addNavigation('column.php');
			xoops_confirm(array('op'=>'del', 'columnID'=>$columnID, 'confirm'=>1, 'name'=>$name) + $xoopsGTicket->getTicketArray( __LINE__ ), 'column.php', _AM_SB_DELETETHISCOL."<br /><br>".$name, _AM_SB_DELETE);
			xoops_cp_footer();
		} 
		exit();
		break;

	case "cancel":
		redirect_header("index.php", 1, sprintf(_AM_SB_BACK2IDX,''));
		exit();

	case "reorder":
		//-------------------------
		if ( ! $xoopsGTicket->check() ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		$_entrydata_handler->reorderColumnsUpdate($_POST['columnweight']);
		redirect_header("./column.php", 1, _AM_SB_ORDERUPDATED);
		exit();
		break;

	case "default":
	default:
    //echo $indexAdmin->addNavigation('column.php');
    editcol(0);
//    showColumns(0);

		break;
} 
include_once 'admin_footer.php';