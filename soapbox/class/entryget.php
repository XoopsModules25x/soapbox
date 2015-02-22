<?php
// $Id: entryget.php,v 0.0.1 2005/10/30 12:30:00 domifara Exp $
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
	echo ( "invalid dirname: " . htmlspecialchars( $mydirname ) ) ;
}

require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbarticles.php';
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbcolumns.php';
require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/class/sbvotedata.php';

require_once XOOPS_ROOT_PATH.'/modules/' . $mydirname . '/include/functions.php';

/**
* Soapbox entrydata handler class.
* This class provides simple interface (a facade class) for handling sbarticles/sbcolumns/sbvotedata
* entrydata.
*
*
* @author  domifara
* @package modules
*/

class SoapboxEntrygetHandler extends XoopsPersistableObjectHandler {

    /**#@+
    * holds reference to entry  handler(DAO) class
    * @access private
    */
    var $_sbaHandler;

    /**
    * holds reference to user handler(DAO) class
    */
    var $_sbCHandler;

    /**
    * holds reference to membership handler(DAO) class
    */
    var $_sbVHandler;

    /**
    * holds temporary module_id
    */
    var $_module_id ;
    var $_module_dirname ;
    /**#@-*/

	var $total_getArticlesAllPermcheck ; 
	var $total_getColumnsAllPermcheck ;

	var $total_getArticlesByColumnID; 
	var $total_getVotedatasByArticleID; 
	var $total_getColumnsByAuthor; 

    /**
     * constructor
     *
     */
    function SoapboxEntrygetHandler(&$db)
    {
		$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
		if($mydirname !== "soapbox" && $mydirname !== "" && ! preg_match( '/^(\D+)(\d*)$/' , $mydirname ) ) {
			echo ( "invalid dirname: " . htmlspecialchars( $mydirname ) ) ;
		}
        $this->_sbAHandler = new SoapboxSbarticlesHandler($db);
        $this->_sbCHandler = new SoapboxSbcolumnsHandler($db);
        $this->_sbVHandler = new SoapboxSbvotedataHandler($db);
        $_mymodule_handler =& xoops_gethandler('module');
        $_mymodule =& $_mymodule_handler->getByDirname($mydirname);
		if (!is_object($_mymodule)) { exit('not found dirname'); }
		$this->_module_dirname = $mydirname;
		$this->_module_id = $_mymodule -> getVar( 'mid' );
    }

    /**
     * retrieve a Article
     *
     * @param int $id ID for the Article
     * @return object SoapboxSbarticles reference to the Article
     */
    function &getArticle($id)
    {
         $ret =& $this->_sbAHandler->get($id);
        return $ret ;
    }
    /**
     * retrieve a Column
     *
     * @param int $id ID for the Article
     * @return object SoapboxSbarticles reference to the Column
     */
    function &getColumn($id)
    {
         $ret =& $this->_sbCHandler->get($id);
        return $ret ;
    }
    /**
     * retrieve a Votedata
     *
     * @param int $id ID for the Article
     * @return object SoapboxSbvotedata reference to the Votedata
     */
    function &getVotedata($id)
    {
         $ret =& $this->_sbVHandler->get($id);
        return $ret ;
    }
    /**
     * retrieve Articles from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the Article's ID as key for the array?
     * @return array array of {@link SoapboxSbarticles} objects
     */
    function &getArticles($criteria = null, $id_as_key = false)
    {
        $ret =& $this->_sbAHandler->getObjects($criteria, $id_as_key);
        return $ret ;
    }
    /**
     * retrieve Columns from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the Column's ID as key for the array?
     * @return array array of {@link SoapboxSbcolumns} objects
     */
    function &getColumns($criteria = null, $id_as_key = false)
    {
        $ret =& $this->_sbCHandler->getObjects($criteria, $id_as_key);
        return $ret ;
    }
    /**
     * retrieve Votedatas from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the Votedata's ID as key for the array?
     * @return array array of {@link SoapboxSbvotedata} objects
     */
    function &getVotedatas($criteria = null, $id_as_key = false)
    {
        $ret =& $this->_sbVHandler->getObjects($criteria, $id_as_key);
        return $ret ;
    }

