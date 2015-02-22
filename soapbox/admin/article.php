<?php
// $Id: article.php,v 0.0.1 2005/10/27 20:30:00 domifara Exp $
/**
 * $Id: article.php v 1.5 23 August 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 23 August 2004
 * Author: hsalazar
 * Licence: GNU
 */

// -- General Stuff -- //
require( "admin_header.php" );
$indexAdmin = new ModuleAdmin();

$op = '';
if (isset($_GET['op'])) $op = trim(strip_tags( $myts->stripSlashesGPC($_GET['op']) ));
if (isset($_POST['op'])) $op = trim(strip_tags( $myts->stripSlashesGPC($_POST['op']) ));

$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());
$totalcats = $_entrydata_handler->getColumnCount();
if ( $totalcats == 0 ){
	redirect_header( "index.php", 1, _AM_SB_NEEDONECOLUMN );
	exit();
}

// -- Edit function -- //
function editarticle( $articleID = 0 ){
	global $xoopsGTicket,$indexAdmin ;
	global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule,
	$xoopsLogger, $xoopsOption, $xoopsUserIsAdmin ; 
	$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
	$myts =& MyTextSanitizer::getInstance();

	if (file_exists(XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php')) {
		include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/calendar.php';
	} else {
		include_once XOOPS_ROOT_PATH.'/language/english/calendar.php';
	}
	include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

	$articleID = intval($articleID);
	$_entrydata_handler =& xoops_getmodulehandler('entrydata',$xoopsModule->dirname());
	if (!empty($articleID)){
		//articleID check
		$_entryob =& $_entrydata_handler->getArticleOnePermcheck($articleID ,FALSE ,FALSE);
		if (!is_object($_entryob) ) {
			redirect_header( "index.php", 1, _AM_SB_NOARTS );
			exit();
		}

		//adminMenu(2, _AM_SB_ARTS._AM_SB_EDITING. $_entryob->getVar('headline') ."'");
		//echo "<h3 style='color: #2F5376; '>" . _AM_SB_ADMINARTMNGMT . "</h3>";
		$sform = new XoopsThemeForm( _AM_SB_MODART . ": " . $_entryob->getVar('headline') , "op",  $myts->htmlSpecialChars(xoops_getenv( 'PHP_SELF' )) );

	} else {
		//create new entry object
		$_entryob =& $_entrydata_handler->createArticle(TRUE);
        $_entryob->cleanVars() ;

		/**
		 *initial first variables before we start
		 */
		$columnID = 1;
		if ( isset($xoopsModuleConfig['form_options']) && $xoopsModuleConfig['form_options'] != 'dhtml' ) {
			$html = 1;
			$breaks = 0;
		}
		//adminMenu(2, _AM_SB_ARTS._AM_SB_CREATINGART);
		//echo "<h3 style='color: #2F5376; '>" . _AM_SB_ADMINARTMNGMT . "</h3>";
		$sform = new XoopsThemeForm( _AM_SB_NEWART, "op",  $myts->htmlSpecialChars(xoops_getenv( 'PHP_SELF' )) );
	} 

	//get vars mode E
	$entry_vars = $_entryob->getVars() ;
	foreach ($entry_vars as $k=>$v) {
	    $e_articles[$k] = $_entryob->getVar($k , 'E') ;
	}

	$sform -> setExtra( 'enctype="multipart/form-data"' );

// COLUMN
	/*
	* Get information for pulldown menu using XoopsTree.
	* First var is the database table
	* Second var is the unique field ID for the categories
	* Last one is not set as we do not have sub menus in WF-FAQ
	*/
	$_can_edit_categoryob_arr =& $_entrydata_handler->getColumns(NULL , TRUE);
	$collist = array();
	foreach ($_can_edit_categoryob_arr as $key => $_can_edit_categoryob) {
		$collist[$key] = $_can_edit_categoryob->getVar('name') ;
	}
    $col_select = new XoopsFormSelect('', 'columnID' ,intval( $e_articles['columnID'] ) );
    $col_select->addOptionArray($collist);
    $col_select_tray = new XoopsFormElementTray(_AM_SB_COLNAME, "<br />");
    $col_select_tray->addElement($col_select);
    $sform->addElement($col_select_tray);

// HEADLINE, LEAD, BODYTEXT
	// This part is common to edit/add
	$sform -> addElement( new XoopsFormText( _AM_SB_ARTHEADLINE, 'headline', 50, 50, $e_articles['headline'] ), TRUE );

// LEAD
//	$sform -> addElement( new XoopsFormTextArea( _AM_SB_ARTLEAD, 'lead', $lead, 5, 60 ) );
//	$editor_lead=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _AM_SB_ARTLEAD , 'lead' , $e_articles['lead'] , '100%', '200px');
//	$sform->addElement($editor_lead,TRUE);


    $editor_lead = new XoopsFormElementTray(_AM_SB_ARTLEAD, '<br />');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'lead';
        $options['value']  = $e_articles['lead'];
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '200px';
        $formmnote         = new XoopsFormEditor('', $xoopsModuleConfig['form_options'], $options, $nohtml = FALSE, $onfailure = 'textarea');
        $editor_lead->addElement($formmnote);
    } else {
        $formmnote = new XoopsFormDhtmlTextArea('', 'formmnote', $item->getVar('formmnote', 'e'), '100%', '100%');
        $editor_lead->addElement($formmnote);
    }
    $sform->addElement($editor_lead, FALSE);


// TEASER
	$sform -> addElement( new XoopsFormTextArea( _AM_SB_ARTTEASER, 'teaser', $e_articles['teaser'], 10, 120 ) );
//	$editor_teaser=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _AM_SB_ARTTEASER ,'teaser', $teaser , '100%', '120px');
//	$sform->addElement($editor_teaser,true);
//
	$autoteaser_radio = new XoopsFormRadioYN( _AM_SB_AUTOTEASER, 'autoteaser', 0, ' ' . _AM_SB_YES . '', ' ' . _AM_SB_NO . '' );
	$sform -> addElement( $autoteaser_radio );
	$sform -> addElement( new XoopsFormText( _AM_SB_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100 ) );

// BODY
//HACK by domifara for Wysiwyg
//	if (isset($xoopsModuleConfig['form_options']) ){
//		$editor=soapbox_getWysiwygForm($xoopsModuleConfig['form_options'] , _AM_SB_ARTBODY, 'bodytext', $e_articles['bodytext'], '100%', '400px');
//		$sform->addElement($editor,true);
//	} else {
//		$sform -> addElement( new XoopsFormDhtmlTextArea( _AM_SB_ARTBODY, 'bodytext', $e_articles['bodytext'], 20, 120 ) );
//	}

    $optionsTrayNote = new XoopsFormElementTray(_AM_SB_ARTBODY, '<br />');
    if (class_exists('XoopsFormEditor')) {
        $options['name']   = 'bodytext';
        $options['value']  = $e_articles['bodytext'];
        $options['rows']   = 5;
        $options['cols']   = '100%';
        $options['width']  = '100%';
        $options['height'] = '400px';
        $bodynote         = new XoopsFormEditor('', $xoopsModuleConfig['form_options'], $options, $nohtml = FALSE, $onfailure = 'textarea');
        $optionsTrayNote->addElement($bodynote);
    } else {
        $bodynote = new XoopsFormDhtmlTextArea('', 'formmnote', $item->getVar('formmnote', 'e'), '100%', '100%');
        $optionsTrayNote->addElement($bodynote);
    }
    $sform->addElement($optionsTrayNote, FALSE);


// IMAGE
	// The article CAN have its own image :)
	// First, if the article's image doesn't exist, set its value to the blank file
	if (!file_exists(XOOPS_ROOT_PATH . "/" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . "/" . $e_articles['artimage']) || empty($e_articles['artimage']) ) {
		$artimage = "blank.png";
	} 
	// Code to create the image selector
	$graph_array = & XoopsLists :: getImgListAsArray( XOOPS_ROOT_PATH . "/" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) );
	$artimage_select = new XoopsFormSelect( '', 'artimage', $e_articles['artimage'] );
	$artimage_select -> addOptionArray( $graph_array );
	$artimage_select -> setExtra( "onchange='showImgSelected(\"image5\", \"artimage\", \"" . $xoopsModuleConfig['sbuploaddir'] . "\", \"\", \"" . XOOPS_URL . "\")'" );
	$artimage_tray = new XoopsFormElementTray( _AM_SB_SELECT_IMG, '&nbsp;' );
	$artimage_tray -> addElement( $artimage_select );
	$artimage_tray -> addElement( new XoopsFormLabel( '', "<br /><br /><img src='" . XOOPS_URL . "/" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . "/" . $e_articles['artimage'] . "' name='image5' id='image5' alt='' />" ) );
	$sform -> addElement( $artimage_tray );

	// Code to call the file browser to select an image to upload
	$sform -> addElement( new XoopsFormFile( _AM_SB_UPLOADIMAGE, 'cimage', intval($xoopsModuleConfig['maxfilesize']) ), FALSE );

