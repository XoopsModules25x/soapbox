<?php
// $Id: sbarticles.php,v 0.0.1 2004/11/05 13:00:00 domifara Exp $
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
if ( !defined("XOOPS_MAINFILE_INCLUDED") || !defined("XOOPS_ROOT_PATH") || !defined("XOOPS_URL") ) {
	exit();
}
require_once XOOPS_ROOT_PATH."/modules/soapbox/include/cleantags.php";
if (!defined('XOBJ_SB_DTYPE_FLOAT')){
	define('XOBJ_SB_DTYPE_FLOAT', 21);
}
class SoapboxSbarticles extends XoopsObject {

	var $pre_offline;
	var $_sbcolumns;

    function SoapboxSbarticles()
	{
		$this->initVar('articleID', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('columnID', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('headline', XOBJ_DTYPE_TXTBOX, "", false, 255);
		$this->initVar('lead', XOBJ_DTYPE_OTHER, "", false);
		$this->initVar('bodytext', XOBJ_DTYPE_OTHER, "", false);
		$this->initVar('teaser', XOBJ_DTYPE_TXTAREA, "", false);
		$this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('submit', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('datesub', XOBJ_DTYPE_LTIME, time() , false);
		$this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('html', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('smiley', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('xcodes', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('breaks', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('block', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('artimage', XOBJ_DTYPE_TXTBOX, "blank.png", false, 255);
		$this->initVar('votes', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('rating', XOBJ_SB_DTYPE_FLOAT, 0.0000, false);
		$this->initVar('commentable', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('offline', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);
		
		$this->pre_offline = 1;
		$this->_sbcolumns = false;
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
                $html = !empty($this->vars['html']['value']) ? 1 : 0;
                $xcode = (!isset($this->vars['xcodes']['value']) || $this->vars['xcodes']['value'] == 1) ? 1 : 0;
                $smiley = (!isset($this->vars['smiley']['value']) || $this->vars['smiley']['value'] == 1) ? 1 : 0;
                $image = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                $br = (!isset($this->vars['breaks']['value']) || $this->vars['breaks']['value'] == 1) ? 1 : 0;
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
                $html = !empty($this->vars['html']['value']) ? 1 : 0;
                $xcode = (!isset($this->vars['xcodes']['value']) || $this->vars['xcodes']['value'] == 1) ? 1 : 0;
                $smiley = (!isset($this->vars['smiley']['value']) || $this->vars['smiley']['value'] == 1) ? 1 : 0;
                $image = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                $br = (!isset($this->vars['breaks']['value']) || $this->vars['breaks']['value'] == 1) ? 1 : 0;
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
        case XOBJ_SB_DTYPE_FLOAT:
			if ( function_exists('floatval') ) {
	            $ret = floatval($ret);
			} else {
	            $ret = intval($ret);
			}
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
                return htmlspecialchars($ret, ENT_QUOTES);
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

    /**
     * clean values of all variables of the object for storage.
     * also add slashes whereever needed
     *
     * @return bool true if successful
     * @access public
     */
    function cleanVars()
    {
        $ts =& MyTextSanitizer::getInstance();
        foreach ($this->vars as $k => $v) {
            $cleanv = $v['value'];
            if (!$v['changed']) {
            } else {
                $cleanv = is_string($cleanv) ? trim($cleanv) : $cleanv;
                switch ($v['data_type']) {
                case XOBJ_DTYPE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $this->setErrors("$k is required.");
                        continue;
                    }
                    if (isset($v['maxlength']) && strlen($cleanv) > intval($v['maxlength'])) {
                        $this->setErrors("$k must be shorter than ".intval($v['maxlength'])." characters.");
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    break;
                case XOBJ_DTYPE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $this->setErrors("$k is required.");
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($ts->censorString($cleanv));
                    } else {
                        $cleanv = $ts->censorString($cleanv);
                    }
                    break;
                case XOBJ_DTYPE_SOURCE:
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    } else {
                        $cleanv = $cleanv;
                    }
                    break;
                case XOBJ_DTYPE_INT:
                    $cleanv = intval($cleanv);
                    break;
//HACK by domifara                    
                case XOBJ_SB_DTYPE_FLOAT:
					if ( function_exists('floatval') ) {
	                    $cleanv = floatval($cleanv);
					} else {
	                    $cleanv = intval($cleanv);
					}
                    break;
                case XOBJ_DTYPE_EMAIL:
                    if ($v['required'] && $cleanv == '') {
                        $this->setErrors("$k is required.");
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$cleanv)) {
                        $this->setErrors("Invalid Email");
                        continue;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv = $ts->stripSlashesGPC($cleanv);
                    }
                    break;
                case XOBJ_DTYPE_URL:
                    if ($v['required'] && $cleanv == '') {
                        $this->setErrors("$k is required.");
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = 'http://' . $cleanv;
                    }
                    if (!$v['not_gpc']) {
                        $cleanv =& $ts->stripSlashesGPC($cleanv);
                    }
                    break;
                case XOBJ_DTYPE_ARRAY:
                    $cleanv = serialize($cleanv);
                    break;
                case XOBJ_DTYPE_STIME:
                case XOBJ_DTYPE_MTIME:
                case XOBJ_DTYPE_LTIME:
                    $cleanv = !is_string($cleanv) ? intval($cleanv) : strtotime($cleanv);
                    break;
                default:
                    break;
                }
            }
            $this->cleanVars[$k] =& $cleanv;
            unset($cleanv);
        }
        if (count($this->_errors) > 0) {
            return false;
        }
        $this->unsetDirty();
        return true;
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

class SoapboxSbarticlesHandler extends XoopsPersistableObjectHandler {

	var $totalarts_AllPermcheck ; 
    /**
     * create a new entry
     * 
     * @param bool $isNew flag the new objects as "new"?
     * @return object SoapboxSbarticles
     */
    function &create($isNew = true)
    {
        $sbarticle = new SoapboxSbarticles();
        if ($isNew) {
            $sbarticle->setNew();
        }
        return $sbarticle;
    }
    /**
     * retrieve a entry
     * 
     * @param int $articleID articleID of the entry
     * @return mixed reference to the {@link soapboxEntry} object, FALSE if failed
     */
    function &get($id)
    {
		$ret = false ;
		if (intval($id) > 0) {
            $sql = "SELECT * FROM ".$this->db -> prefix( "sbarticles" )." WHERE articleID = '$id'" ;
            if (!$result = $this->db->query($sql)) {
                return $ret;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $sbarticle = new SoapboxSbarticles();
                $sbarticle->assignVars($this->db->fetchArray($result));
				//pre_offline value buckup
				if ($sbarticle->getVar('offline') || $sbarticle->getVar('submit')){
					$sbarticle->pre_offline =  1;
				}
                return $sbarticle;
            }
        }
        return $ret;
    }


    /**
     * retrieve entrys from the database
     * 
     * @param object $criteria {@link CriteriaElement} conditions to be match
     * @param bool $id_as_key use the articleID as key for the array?
     * @return array array of {@link SoapboxSbarticles} objects
     */
    function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
            $sql = "SELECT * FROM ".$this->db -> prefix( "sbarticles" ) ;
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
			$sbarticle = new SoapboxSbarticles();
            $sbarticle->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $sbarticle;
            } else {
                $ret[$myrow['articleID']] =& $sbarticle;
            }
            unset($sbarticle);
        }
		$this->db->freeRecordSet($result) ;
        return $ret;
    }

    /**
     * insert a new entry in the database
     * 
     * @param object $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param bool $force
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    function insert(&$sbarticle, $force = false)
    {
        if (strtolower(get_class($sbarticle)) != 'soapboxsbarticles' ) {
            return false;
        }
        if (!$sbarticle->isDirty()) {
            return true;
        }
        if (!$sbarticle->cleanVars()) {
            return false;
        }
        foreach ($sbarticle->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        // RMV-NOTIFY
        if ($sbarticle->isNew()) {
            $articleID = $this->db->genId($this->db->prefix('sbarticles').'_articleID_seq');
            $sql = sprintf("INSERT INTO %s (articleID, columnID, headline, lead, bodytext, teaser, uid, submit, datesub, counter, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, commentable, offline, notifypub) VALUES (%u, %u, %s, %s, %s, %s, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %s, %u, %f, %u, %u, %u )", $this->db->prefix('sbarticles'), $articleID, $columnID, $this->db->quoteString($headline), $this->db->quoteString($lead), $this->db->quoteString($bodytext), $this->db->quoteString($teaser), $uid, $submit, $datesub, $counter, $weight, $html, $smiley, $xcodes, $breaks, $block, $this->db->quoteString($artimage), $votes, $rating, $commentable, $offline, $notifypub);
        } else {
            $sql = sprintf("UPDATE %s SET columnID = %u , headline = %s , lead = %s , bodytext = %s , teaser = %s , uid = %u , submit = %u , datesub = %u , counter = %u , weight = %u , html = %u , smiley = %u , xcodes = %u , breaks = %u , block = %u , artimage = %s , votes = %u , rating = %f , commentable = %u , offline = %u , notifypub = %u WHERE articleID = %u", $this->db->prefix('sbarticles'), $columnID, $this->db->quoteString($headline), $this->db->quoteString($lead), $this->db->quoteString($bodytext), $this->db->quoteString($teaser), $uid, $submit, $datesub, $counter, $weight, $html, $smiley, $xcodes, $breaks, $block, $this->db->quoteString($artimage), $votes, $rating, $commentable, $offline, $notifypub, $articleID);
        }
        if (false != $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($articleID)) {
            $articleID = $this->db->getInsertId();
        }
        $sbarticle->assignVar('articleID', $articleID);
        return true;
    }

    /**
     * delete a entry from the database
     * 
     * @param object $sbarticle reference to the entry to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function delete(&$sbarticle, $force = false)
    {
     	global $xoopsModule ;
        if (strtolower(get_class($sbarticle)) != strtolower('SoapboxSbarticles')) {
            return false;
        }
        $sql = sprintf("DELETE FROM %s WHERE articleID = %u", $this->db->prefix("sbarticles"), $sbarticle->getVar('articleID'));
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
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('sbarticles');
        
        
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
     * updates a single field in a Article record
     *
     * @param object $entry reference to the {@link SoapboxSbarticles} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateByField(&$entry, $fieldName, $fieldValue, $force = false)
    {
        if (strtolower(get_class($entry)) != strtolower('SoapboxSbarticles') ) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);
        return $this->insert($entry , $force);
    }
}
?>