    /**
     * count Article matching certain conditions
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return int
     */
    function getArticleCount($criteria = null)
    {
        return $this->_sbAHandler->getCount($criteria);
    }
    /**
     * count Column matching certain conditions
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return int
     */
    function getColumnCount($criteria = null)
    {
        return $this->_sbCHandler->getCount($criteria);
    }
    /**
     * count Votedata matching certain conditions
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return int
     */
    function getVotedataCount($criteria = null)
    {
        return $this->_sbVHandler->getCount($criteria);
    }

	/**
     * array return . from int or objects
     *
     * @param $sbcolumns is int , array or {@link SoapboxSbcolumns} objec
     * @return array ( int columnID's)
     */
    function getColumnsItemIDs( &$sbcolumns )
	{
    	$ret = array();
		$columnIDs = array() ;
		if (!isset($sbcolumns) || empty($sbcolumns)) {
		    return $ret;
		}
		if (is_object($sbcolumns)) {
	        if (strtolower(get_class($sbcolumns)) == strtolower('SoapboxSbcolumns')) {
				$columnIDs[] = $sbcolumns->getVar('columnID');
	        }
		} else {
			if (is_array($sbcolumns)) {
				if (count($sbcolumns) == 0) {
				    return $ret;
				}
				$sbcolumns = array_unique($sbcolumns);
				foreach ($sbcolumns as $k=>$v) {
					if (is_object($v)) {
				        if (strtolower(get_class($v)) == strtolower('SoapboxSbcolumns')) {
							$columnIDs[] = $v->getVar('columnID');
						}
			        } else{
						$columnIDs[] = intval($v) ;
					}
				}
			} else {
			    $columnIDs[] = intval($sbcolumns) ;
			}
		}
		$ret = array_unique($columnIDs);
	    return $ret;
	}