// WEIGHT
	$sform->addElement(new XoopsFormText(_AM_SB_WEIGHT, 'weight', 4, 4, $e_articles['weight']));
	//----------
	// datesub
	//----------
	//$datesub_caption = $myts->htmlSpecialChars( formatTimestamp( $e_articles['datesub'] , $xoopsModuleConfig['dateformat']) . "=>");
    //$datesub_tray = new XoopsFormDateTime( _AM_SB_POSTED.'<br />' . $datesub_caption ,'datesub' , 15, time()) ;
    $datesub_tray = new XoopsFormDateTime( _AM_SB_POSTED.'<br />' ,'datesub' , 15, $e_articles['datesub']) ;


	// you don't want to change datesub
//	$datesubnochage_checkbox = new XoopsFormCheckBox( _AM_SB_DATESUBNOCHANGE, 'datesubnochage', 0 );
//    $datesubnochage_checkbox->addOption(1, _AM_SB_YES);
//	$datesub_tray -> addElement( $datesubnochage_checkbox );
	$sform->addElement($datesub_tray);
	//-----------

// COMMENTS
	if (isset($GLOBALS['xoopsModuleConfig']['globaldisplaycomments']) && $GLOBALS['xoopsModuleConfig']['globaldisplaycomments'] == 1){
		// COMMENTS
		// Code to allow comments
		$addcommentable_radio = new XoopsFormRadioYN( _AM_SB_ALLOWCOMMENTS, 'commentable', $e_articles['commentable'], ' ' . _AM_SB_YES . '', ' ' . _AM_SB_NO . '' );
		$sform -> addElement( $addcommentable_radio );
	}	

	// OFFLINE
	// Code to take article offline, for maintenance purposes
	$offline_radio = new XoopsFormRadioYN(_AM_SB_SWITCHOFFLINE, 'offline', $e_articles['offline'] , ' '._AM_SB_YES.'', ' '._AM_SB_NO.'');
	$sform -> addElement($offline_radio);

	// ARTICLE IN BLOCK
	// Code to put article in block
	$block_radio = new XoopsFormRadioYN( _AM_SB_BLOCK, 'block', $e_articles['block']  , ' ' . _AM_SB_YES . '', ' ' . _AM_SB_NO . '' );
	$sform -> addElement( $block_radio );

	// notification public
	$notifypub_radio = new XoopsFormRadioYN( _AM_SB_NOTIFY, 'notifypub', $e_articles['notifypub'] , ' ' . _AM_SB_YES . '', ' ' . _AM_SB_NO . '' );
	$sform -> addElement( $notifypub_radio );

