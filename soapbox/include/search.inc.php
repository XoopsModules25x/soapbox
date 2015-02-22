<?php
// $Id: search.inc.php,v 0.0.1 2005/10/24 20:30:00 domifara Exp $
/**
 * $Id: search.inc.php v 1.5 25 April 2004 hsalazar Exp $
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 25 April 2004
 * Author: hsalazar
 * Licence: GNU
 */
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
function sb_search( $queryarray, $andor, $limit, $offset, $userid )
	{
	global $xoopsUser ;
	$ret = array();
	if (!is_object( $xoopsUser) ||  $xoopsUser->getVar('uid') == 0 ) {
		return $ret;
	}
	$count = count( $queryarray );
	if ($userid == 0 ){
		if ( !is_array( $queryarray )  || empty( $queryarray ) || $count == 0) {
			return $ret;
		}
	}
	//-------------------------------------	
	$_entrydata_handler =& xoops_getmodulehandler('entryget','soapbox');
	//-------------------------------------	
	$canread_columnIDs = array() ;
	$canread_columnnames = array() ;
	$_userinfo_authors_column = array() ;
	$_column_authors_uid = array() ;
	//get category object
	$_categoryob_arr =& $_entrydata_handler->getColumnsAllPermcheck( 
																0 , 0 , 
																true , null ,null ,
																null , null ,
																false);
	foreach ($_categoryob_arr as $k=>$_categoryob) {
		$canread_columnIDs[] = $_categoryob->getVar('columnID') ;
		$canread_columnnames[$_categoryob->getVar('columnID')] = $_categoryob->getVar('name') ;
		$_column_authors_uid[$_categoryob->getVar('columnID')] = $_categoryob->getVar('author') ;
		if ( $userid != 0 && $userid ==$_categoryob->getVar('author') ) {
				$_userinfo_authors_column[] = $_categoryob->getVar('columnID') ;
		}
	}
//
	if (empty($canread_columnIDs)) {
		return $ret;
	}
	$criteria =	new CriteriaCompo();
	$crit_canread = new CriteriaCompo();
	$crit_canread->add(new Criteria( 'columnID',  '('.implode(',', array_unique($canread_columnIDs)).')' , 'IN') );
    $criteria->add($crit_canread , 'AND');
    unset($crit_canread);
//for userinfo
	if ( $userid != 0 &&  !empty($_userinfo_authors_column) && count($_userinfo_authors_column) > 0) {
		$criteria_userinfo = new CriteriaCompo();
		$criteria_userinfo->add(new Criteria( 'columnID',  '('.implode(',', array_unique($_userinfo_authors_column)).')' , 'IN') );
	    $criteria->add($criteria_userinfo , 'AND');
	    unset($criteria_userinfo);
	}
//for serch form
	if ( is_array( $queryarray )  && $count > 0){
	   	$crit_query = new CriteriaCompo();
		foreach ($queryarray as $query_v) {
			$crit_query_one = new CriteriaCompo();
			$crit_query_one->add(new Criteria('columnID',  '('.implode(',', array_unique($_userinfo_authors_column)).')' , 'IN') );
			$crit_query_one->add(new Criteria('headline','%'.$query_v.'%' , 'like'));
			$crit_query_one->add(new Criteria('headline' , '.*('.preg_quote($query_v).').*' , 'regexp') , 'OR');
			$crit_query_one->add(new Criteria('lead' , '%'.$query_v.'%' , 'like') , 'OR');
			$crit_query_one->add(new Criteria('lead' , '.*('.preg_quote($query_v).').*' , 'regexp') , 'OR');
			$crit_query_one->add(new Criteria('bodytext' , '%'.$query_v.'%' , 'like') , 'OR');
			$crit_query_one->add(new Criteria('bodytext' , '.*('.preg_quote($query_v).').*' , 'regexp') , 'OR');
	        $crit_query->add($crit_query_one, $andor);
	        unset($crit_query_one);
		}
	    $criteria->add($crit_query , 'AND');
	}

    $criteria->setStart($offset);
    $criteria->setLimit($limit);
    $criteria->setSort('datesub');
    $criteria->setOrder('DESC');
    $sbarticles_arr =& $_entrydata_handler->getArticles($criteria);
    unset($criteria);
	
	$i = 0;
	foreach ($sbarticles_arr as $sbarticles) {
			$ret[$i]['image'] = "images/sb.png";
			$ret[$i]['link'] = "article.php?articleID=" . $sbarticles->getVar('articleID');
			$ret[$i]['title'] = $sbarticles->getVar('headline');
			$ret[$i]['time'] = $sbarticles->getVar('datesub');
			$ret[$i]['uid'] = $_column_authors_uid[$sbarticles->getVar('columnID')];
			$i++;
	} 
	return $ret;
} 
?>