	/**
     * array return . from int or objects
     *
     * @param $sbarticles is int , array or {@link SoapboxSbarticles} objec
     * @return array ( int articleID's)
     */
    function getArticlesItemIDs( &$sbarticles )
	{
    	$ret = array();
		$articleIDs = array() ;
		if (!isset($sbarticles) || empty($sbarticles)) {
		    return $ret;
		}
		if (is_object($sbarticles)) {
	        if (strtolower(get_class($sbarticles)) == strtolower('SoapboxSbarticles')) {
				$articleIDs[] = $sbarticles->getVar('articleID');
	        }
		} else {
			if (is_array($sbarticles)) {
				if (count($sbarticles) == 0) {
				    return $ret;
				}
				$sbarticles = array_unique($sbarticles);
				foreach ($sbarticles as $k=>$v) {
					if (is_object($v)) {
				        if (strtolower(get_class($v)) == strtolower('SoapboxSbarticles')) {
							$articleIDs[] = $v->getVar('articleID');
						}
			        } else{
						$articleIDs[] = intval($v) ;
					}
				}
			} else {
			    $articleIDs[] = intval($sbarticles) ;
			}
		}
		$ret = array_unique($articleIDs);
	    return $ret;
	}
	/**
     * get sbcolumns objects with pemission check , sort 
     * 
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @param bool $checkRight true is with pemission check
     * @param string $sortname sort oder by filed name
     * @param string $sortorder sort oder by option (one filed name)
     * @param var $sbcolumns is select int columnID or array columnIDs ,object or objects
     * @param var $NOTsbcolumns is no select columnID or array columnIDs ,object or objects
     * @param bool $id_as_key use the Column's ID as key for the array?
      * @set int total count of entrys to total_getColumnsAllPermcheck
     * @return array array of {@link SoapboxSbcolumns} objects
     */
    function &getColumnsAllPermcheck(
		 $limit=0, $start=0,
		 $checkRight = true ,
		 $sortname = 'weight', $sortorder = 'ASC',
		 $sbcolumns = null , $NOTsbcolumns = null ,
		 $id_as_key = false )
	{
    	global $xoopsUser ;
    	$ret = array();
		$this->total_getColumnsAllPermcheck = 0;
		$groups = ( is_object($xoopsUser) ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
		$columnIDs = array() ;
		$notcolumnIDs = array() ;
		$can_read_columnIDs = array() ;
		//if obect -- change --> array
		if (isset($sbcolumns)) {
			$columnIDs = $this->getColumnsItemIDs($sbcolumns);
		}
		//if obect -- change --> array
		if (isset($NOTsbcolumns)) {
			$notcolumnIDs = $this->getColumnsItemIDs($NOTsbcolumns);
		}
		if ($checkRight) {
			$gperm_name = 'Column Permissions';
			$gperm_handler = & xoops_gethandler( 'groupperm' );
			$can_read_columnIDs = $gperm_handler->getItemIds( $gperm_name, $groups, $this->_module_id ) ;
		}
		//--------------------------
		$criteria = new CriteriaCompo();
		$criteria_used = false;
		if (!empty($columnIDs) && count($columnIDs) > 0 ) {
			$criteria->add(new Criteria( 'columnID',  '('.implode(',', array_unique($columnIDs)).')' , 'IN') );
			$criteria_used = true;
		}	
		if (!empty($notcolumnIDs) && count($notcolumnIDs) > 0 ) {
			$criteria->add(new Criteria( 'columnID',  '('.implode(',', array_unique($notcolumnIDs)).')' , 'NOT IN') );
			$criteria_used = true;
		}
		if ( !empty($can_read_columnIDs) && count($can_read_columnIDs) > 0 ) {
			$criteria->add(new Criteria( 'columnID',  '('.implode(',', array_unique($can_read_columnIDs)).')' , 'IN') );
			$criteria_used = true;
		}	
		//------ hold all count
		if ($criteria_used) {
			$this->total_getColumnsAllPermcheck = $this->getColumnCount($criteria) ;
		} else {
			$this->total_getColumnsAllPermcheck = $this->getColumnCount() ;
		}
		if ( empty($this->total_getColumnsAllPermcheck) ) {
		    return $ret;
		}
		if (isset($sortname) && trim($sortname) != '') {
				$criteria->setSort( $sortname ) ;
		}
		if (isset($sortorder) && trim($sortorder) != '' ) {
				$criteria->setOrder($sortorder) ;
		}
		$criteria->setLimit( intval($limit) ) ;
		$criteria->setStart( intval($start)) ;
		$ret =& $this->getColumns($criteria , $id_as_key);

		unset($criteria);

        return $ret;
		//-------------------------------------	
    }
	/**
     * get sbcolumns objects with pemission check , sort 
     * 
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @param bool $checkRight true is with pemission check
     * @param bool $published true is with datesub check
     * @param int $submit for submit check where submit = $submit
     * @param int $offline for offline check where offline = $offline
     * @param int $block for block check where block = $block
     * @param string $sortname sort oder by filed name
     * @param string $sortorder sort oder by option (one filed name)
     * @param var $select_sbcolumns is select int columnID or array columnIDs ,object or objects
     * @param var $NOTsbarticles is no select articleID or array articleIDs ,object or objects
     * @param bool $approve_submit with author articles of column non check else offline
     * @param bool $id_as_key use the articleID's ID as key for the array?
     * 
     * @set int total count of entrys to total_getArticlesAllPermcheck
     * @return array array of {@link SoapboxSbarticles} objects
     */
    function &getArticlesAllPermcheck(
		 $limit=0, $start=0,
		 $checkRight = true, $published = true, $submit = 0, $offline = 0, $block = null ,
		 $sortname = 'datesub', $sortorder = 'DESC',
		 $select_sbcolumns = null , $NOTsbarticles = null ,
		 $approve_submit = false ,
		 $id_as_key = false )
    {
    	global $xoopsUser ;
    	$ret = array();
		$this->total_getArticlesAllPermcheck = 0;
		//getColmuns
		$can_read_columnIDs = array() ;
		$can_read_column_authors = array() ;
		$NOTarticleIDs = array();
		if ( $checkRight ||  isset($select_sbcolumns) || $approve_submit ){
			//get category object
			$_sbcolumns_arr =& $this->getColumnsAllPermcheck(
																					0 , 0 ,
																					$checkRight ,
																					 null , null ,
																					$select_sbcolumns , null ,
																					true);
			if (empty($_sbcolumns_arr) || count($_sbcolumns_arr) == 0) {
			    return $ret;
			}	
			foreach ($_sbcolumns_arr as $key => $_sbcolumn) {
			    $can_read_columnIDs[] = $_sbcolumn->getVar('columnID') ;
			    if (is_object($xoopsUser)) {
					if ( $xoopsUser->isAdmin( $this->_module_id ) || $xoopsUser->getVar('uid') == $_sbcolumn->getVar('author') ) {
						$can_read_column_authors[] = $_sbcolumn->getVar('columnID') ;
					}
				}
			}
			if (empty($can_read_columnIDs) ) {
			    return $ret;
			}	
		} else {
			//get category object all
			$_sbcolumns_arr =& $this->getColumns( null ,true) ;
		}
		//getArticles
		$criteria = new CriteriaCompo();
		$criteria_used = false;
		$criteria_public = new CriteriaCompo();
		$criteria_public_used = false;
		if ( count($can_read_columnIDs) > 0 ) {
			$criteria_public->add(new Criteria( 'columnID',  '('.implode(',', array_unique($can_read_columnIDs)).')' , 'IN') );
			$criteria_public_used = true;
		}
		if ( $checkRight){
			if ($published) {
				$criteria_public->add(new Criteria( 'datesub', time() , '<' ) );
				$criteria_public->add(new Criteria( 'datesub', 0 , '>' ) );
				$criteria_public_used = true;
			}
		}
		//------
		if (isset($submit)) {
			$criteria_public->add(new Criteria( 'submit', intval($submit) ) );
			$criteria_public_used = true;
		}
		if (isset($offline)) {
			$criteria_public->add(new Criteria( 'offline', intval($offline) ) );
			$criteria_public_used = true;
		}
		if (isset($block)) {
			$criteria_public->add(new Criteria( 'block', intval($block) ) );
			$criteria_public_used = true;
		}
		if (isset($NOTsbarticles)) {
			$notarticleIDs = $this->getColumnsItemIDs($NOTsbarticles);
			$criteria_public->add(new Criteria( 'articleID',  '('.implode(',', array_unique($notarticleIDs)).')' , 'NOT IN') );
			$criteria_public_used = true;
		}

		if ($criteria_public_used) {
			$criteria = new CriteriaCompo($criteria_public);
			$criteria_used = true;
		}
		unset($criteria_public);

		// approve submit for column_authors
		if ($approve_submit &&  count($can_read_column_authors) > 0  ) {
			$crit_approve_submit = new CriteriaCompo();
			$crit_approve_submit->add(new Criteria( 'columnID',  '('.implode(',', array_unique($can_read_column_authors)).')' , 'IN') );
			if (isset($NOTsbarticles)) {
				$notarticleIDs = $this->getColumnsItemIDs($NOTsbarticles);
				if ( count($notarticleIDs) > 0 ) {
					$crit_approve_submit->add(new Criteria( 'articleID',  '('.implode(',', array_unique($notarticleIDs)).')' , 'NOT IN') );
				}
			}
//			$crit_approve_submit->add(new Criteria( 'submit', 1 ));
			$crit_approve_submit->add(new Criteria( 'offline', 0 ));
			$criteria->add($crit_approve_submit, 'OR');
			$criteria_used = true;
			unset($crit_approve_submit);
		}
		//------
		if ($criteria_public_used) {
			$this->total_getArticlesAllPermcheck = $this->getArticleCount($criteria);
		} else{
			$this->total_getArticlesAllPermcheck = $this->getArticleCount();
		}
		if (empty($this->total_getArticlesAllPermcheck)) {
		    return $ret;
		} else {
			if (isset($sortname) && trim($sortname) != '' ) {
					$criteria->setSort( $sortname ) ;
			}
			if (isset($sortorder) && trim($sortorder) != '' ) {
					$criteria->setOrder($sortorder) ;
			}
			$criteria->setLimit( intval($limit) ) ;
			$criteria->setStart( intval($start)) ;
			$sbarticle_arr =& $this->getArticles($criteria , $id_as_key);
			foreach ($sbarticle_arr as $k=>$sbarticle) {
				$sbarticle_arr[$k]->_sbcolumns =& $_sbcolumns_arr[$sbarticle->getVar('columnID')];
			}
		}
		unset($criteria);

        return $sbarticle_arr;
		//-------------------------------------	
 
    }

    /**
     * get object with check Perm a entry
     * 
     * @param int $articleID articleID of the entry
     * @param bool $force
     * @return mixed reference to the {@link SoapboxSbarticles} object, FALSE if failed
     */
    function &getArticleOnePermcheck( $id , $checkRight = true, $approve_submit = false )
    {
		global $xoopsUser ;
		$ret = false;
		$sbarticle =& $this->getArticle($id);
	    if (!is_object($sbarticle)) {
            return $ret;
	    }
//		$gperm_name = 'Column Permissions';
//		$groups = ( is_object($xoopsUser) ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
//		$gperm_handler = & xoops_gethandler( 'groupperm' );
//		if ( !$gperm_handler -> checkRight( $gperm_name,$sbarticle->getVar('columnID'), $groups, $this->_module_id ) ){
//            return $ret;
//		}
			//get category object
			$_sbcolumns_arr =& $this->getColumnsAllPermcheck(
																					1 , 0 ,
																					$checkRight ,
																					 null , null ,
																					$sbarticle->getVar('columnID') , null ,
																					true);
		if ($checkRight) {
			if (empty($_sbcolumns_arr) || count($_sbcolumns_arr) == 0) {
			    return $ret;
			}
			$sbarticle->_sbcolumns =& $_sbcolumns_arr[$sbarticle->getVar('columnID')];
			if ($sbarticle->getVar('offline') != 0) {
	            return $ret;
			}
		    if (is_object($xoopsUser)) {
				if ( $approve_submit) {
					if ( $xoopsUser->isAdmin( $this->_module_id) ||  $xoopsUser->getVar('uid') == $sbarticle->_sbcolumns->getVar('author') ) {
					    //true
						$ret =& $sbarticle ;
						return $ret;
					}
				}		
		    }
			if ($sbarticle->getVar('datesub') == 0) {
	            return $ret;
			}
			if ($sbarticle->getVar('datesub') > time()) {
	            return $ret;
			}
			if ($sbarticle->getVar('submit') != 0) {
	            return $ret;
			}
		}
	    //true
		$ret =& $sbarticle ;
		return $ret;
    }

//----------------------------------------------------------------------

    /**
     * get a list of Articles belonging to a column
     *
     * @param int $columnID ID of the Column
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     * @param int $start index of the first user to return
     * @return array Array of {@link SoapboxSbarticles} objects (if $asobject is TRUE)
     * or of associative arrays matching the record structure in the database.
     */
    function &getArticlesByColumnID($columnID, $asobject = false, $limit = 0, $start = 0 , $sortname = null , $sortorder = null)
    {
        $ret = array();
		$this->total_getArticlesByColumnID = 0;
		
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'columnID', $columnID  ) );
		$this->total_getArticlesByColumnID = $this->getArticleCount($criteria);
		if (empty($this->total_getArticlesByColumnID ) || $this->total_getArticlesByColumnID == 0) {
		    return $ret;
		}
		if (isset($sortname) && trim($sortname) != '' ) {
			$criteria->setSort( $sortname ) ;
		}
		if (isset($sortorder) && trim($sortorder) != '' ) {
			$criteria->setOrder($sortorder) ;
		}
		$criteria->setLimit( intval($limit) ) ;
		$criteria->setStart( intval($start)) ;
		$sbarticle_arr =& $this->getArticles($criteria , true);
		unset($criteria);
		if (empty($sbarticle_arr) || count($sbarticle_arr) == 0) {
		    return $ret;
		}
		if ($asobject) {
			$ret =& $sbarticle_arr;
		} else {
			foreach ($sbarticle_arr as $key => $sbarticle) {
						$ret[] = $key;
			}
		}
		return $ret;
    }
    /**
     * get a list of Votedatas belonging to a Article
     *
     * @param int $articleID ID of the Article
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     * @param int $start index of the first user to return
     * @return array Array of {@link SoapboxSbvotedata} objects (if $asobject is TRUE)
     * or of associative arrays matching the record structure in the database.
     */
    function &getVotedatasByArticleID($articleID, $asobject = false, $limit = 0, $start = 0 , $sortname = null , $sortorder = null)
    {
        $ret = array();
		$this->total_getVotedatasByArticleID = 0;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'lid', $articleID ) );
		$this->total_getVotedatasByArticleID = $this->getVotedataCount($criteria);
		if (empty($this->total_getVotedatasByArticleID ) || $this->total_getVotedatasByArticleID == 0) {
		    return $ret;
		}
		if (isset($sortname) && trim($sortname) != '' ) {
			$criteria->setSort( $sortname ) ;
		}
		if (isset($sortorder) && trim($sortorder) != '' ) {
			$criteria->setOrder($sortorder) ;
		}
		$criteria->setLimit( intval($limit) ) ;
		$criteria->setStart( intval($start)) ;
		$sbvotedata_arr =& $this->getVotedatas($criteria , true);
		unset($criteria);
		if (empty($sbvotedata_arr) || count($sbvotedata_arr) == 0) {
		    return $ret;
		}
		if ($asobject) {
			$ret =& $sbvotedata_arr;
		} else {
			foreach ($sbvotedata_arrr as $key => $sbvotedata) {
						$ret[] = $key;
			}
		}
		return $ret;
    }

    /**
     * get a list of columns that a user is author of
     *
     * @param int $user_id ID of the user
     * @param bool $asobject return groups as {@link SoapboxSbcolumns} objects or arrays?
     * @return array array of objects or arrays
     */
    function &getColumnsByAuthor($user_id, $asobject = false, $limit = 0, $start = 0 , $sortname = null , $sortorder = null)
    {
        $ret = array();
		$this->total_getColumnsByAuthor = 0;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'author', $user_id ) );
		$this->total_getColumnsByAuthor = $this->getColumnCount($criteria);
		if (empty($this->total_getColumnsByAuthor ) || $this->total_getColumnsByAuthor == 0) {
		    return $ret;
		}
		if (isset($sortname) && trim($sortname) != '' ) {
			$criteria->setSort( $sortname ) ;
		}
		if (isset($sortorder) && trim($sortorder) != '' ) {
			$criteria->setOrder($sortorder) ;
		}
		$criteria->setLimit( intval($limit) ) ;
		$criteria->setStart( intval($start)) ;
		$sbcolumns_arr =& $this->getColumns($criteria ,true);
		unset($criteria);
		if (empty($sbcolumns_arr) || count($sbcolumns_arr) == 0) {
		    return $ret;
		}
		if ($asobject) {
			$ret =& $sbcolumns_arr;
		} else {
			foreach ($sbcolumns_arr as $key => $sbcolumns) {
				$ret[] = $key;
			}
		}
		return $ret;
    }
    /**
     * get a list of columns that article objects
     *
	 * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param bool $id_as_key use the columnID as key for the array?
     * @return array array of {@link SoapboxSbcolumns} objects
     */
	function &getColumnsByArticles( &$_sbarticle_arr, $asobject = false, $limit = 0, $start = 0 , $sortname = null , $sortorder = null)
    {
        $ret = array();
		$columnIDs = array();
		if (is_array($_sbarticle_arr)) {
			foreach ($_sbarticle_arr as $sbarticle){
		        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles') ) {
				    $columnIDs[] = $sbarticle->getVar('columnID') ;
		        }
			}
		} else {
		        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles') ) {
				    $columnIDs[] = $_sbarticle_arr->getVar('columnID') ;
		        }
		}
		if (!empty($columnIDs) && count($columnIDs) > 0 ) {
		    return $ret;
		}	
		$columnIDs = array_unique($columnIDs);

		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'columnID',  '('.implode(',', array_unique($columnIDs)).')' , 'IN') );
		if (isset($sortname) && trim($sortname) != '' ) {
			$criteria->setSort( $sortname ) ;
		}
		if (isset($sortorder) && trim($sortorder) != '' ) {
			$criteria->setOrder($sortorder) ;
		}
		$criteria->setLimit( intval($limit) ) ;
		$criteria->setStart( intval($start)) ;
		$sbcolumns_arr =& $this->getColumns($criteria , true);
		unset($criteria);
		if (empty($sbcolumns_arr) || count($sbcolumns_arr) == 0) {
		    return $ret;
		}
		if ($asobject) {
			$ret =& $sbcolumns_arr;
		} else {
			foreach ($sbcolumns_arr as $key => $sbcolumns) {
				$ret[] = $key;
			}
		}
		return $ret;
    }

    /**
     * count Votedata belonging to a articleID
     *
     * @param int $articleID ID of the group
     * @return int
     */
    function getVotedataCountByArticleID($articleID)
    {
 		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'lid', $articleID ) );
       return $this->getVotedataCount($criteria);
    }
    /**
     * count Article belonging to a columnID
     *
     * @param int $articleID ID of the group
     * @return int
     */
    function getArticleCountByColumnID($columnID)
    {
 		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'columnID', $columnID ) );
       return $this->getArticleCount($criteria);
    }

    /**
     * update count up hit's counter of sbarticles obects ,with author user check
     *
	 * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE
     */
	function getUpArticlecount( &$sbarticle, $force = false )
	{
		global $xoopsUser , $xoopsModuleConfig;
		
        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles') ) {
            return false;
        }
		$groups = ( is_object($xoopsUser) ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
		//update count
		$hitcount_update = false ;
		if ( $groups == XOOPS_GROUP_ANONYMOUS ) {
			$hitcount_update = true ;
		} else {
			if ( $xoopsModuleConfig['adminhits'] == 1 ){
				$hitcount_update = true ;
			} else {
				if ( $xoopsUser->isAdmin($this->_module_id ) ){
					$hitcount_update = false ;
				} else {
					if ( $sbarticle->getVar('uid') != $xoopsUser->uid() ) {
						$hitcount_update = true ;
					} else {
						$hitcount_update = false ;
					}
				}
			}
		}
		if ( $hitcount_update ) {
			$hitcount = $sbarticle->getVar('counter') + 1 ;
			return $this->_sbAHandler->updateByField($sbarticle , "counter" , $hitcount , $force);
		}
	    return false;
	} 

	
    /**
     * get edit icon display html layout for admin or author 
     *
	 * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
	 * @param object $sbcolumns reference to the {@link SoapboxSbcolumns} object
     * @return string (html tags)
     */
	function getadminlinks( &$sbarticle , &$sbcolumns )
	{
		global $xoopsUser, $xoopsModule;
        $pathIcon16 = $xoopsModule->getInfo('icons16');
		$myts = & MyTextSanitizer :: getInstance();
		// Functional links
		$ret = '';
		if ( is_object($xoopsUser) ) {
			if ( $xoopsUser->isAdmin($this->_module_id)) {
				if ($sbarticle->getVar('submit') != 0){
					$ret = "<a href=\"".XOOPS_URL.'/modules/'.$this->_module_dirname."/admin/submissions.php?op=mod&articleID=".$sbarticle->getVar('articleID')."\" target=\"_blank\">"
										."<img src='" . $pathIcon16."/edit.png' border=\"0\" alt=\""._MD_SB_EDITART."\" width=\"16\" height=\"16\">"
										."</a>&nbsp;" ;
				} else {
					$ret = "<a href=\"".XOOPS_URL.'/modules/'.$this->_module_dirname."/admin/article.php?op=mod&articleID=".$sbarticle->getVar('articleID')."\" target=\"_blank\">"
										."<img src='" . $pathIcon16."/edit.png' border=\"0\" alt=\""._MD_SB_EDITART."\" width=\"16\" height=\"16\">"
										."</a>&nbsp;" ;
				}
					$ret .= "<a href=\"".XOOPS_URL.'/modules/'.$this->_module_dirname."/admin/article.php?op=del&articleID=".$sbarticle->getVar('articleID')."\" target=\"_blank\">"
												."<img src='" . $pathIcon16."/delete.png' border=\"0\" alt=\""._MD_SB_DELART."\" width=\"16\" height=\"16\">"
												."</a>&nbsp;";
			} elseif ($xoopsUser->uid() == $sbcolumns->getVar('author') ) {
				$ret = "<a href=\"".XOOPS_URL.'/modules/'.$this->_module_dirname."/submit.php?op=edit&articleID=".$sbarticle->getVar('articleID')."\" target=\"_blank\">"
										."<img src='" . $pathIcon16."/edit.png' border=\"0\" alt=\""._MD_SB_EDITART."\" width=\"16\" height=\"16\">"
										."</a>&nbsp;";
			} else {
				$ret = '';
			}
		}	
		return $ret;
	}	
    /**
     * get print ,mail icon html layout for guest or nomal user
     *
	 * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @return string (html tags)
     */
	function getuserlinks( &$sbarticle )
	{
		global $xoopsConfig, $xoopsModule;
          $pathIcon16 = $xoopsModule->getInfo('icons16');

		$myts = & MyTextSanitizer :: getInstance();
		// Functional links
		$ret = '';
		$mbmail_subject =sprintf(_MD_SB_INTART,$xoopsConfig['sitename']) ;
		$mbmail_body =sprintf(_MD_SB_INTARTFOUND, $xoopsConfig['sitename']) ;
		$al = soapbox_getacceptlang();
		if ($al == "ja") {
			if (function_exists('mb_convert_encoding') && function_exists('mb_encode_mimeheader') && @mb_internal_encoding(_CHARSET)) {
				$mbmail_subject =mb_convert_encoding( $mbmail_subject  , 'SJIS' , _CHARSET) ;
				$mbmail_body =mb_convert_encoding( $mbmail_body   , 'SJIS' , _CHARSET) ;
			}
		}
		$mbmail_subject =rawurlencode( $mbmail_subject) ;
		$mbmail_body =rawurlencode($mbmail_body) ;
		$ret = "<a href=\"".XOOPS_URL.'/modules/'.$this->_module_dirname."/print.php?articleID=" . $sbarticle->getVar('articleID') . "\" target=\"_blank\">"
									."<img src='" . $pathIcon16."/printer.png' border=\"0\" alt=\""._MD_SB_PRINTART."\" width=\"16\" height=\"16\">"
									."</a>&nbsp;"
									."<a href=\"mailto:?subject=".$myts->htmlSpecialChars($mbmail_subject) ."&amp;body=".$myts->htmlSpecialChars($mbmail_body) .":  ".XOOPS_URL."/modules/".$this->_module_dirname."/article.php?articleID=".$sbarticle->getVar('articleID')." \" target=\"_blank\">"
									."<img src='" . $pathIcon16."/mail_forward.png' border=\"0\" alt=\""._MD_SB_SENDTOFRIEND."\" width=\"16\" height=\"16\">"
									."</a>&nbsp;";
		return $ret;
	}	

}
?>