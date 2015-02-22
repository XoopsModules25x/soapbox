<?php
// $Id: entryob.php,v 0.0.1 2004/11/05 13:00:00 domifara Exp $
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
// Author: Jan Pedersen (Mithrandir)                                         //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
require_once XOOPS_ROOT_PATH."/modules/soapbox/include/cleantags.php";

class SoapboxSbvotedata extends XoopsObject {

    function SoapboxSbvotedata() {
		$this->initVar('ratingid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('lid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('rating', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('ratinguser', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('ratinghostname', XOBJ_DTYPE_TXTBOX, "", false, 60);
		$this->initVar('ratingtimestamp', XOBJ_DTYPE_LTIME, 0, false);
		//not in table
		$this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('doimage', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);
	}

	//##################### HACK Methods ######################
	//HACK for utf-8   clean when if utf-8 text is lost bytes
    /**
    * returns a specific variable for the object in a proper format
    * 
    * @access public
    * @param string $key key of the object's variable to be returned
    * @param string $format format to use for the output
    * @return mixed formatted value of the variable
    */
    function &getVar($key, $format = 's')
    {
        $ret = $this->vars[$key]['value'];
		//HACK for lost last byte cleaning of multi byte string
		//---------------------------------------
		if (XOOPS_USE_MULTIBYTES == 1) {
			switch ($this->vars[$key]['data_type']) {
				case XOBJ_DTYPE_TXTBOX:
				case XOBJ_DTYPE_TXTAREA:
					$ret = $this->getJ_cleanLostByteTail($ret);
					break 1;
				default:
					break 1;
			}
		}
		//---------------------------------------
        switch ($this->vars[$key]['data_type']) {

        case XOBJ_DTYPE_TXTBOX:
            switch (strtolower($format)) {
            case 's':
            case 'show':
	            $ts =& MyTextSanitizer::getInstance();
                $ret = $ts->htmlSpecialChars($ret);
                break 1;
            case 'e':
            case 'edit':
                $ts =& MyTextSanitizer::getInstance();
                $ret = $ts->htmlSpecialChars($ret);
                break 1;
            case 'p':
            case 'preview':
            case 'f':
            case 'formpreview':
                $ts =& MyTextSanitizer::getInstance();
                $ret = $ts->htmlSpecialChars($ts->stripSlashesGPC($ret));
                break 1;
            case 'n':
            case 'none':
            default:
                break 1;
            }
            break;
        case XOBJ_DTYPE_TXTAREA:
            switch (strtolower($format)) {
            case 's':
            case 'show':
                $ts =& MyTextSanitizer::getInstance();
                $html = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                $xcode = (!isset($this->vars['doxcode']['value']) || $this->vars['doxcode']['value'] == 1) ? 1 : 0;
                $smiley = (!isset($this->vars['dosmiley']['value']) || $this->vars['dosmiley']['value'] == 1) ? 1 : 0;
                $image = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                $br = (!isset($this->vars['dobr']['value']) || $this->vars['dobr']['value'] == 1) ? 1 : 0;
				//----------------
				if ($html == 1 && $br != 0){
					$text = preg_replace("/>((\015\012)|(\015)|(\012))/",">",$ret);
					$text = preg_replace("/((\015\012)|(\015)|(\012))</","<",$ret);
				}
				$ret = $GLOBALS['SoapboxCleantags']->cleanTags($ts->displayTarea($ret, $html, $smiley, $xcode, $image, $br));
				//----------------
                break 1;
            case 'e':
            case 'edit':
                $ret = htmlspecialchars($ret, ENT_QUOTES);
                break 1;
            case 'p':
            case 'preview':
                $ts =& MyTextSanitizer::getInstance();
                $html = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                $xcode = (!isset($this->vars['doxcode']['value']) || $this->vars['doxcode']['value'] == 1) ? 1 : 0;
                $smiley = (!isset($this->vars['dosmiley']['value']) || $this->vars['dosmiley']['value'] == 1) ? 1 : 0;
                $image = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                $br = (!isset($this->vars['dobr']['value']) || $this->vars['dobr']['value'] == 1) ? 1 : 0;
				//----------------
				if ($html == 1 && $br != 0){
					$text = preg_replace("/>((\015\012)|(\015)|(\012))/",">",$ret);
					$text = preg_replace("/((\015\012)|(\015)|(\012))</","<",$ret);
				}
                $ret = $GLOBALS['SoapboxCleantags']->cleanTags($ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br));
				//----------------
                break 1;
            case 'f':
            case 'formpreview':
                $ts =& MyTextSanitizer::getInstance();
                $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                break 1;
            case 'n':
            case 'none':
            default:
                break 1;
            }
            break;
        case XOBJ_DTYPE_INT:
            $ret = intval($ret);
            break;
        case XOBJ_DTYPE_ARRAY:
            $ret =& unserialize($ret);
            break;
        case XOBJ_DTYPE_SOURCE:
            switch (strtolower($format)) {
            case 's':
            case 'show':
                break 1;
            case 'e':
            case 'edit':
                $ret = htmlspecialchars($ret, ENT_QUOTES);
                break 1;
            case 'p':
            case 'preview':
                $ts =& MyTextSanitizer::getInstance();
                $ret = $ts->stripSlashesGPC($ret);
                break 1;
            case 'f':
            case 'formpreview':
                $ts =& MyTextSanitizer::getInstance();
                $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                break 1;
            case 'n':
            case 'none':
            default:
                break 1;
            }
            break;
        default:
            if ($this->vars[$key]['options'] != '' && $ret != '') {
                switch (strtolower($format)) {
                case 's':
                case 'show':
					$selected = explode('|', $ret);
                    $options = explode('|', $this->vars[$key]['options']);
                    $i = 1;
                    $ret = array();
                    foreach ($options as $op) {
                        if (in_array($i, $selected)) {
                            $ret[] = $op;
                        }
                        $i++;
                    }
                    $ret = implode(', ', $ret);
                    break;
                case 'e':
                case 'edit':
                    $ret = explode('|', $ret);
                    break 1;
                default:
                    break 1;
                }

            }
            break;
        }
        return $ret;
    }

	function getJ_cleanLostByteTail($text)
	{
		if (strtoupper(_CHARSET) == 'UTF-8'){
			$text = preg_replace('/[\xC0-\xFD]$/',"",$text);
			$text = preg_replace('/[\xE0-\xFD][\x80-\xBF]$/',"",$text);
			$text = preg_replace('/[\xF0-\xFD][\x80-\xBF]{2}$/',"",$text);
			$text = preg_replace('/[\xF8-\xFD][\x80-\xBF]{3}$/',"",$text);
			$text = preg_replace('/[\xFC-\xFD][\x80-\xBF]{4}$/',"",$text);
			$text = preg_replace('/^([\x80-\xBF]+)/',"",$text);
		} elseif (strtoupper(_CHARSET) == 'EUC-JP'){
		    if (preg_match('/[\x80-\xff]$/',$text)){
			    $tmp = preg_replace('/\x8F[\x80-\xff]{2}/',"",$text); //EUC-jp EX 3 byte Foreign string
			    $tmp = preg_replace('/[\x80-\xff]{2}/',"",$tmp);
			    if (preg_match('/[\x80-\xff]$/',$tmp)){
			    	$text = substr($text,0,-1) ;
			    }
			    if (preg_match('/^[\x80-\xff]/',$tmp)){
			    	$text = substr($text,1) ;
			    }
		    }
		} else {
		    if (preg_match('/[\x80-\xff]$/',$text)){
			    $tmp = preg_replace('/[\x80-\xff]{2}/',"",$text);
			    if (preg_match('/[\x80-\xff]$/',$tmp)){
			    	$text = substr($text,0,-1) ;
			    }
			    if (preg_match('/^[\x80-\xff]/',$tmp)){
			    	$text = substr($text,1) ;
			    }
		    }
	    }
	
		return $text ;
	}

    /**
    * Returns an array representation of the object
    *
    * @return array
    */
    function toArray() {
        $ret = array();
        $vars = $this->getVars();
        foreach (array_keys($vars) as $i) {
            $ret[$i] = $this->getVar($i);
        }
        return $ret;
    }

}

class SoapboxSbvotedataHandler extends XoopsPersistableObjectHandler {
 
    /**
     * create a new entry
     * 
     * @param bool $isNew flag the new objects as "new"?
     * @return object SoapboxSbvotedata
     */
    function &create($isNew = true)
    {
        $entry = new SoapboxSbvotedata();
        if ($isNew) {
            $entry->setNew();
        }
        return $entry;
    }
    /**
     * retrieve a entry
     * 
     * @param int $ratingid ratingid of the entry
     * @return mixed reference to the {@link Entry} object, FALSE if failed
     */
    function &get($id)
    {
		$ret = false ;
        if (intval($id) > 0) {
            $sql = "SELECT * FROM ".$this->db -> prefix( "sbvotedata" )." WHERE ratingid = '$id'" ;
            if (!$result = $this->db->query($sql)) {
                return $ret;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $entry = new SoapboxSbvotedata();
                $entry->assignVars($this->db->fetchArray($result));
                return $entry;
            }
        }
        return $ret;
    }


    /**
     * retrieve entrys from the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be match
     * @param bool $id_as_key use the ratingid as key for the array?
     * @return array array of {@link SoapboxSbvotedata} objects
     */
    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
            $sql = "SELECT * FROM ".$this->db -> prefix( "sbvotedata" ) ;
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
			$entry = new SoapboxSbvotedata();
            $entry->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $entry;
            } else {
                $ret[$myrow['ratingid']] =& $entry;
            }
            unset($entry);
        }
        return $ret;
    }

    /**
     * insert a new entry in the database
     * 
     * @param object $entry reference to the {@link SoapboxSbvotedata} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insert(&$entry, $force = false)
    {
        if (strtolower(get_class($entry)) != strtolower('SoapboxSbvotedata') ) {
            return false;
        }
        if (!$entry->isDirty()) {
            return true;
        }
        if (!$entry->cleanVars()) {
            return false;
        }
        foreach ($entry->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        // RMV-NOTIFY
        if ($entry->isNew()) {
            $ratingid = $this->db->genId($this->db->prefix('sbvotedata').'_ratingid_seq');
            $sql = sprintf("INSERT INTO %s (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES (%u, %u, %u, %u, %s, %u)", $this->db->prefix('sbvotedata'), $ratingid, $lid, $ratinguser, $rating, $this->db->quoteString($ratinghostname), $ratingtimestamp );
        } else {
            $sql = sprintf("UPDATE %s SET lid = %u, ratinguser = %u, rating = %u, ratinghostname = %s, ratingtimestamp = %uratingtimestamp WHERE ratingid = %u", $this->db->prefix('sbvotedata'), $ratinguser, $rating, $this->db->quoteString($ratinghostname), $ratingtimestamp, $ratingid );
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($ratingid)) {
            $ratingid = $this->db->getInsertId();
        }
        $entry->assignVar('ratingid', $ratingid);
        return true;
    }

    /**
     * delete a entry from the database
     * 
     * @param object $entry reference to the entry to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function delete(&$entry, $force = false)
    {
        if (strtolower(get_class($entry)) != strtolower('SoapboxSbvotedata') ) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE ratingid = %u", $this->db->prefix("sbvotedata"), $entry->getVar('ratingid'));
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
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
    function deleteEntrys($criteria = null, $force = false)
    {
        $sql = sprintf("DELETE FROM %s " , $this->db->prefix("sbvotedata"));
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * count entrys matching a condition
     * 
     * @param object $criteria {@link CriteriaElement} to match
     * @return int count of entrys
     */
    function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('sbvotedata');
        
        
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }
	
    /**
     * updates a single field in a Votedata record
     *
     * @param object $entry reference to the {@link SoapboxSbvotedata} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateByField(&$entry, $fieldName, $fieldValue , $force = false)
    {
        if (strtolower(get_class($entry)) != strtolower('SoapboxSbvotedata') ) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);
        return $this->insert($entry , $force);
    }

}
?>