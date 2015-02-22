<?php
// $Id: entrydata.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
if($mydirname !== "soapbox" && $mydirname !== "" && ! preg_match( '/^(\D+)(\d*)$/' , $mydirname ) ) {
	echo ( "invalid dirname: " . htmlspecialchars( $this->mydirname ) ) ;
}
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbarticles.php';
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbcolumns.php';
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbvotedata.php';
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/entryget.php';

/**
* Soapbox entrydata handler class.
* This class provides simple interface (a facade class) for handling sbarticles/sbcolumns/sbvotedata
* entrydata.
*
*
* @author  domifara
* @package modules
*/

class SoapboxEntrydataHandler extends SoapboxEntrygetHandler {

    /**
     * constructor
     *
     */
    function __constructor(&$db)
    {
    	global $mydirname ;
        $this->_sbAHandler = new SoapboxSbarticlesHandler($db);
        $this->_sbCHandler = new SoapboxSbcolumnsHandler($db);
        $this->_sbVHandler = new SoapboxSbvotedataHandler($db);
        $_mymodule_handler =& xoops_gethandler('module');
        $_mymodule =& $_mymodule_handler->getByDirname('soapbox');
		if (!is_object($_mymodule)) { exit('not found dirname'); }
		$this->_module_dirname = $mydirname;
		$this->_module_id = $_mymodule -> getVar( 'mid' );
    }

    /**
     * create a new Article
     *
     * @return object SoapboxSbarticles reference to the new Article
     */
    function &createArticle($isNew = true)
    {
		$ret =& $this->_sbAHandler->create($isNew);
        return $ret ;
    }
    /**
     * create a new Column
     *
     * @return object SoapboxSbcolumns reference to the new Column
     */
    function &createColumn($isNew = true)
    {
        $ret =& $this->_sbCHandler->create($isNew);
        return $ret ;
    }
    /**
     * create a new Votedata
     *
     * @return object SoapboxSbvotedata reference to the new Votedata
     */
    function &createVotedata($isNew = true)
    {
        $ret =& $this->_sbVHandler->create($isNew);
        return $ret ;
    }

    /**
     * insert a new entry in the database
     * 
     * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insertArticle(&$sbarticle , $force = false)
    {

        if (!$this->_sbAHandler->insert($sbarticle , $force)) {
	        return false ;
        }
		// re count ----------------------------------
		$this->updateTotalByColumnID($sbarticle->getVar('columnID'));
		// re count ----------------------------------
        return true;
    }
    /**
     * insert a new Column in the database
     * 
     * @param object $sbcolumn reference to the {@link SoapboxSbcolumns} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insertColumn(&$sbcolumn , $force = false)
    {
        if (!$this->_sbCHandler->insert($sbcolumn, $force)) {
	        return false ;
        }
        return true;
    }
    /**
     * insert a new Votedata in the database
     * 
     * @param object $sbvotedata reference to the {@link SoapboxSbvotedata} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insertVotedata(&$sbvotedata , $force = false)
    {
        if (!$this->_sbVHandler->insert($sbvotedata, $force)) {
	        return false ;
        }
        return true;
    }

    /**
     * delete a Article from the database
     * 
     * @param object $sbarticle reference to the Article to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function deleteArticle(&$sbarticle , $force = false  , $re_count = true)
    {
		// delete Article
		if ( ! $this->_sbAHandler->delete($sbarticle , $force) ) {
            return false;
		}
		// delete Votedata
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'lid', $sbarticle->getVar('articleID') ) );
        $this->_sbVHandler->deleteEntrys($criteria , $force);
		unset($criteria);
		// delete comments
		xoops_comment_delete($this->_module_id , $sbarticle->getVar('articleID'));
 		// re count ----------------------------------
 		if ($re_count){
			$this->updateTotalByColumnID($sbarticle->getVar('columnID'));
 		}
		// re count ----------------------------------
       return true;
    }

    /**
     * delete a Column from the database
     * 
     * @param object $sbcolumn reference to the Column to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function deleteColumn(&$sbcolumn , $force = false)
    {
		// delete Column
		if ( ! $this->_sbCHandler->delete($sbcolumn , $force) ) {
            return false;
		}
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('columnID', $sbcolumn->getVar('columnID') ));
        $this->deleteArticlesEntrys($criteria , $force , false) ;
		unset($criteria);
        return true;
    }
    /**
     * delete a Votedata from the database
     * 
     * @param object $sbvotedata reference to the Votedata to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function deleteVotedata(&$sbvotedata , $force = false)
    {
		// delete Votedata
		if ( ! $this->_sbVHandler->delete($sbvotedata , $force) ) {
            return false;
		}
        return true;
    }

    /**
     * delete  entrys from the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be match
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function deleteArticlesEntrys($criteria = null, $force = false  , $re_count = false)
    {
		$_sbarticles_arr = $this->getArticles($criteria) ;
        if (empty($_sbarticles_arr) || count($_sbarticles_arr) == 0) {
            return false;
        }
		foreach ($_sbarticles_arr as $sbarticle) {
			$this->deleteArticle($sbarticle , $force , false) ;
		}
        return true;
    }


    /**
     *
     */
	function updateRating( &$sbarticle, $force = false ) // updates rating data in itemtable for a given item
	{
        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles') ) {
            return false;
        }
		$totalrating = 0.00;
		$votesDB = 0;
		$finalrating = 0;