// VARIOUS OPTIONS
	//----------
	$options_tray = new XoopsFormElementTray(_AM_SB_OPTIONS,'<br />');

	$html_checkbox = new XoopsFormCheckBox( '', 'html', $e_articles['html'] );
	$html_checkbox -> addOption( 1, _AM_SB_DOHTML );
	$options_tray -> addElement( $html_checkbox );

	$smiley_checkbox = new XoopsFormCheckBox( '', 'smiley', $e_articles['smiley'] );
	$smiley_checkbox -> addOption( 1, _AM_SB_DOSMILEY );
	$options_tray -> addElement( $smiley_checkbox );

	$xcodes_checkbox = new XoopsFormCheckBox( '', 'xcodes', $e_articles['xcodes'] );
	$xcodes_checkbox -> addOption( 1, _AM_SB_DOXCODE );
	$options_tray -> addElement( $xcodes_checkbox );

	$breaks_checkbox = new XoopsFormCheckBox( '', 'breaks', $e_articles['breaks'] );
	$breaks_checkbox -> addOption( 1, _AM_SB_BREAKS );
	$options_tray -> addElement( $breaks_checkbox );

	$sform -> addElement( $options_tray );
	//----------

	$sform -> addElement( new XoopsFormHidden( 'articleID', $e_articles['articleID'] ) );

	$button_tray = new XoopsFormElementTray( '', '' );
	$hidden = new XoopsFormHidden( 'op', 'addart' );
	$button_tray -> addElement( $hidden );

	if ( !$e_articles['articleID'] ) // there's no articleID? Then it's a new article
		{
		$butt_create = new XoopsFormButton( '', '', _AM_SB_CREATE, 'submit' );
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');
		$button_tray->addElement( $butt_create );

		$butt_clear = new XoopsFormButton( '', '', _AM_SB_CLEAR, 'reset' );
		$button_tray->addElement( $butt_clear );

		$butt_cancel = new XoopsFormButton( '', '', _AM_SB_CANCEL, 'button' );
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement( $butt_cancel );
		} 
	else // else, we're editing an existing article
		{
		$butt_create = new XoopsFormButton( '', '', _AM_SB_MODIFY, 'submit' );
		$butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');
		$button_tray->addElement( $butt_create );

		$butt_cancel = new XoopsFormButton( '', '', _AM_SB_CANCEL, 'button' );
		$butt_cancel->setExtra('onclick="history.go(-1)"');
		$button_tray->addElement( $butt_cancel );
		}

	$sform -> addElement( $button_tray );
	//-----------
	$xoopsGTicket->addTicketXoopsFormElement( $sform , __LINE__  ) ;
	//-----------
	$sform -> display();
	unset( $hidden );
	} 