		$sbvotedata_arr =& $this->getVotedatasByArticleID($sbarticle->getVar('articleID'), true, 0, 0) ;
		$votesDB =count($sbvotedata_arr);
        if (empty($sbvotedata_arr) || $votesDB == 0 ) {
			return false;
        }
		foreach ($sbvotedata_arr as $sbvotedata){
			if (is_object($sbvotedata)){
				$totalrating += $sbvotedata->getVar('rating');
			}
		}
		if ( ( $totalrating ) != 0 && $votesDB != 0 ){
			$finalrating = ($totalrating / $votesDB) + 0.00005;
			$finalrating = number_format( $finalrating, 4 );
		}
		//
		$sbarticle->setVar('rating' , $finalrating );
		$sbarticle->setVar('votes' , $votesDB );
        if (!$this->insertArticle($sbarticle , $force)) {
            return false;
        }
		return true;
	} 
    /**
     *
     */
    function updateTotalByColumnID($columnID , $force = false)
    {
		// re count ----------------------------------
		$sbcolumns =& $this->getColumn($columnID);
		if (is_object($sbcolumns) ){
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria( 'datesub', time() , '<' ) );
			$criteria->add(new Criteria( 'datesub', 0 , '>' ) );
			$criteria->add(new Criteria( 'submit', 0 ) );
			$criteria->add(new Criteria( 'offline', 0 ) );
			$sbcolumns->setVar('total' , $this->getArticleCount($criteria)) ;
			unset($criteria);
			$this->insertColumn($sbcolumns , $force);
		}
		// re count ----------------------------------
        return true;
    }
    /**
     * updates a single field in a Article record
     *
     * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateArticleByField(&$sbarticle, $fieldName, $fieldValue)
    {
		return $this->_sbAHandler->updateByField($sbarticle , $fieldName, $fieldValue);
    }
    /**
     * updates a single field in a Column record
     *
     * @param object $sbcolumns reference to the {@link SoapboxSbcolumns} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateColumnByField(&$sbcolumns, $fieldName, $fieldValue)
    {
		return $this->_sbCHandler->updateByField($sbcolumns , $fieldName, $fieldValue);
    }
    /**
     * updates a single field in a Votedata record
     *
     * @param object $user reference to the {@link SoapboxSbcolumns} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateVotedataByField(&$sbvotedata, $fieldName, $fieldValue)
    {
		return $this->_sbVHandler->updateByField($sbvotedata , $fieldName, $fieldValue);
    }

    /**
     * weight update
     * 
     * @param array $weight to match
     * @return bool FALSE if failed.
     */
	function reorderColumnsUpdate($weight)
	{
		if (!isset($weight) || empty($weight) || !is_array($weight)){
            return false;
		}	
		foreach ($weight as $columnID => $order) {
			$weight[$columnID] = intval($order);
		}
		array_unique($weight);
		foreach ($weight as $columnID => $order) {
			if ( isset($columnID) && is_numeric($columnID) && isset($order) ) {
				$sbcolumn =& $this->getColumn($columnID) ;
				if ( is_object($sbcolumn) ) {
					if ( !is_numeric ( $order ) or empty ( $order ) ) {
						 $order = 0;
					 }
					 $this->updateColumnByField($sbcolumn , "weight" , $order);
				}
			}
		}
         return true;
	}

    /**
     * weight update
     * 
     * @param array $weight to match
     * @return bool FALSE if failed.
     */
	function reorderArticlesUpdate($weight)
	{
		if (!isset($weight) || empty($weight) || !is_array($weight)){
            return false;
		}	
		foreach ($weight as $articleID => $order) {
			$weight[$articleID] = intval($order);
		}
		array_unique($weight);
		foreach ($weight as $articleID => $order) {
			if ( isset($articleID) && is_numeric($articleID) && isset($order) ) {
				$sbarticle =& $this->getArticle($articleID) ;
				if ( is_object($sbarticle) ) {
					if ( !is_numeric ( $order ) or empty ( $order ) ) {
						 $order = 0;
					 }
					 $this->updateArticleByField($sbarticle , "weight" , $order);
				}
			}
		}
         return true;
	}


    /**
     * newarticleTriggerEvent a category of this columnID from the database
     * 
     * @param object $sbcolumn reference to the category to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
     function newColumnTriggerEvent(&$sbcolumn , $events = 'new_column')
    {
        if (strtolower(get_class($sbcolumn)) != strtolower('SoapboxSbcolumns')) {
            return false;
        }
		if ( $sbcolumn->getVar('notifypub') != 1){
            return false;
        }
		// Notify of new link (anywhere) and new link in category
		$tags = array();
		$tags['COLUMN_NAME'] = $sbcolumn->getVar('name');
		$tags['COLUMN_URL'] = XOOPS_URL.'/modules/'. $this->_module_dirname .'/column.php?columnID=' . $sbcolumn->getVar('columnID');
		$notification_handler = & xoops_gethandler('notification');
		$notification_handler->triggerEvent('global', 0, 'new_column', $tags);
        return true;
    }
    /**
     * new articleTriggerEvent counter a new entry in the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be match
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
     function newArticleTriggerEvent(&$sbarticle , $events = 'new_article')
    {
        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles') ) {
            return false;
        }
		$sbcolumns =& $this->getColumn($sbarticle->getVar('columnID'));
		if (!is_object($sbcolumns) ){
            return false;
		}
		// Notify of new link (anywhere) and new link in category
		$tags = array();
		$tags['ARTICLE_NAME'] = $sbarticle->getVar('headline');
		$tags['ARTICLE_URL'] = XOOPS_URL . '/modules/' . $this->_module_dirname . '/article.php?articleID=' . $sbarticle->getVar('articleID');
		$tags['COLUMN_NAME'] = $sbcolumns->getVar('name');
		$tags['COLUMN_URL'] = XOOPS_URL . '/modules/' . $this->_module_dirname . '/column.php?columnID=' . $sbarticle->getVar('columnID');
		// Notify of to admin only for approve article_submit
		$tags['WAITINGSTORIES_URL'] = XOOPS_URL . '/modules/' . $this->_module_dirname . '/admin/submissions.php?op=col';
        $notification_handler = & xoops_gethandler( 'notification' );
		//approve evevt
		if ( $events == 'article_submit') {
			$notification_handler -> triggerEvent( 'global', 0, 'article_submit', $tags );
			$notification_handler -> triggerEvent( 'column', $sbarticle->getVar('columnID'), 'article_submit', $tags );
		} elseif ( $events == 'approve') {
			$notification_handler -> triggerEvent( 'article', $sbarticle->getVar('articleID'), 'approve', $tags );
		}
		//online
		if ( $sbarticle->getVar('offline') == 0 &&  $sbarticle->getVar('submit') == 0 ) {
			//when offline ,update offline changed to visible --> event
			if ( $sbarticle->getVar('notifypub') == 1  && $sbarticle->pre_offline == 1 ){
				$notification_handler -> triggerEvent( 'global', 0, 'new_article', $tags );
				$notification_handler -> triggerEvent( 'column', $sbarticle->getVar('columnID'), 'new_article', $tags );
			}
		} 
        return true;
    }

}

?>