/* -- Available operations -- */
switch ( $op )
	{
    case "mod":
		xoops_cp_header();
        echo $indexAdmin->addNavigation('article.php');
		$articleID = isset( $_POST['articleID'] ) ? intval( $_POST['articleID'] ) : intval( $_GET['articleID'] );
		editarticle($articleID);
		break;

	case "addart":
		//-------------------------
		if ( ! $xoopsGTicket->check() ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		//-------------------------

		//articleID check
		if (!isset($_POST['articleID']) ) {
			redirect_header( "index.php", 1, _AM_SB_ARTNOTCREATED );
			exit();
		} else  {
			$articleID = intval($_POST['articleID']);
		}
		//articleID check
		if (!isset($_POST['columnID']) ) {
			redirect_header( "index.php", 1, _AM_SB_ARTNOTCREATED );
			exit();
		} else  {
			$columnID = intval($_POST['columnID']);
		}

		//get category object
		$_categoryob =& $_entrydata_handler->getColumn($columnID);
		if (!is_object($_categoryob) ) {
			redirect_header( "index.php", 1, _AM_SB_NEEDONECOLUMN );
			exit();
		}
	
		$_entryob =& $_entrydata_handler->getArticle($articleID);
		//new data or edit
		if (!is_object($_entryob) ) {
			$_entryob =& $_entrydata_handler->createArticle(true);
	        $_entryob->cleanVars() ;
		}	
		//set	

		// new data post uid
		if (is_object($xoopsUser) ) {
			$_entryob->setVar('uid' , $xoopsUser ->getVar('uid') );
		} else {
			//trigger_error ("Why:uid no mach") ;
			redirect_header( "index.php", 1, _AM_SB_ARTNOTCREATED );
			exit();
		}

		if ( isset($_POST['articleID']) ) { $_entryob->setVar('articleID' , $articleID ) ; }
		if ( isset($_POST['columnID']) ) { $_entryob->setVar('columnID' , $columnID ) ; }

		if ( isset($_POST['weight']) ) { $_entryob->setVar('weight' , intval($_POST['weight']) ) ; }

		if ( isset($_POST['commentable']) ) { $_entryob->setVar('commentable' , intval($_POST['commentable']) ) ; }
		if ( isset($_POST['block']) ) { $_entryob->setVar('block' , intval($_POST['block']) ) ; }
		if ( isset($_POST['offline']) ) { $_entryob->setVar('offline' , intval($_POST['offline']) ) ; }
		if ( isset($_POST['notifypub']) ) { $_entryob->setVar('notifypub' , intval($_POST['notifypub']) ) ; }

		if ( isset($_POST['breaks']) ) { $_entryob->setVar('breaks' , intval($_POST['breaks']) ) ; }
		if ( isset($_POST['html']) ) { $_entryob->setVar('html' , intval($_POST['html']) ) ; }
		if ( isset($_POST['smiley']) ) { $_entryob->setVar('smiley' , intval($_POST['smiley']) ) ; }
		if ( isset($_POST['xcodes']) ) { $_entryob->setVar('xcodes' , intval($_POST['xcodes']) ) ; }

		if ( isset($_POST['headline']) ) { $_entryob->setVar('headline' , $_POST['headline'] ) ; }
		if ( isset($_POST['lead']) ) { $_entryob->setVar('lead' , $_POST['lead'] ) ; }
		if ( isset($_POST['bodytext']) ) { $_entryob->setVar('bodytext' , $_POST['bodytext']) ; }
		if ( isset($_POST['votes']) ) { $_entryob->setVar('votes' , intval($_POST['votes']) ) ; }
		if ( isset($_POST['rating']) ) { $_entryob->setVar('rating' , intval($_POST['rating']) ) ; }


		if ( isset($_POST['teaser']) ) { $_entryob->setVar('teaser' , $_POST['teaser'] ) ; }

		$autoteaser = (isset($_POST['autoteaser'])) ? intval($_POST['autoteaser']) : 0;
		$charlength = (isset($_POST['teaseramount'])) ? intval($_POST['teaseramount']) : 0;
		if ( $autoteaser && $charlength ){
			$_entryob->setVar('teaser' , xoops_substr($_entryob->getVar('bodytext' , 'none'), 0, $charlength) ) ;
		}
			//datesub
			$datesubnochage = (isset($_POST['datesubnochage'])) ? intval($_POST['datesubnochage']) : 0;
			$datesub_date_sl = (isset($_POST['datesub'])) ? intval(strtotime($_POST['datesub']['date']))  : 0;
			$datesub_time_sl = (isset($_POST['datesub'])) ? intval($_POST['datesub']['time'])  : 0;
			$datesub = (isset($_POST['datesub'])) ? $datesub_date_sl + $datesub_time_sl  : 0;
			//if (!$datesub || $_entryob->_isNew){
            if (!$datesub){
				$_entryob->setVar('datesub' , time() ) ;
			} else {
				if (!$datesubnochage){
					$_entryob->setVar('datesub' , $datesub ) ;
				}
			}
		
		$_entryob->setVar('submit' , 0 ) ;

// ARTICLE IMAGE
		// Define variables
		$error = 0;
		$word = null;
		$uid = $xoopsUser -> uid();
		$submit = 1;
		$date = time();
		//-----------------
		//artimage
		if ( isset($_POST['artimage']) ) { $_entryob->setVar('artimage' , $_POST['artimage'] ) ; }
		if ( isset($_FILES['cimage']['name']) ){
			$artimage_name = trim( strip_tags( $myts->stripSlashesGPC($_FILES['cimage']['name']) ) );
			if ( $artimage_name != "" ){
				include_once XOOPS_ROOT_PATH . '/class/uploader.php';
				if ( file_exists( XOOPS_ROOT_PATH . "/" . $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) . "/" . $artimage_name ) ){
					redirect_header( "index.php", 1, _AM_SB_FILEEXISTS );
				} 
				$allowed_mimetypes = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/png' );
	
				uploading($allowed_mimetypes, $artimage_name, "index.php", 0, $myts->htmlSpecialChars($xoopsModuleConfig['sbuploaddir']) );
			
				$_entryob->setVar('artimage' , $artimage_name ) ;
			}
		}
		if ($_entryob->getVar('artimage') == ''){
			$_entryob->setVar('artimage' , 'blank.png' ) ;
		} 
		//-----------------


		// Save to database
		if ($_entryob->_isNew) {
			if (!$_entrydata_handler->insertArticle($_entryob)) {
					xoops_cp_header();
                    echo $indexAdmin->addNavigation('article.php');
					print_r($_entryob->getErrors()) ;
					xoops_cp_footer();
					exit();
				redirect_header( "index.php", 1, _AM_SB_ARTNOTCREATED );
			} else {
				// Notify of to admin only for approve
				$_entrydata_handler->newArticleTriggerEvent($_entryob , 'new_article' ) ;
				redirect_header( "index.php", 1, _AM_SB_ARTCREATEDOK );
				exit();
			}
		} else {
			if (!$_entrydata_handler->insertArticle($_entryob)) {
				redirect_header( "index.php", 1, _AM_SB_ARTNOTUPDATED );
			} else {
				$_entrydata_handler->newArticleTriggerEvent($_entryob , 'new_article' ) ;
				redirect_header( "index.php", 1, _AM_SB_ARTMODIFIED );
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
			//articleID check
			if (!isset($_POST['articleID']) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			} else  {
				$articleID = intval($_POST['articleID']);
			}
	
			$_entryob =& $_entrydata_handler->getArticle($articleID);
			if (!is_object($_entryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			}
			//
			if (!$_entrydata_handler->deleteArticle($_entryob) ){
				trigger_error ("ERROR:not deleted from database") ;
				exit() ;
			} else {
				$headline = $myts->htmlSpecialChars($_entryob->getVar('headline') ) ;
				redirect_header("index.php",1,sprintf( _AM_SB_ARTISDELETED, $headline ) );
				exit() ;
			}
		} else {
			$articleID = isset($_POST['articleID']) ? intval($_POST['articleID']) : intval($_GET['articleID']);
			$_entryob =& $_entrydata_handler->getArticle($articleID);
			if (!is_object($_entryob) ) {
				redirect_header( "index.php", 1, _NOPERM );
				exit();
			}
			$headline = $myts->htmlSpecialChars($_entryob->getVar('headline') ) ;
			xoops_cp_header();
            echo $indexAdmin->addNavigation('article.php');
			xoops_confirm( array( 'op' => 'del', 'articleID' => $articleID, 'confirm' => 1, 'headline' => $headline ) + $xoopsGTicket->getTicketArray( __LINE__ ) , 'article.php', _AM_SB_DELETETHISARTICLE . "<br /><br>" . $headline, _AM_SB_DELETE );
			xoops_cp_footer();
	
		}
		exit();
		break;
	case "reorder":
		//-------------------------
		if ( ! $xoopsGTicket->check() ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		$_entrydata_handler->reorderArticlesUpdate($_POST['articleweight']);
		redirect_header("index.php", 1, _AM_SB_ORDERUPDATED);
		break;

	case "default":
	default:
		xoops_cp_header();
        echo $indexAdmin->addNavigation('article.php');
		editarticle(0);
		//showArticles (0);
		break;

	} 
include_once 'admin_footer.php';