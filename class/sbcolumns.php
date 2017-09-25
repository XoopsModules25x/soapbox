<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright      {@link https://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */

// defined('XOOPS_ROOT_PATH') || exit('Restricted access.');
require_once XOOPS_ROOT_PATH . '/modules/soapbox/include/cleantags.php';

/**
 * Class SoapboxSbcolumns
 */
class SoapboxSbcolumns extends XoopsObject
{
    /**
     * SoapboxSbcolumns constructor.
     */
    public function __construct()
    {
        $this->initVar('columnID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('author', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, '', true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('total', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('colimage', XOBJ_DTYPE_TXTBOX, 'blank.png', false, 255);
        $this->initVar('created', XOBJ_DTYPE_INT, 1033141070, false);
        //not in table
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);

        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);
    }

    //##################### HACK Methods ######################
    //HACK for utf-8   clean when if utf-8 text is lost bytes
    /**
     * returns a specific variable for the object in a proper format
     *
     * @access public
     * @param  string $key    key of the object's variable to be returned
     * @param  string $format format to use for the output
     * @return mixed  formatted value of the variable
     */
    public function &getVar($key, $format = 's')
    {
        $ret = $this->vars[$key]['value'];
        //HACK for lost last byte cleaning of multi byte string
        //---------------------------------------
        if (XOOPS_USE_MULTIBYTES === 1) {
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
                        $ts  = MyTextSanitizer::getInstance();
                        $ret = $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'e':
                    case 'edit':
                        $ts  = MyTextSanitizer::getInstance();
                        $ret = $ts->htmlSpecialChars($ret);
                        break 1;
                    case 'p':
                    case 'preview':
                    case 'f':
                    case 'formpreview':
                        $ts  = MyTextSanitizer::getInstance();
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
                        $ts     = MyTextSanitizer::getInstance();
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value'])
                                   || 1 === $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value'])
                                   || 1 === $this->vars['dosmiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || 1 === $this->vars['dobr']['value']) ? 1 : 0;
                        //----------------
                        if (1 === $html && 0 !== $br) {
                            $text = preg_replace(">((\015\012)|(\015)|(\012))/", '>', $ret);
                            $text = preg_replace("/((\015\012)|(\015)|(\012))</", '<', $ret);
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
                        $ts     = MyTextSanitizer::getInstance();
                        $html   = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                        $xcode  = (!isset($this->vars['doxcode']['value'])
                                   || 1 === $this->vars['doxcode']['value']) ? 1 : 0;
                        $smiley = (!isset($this->vars['dosmiley']['value'])
                                   || 1 === $this->vars['dosmiley']['value']) ? 1 : 0;
                        $image  = (!isset($this->vars['doimage']['value'])
                                   || 1 === $this->vars['doimage']['value']) ? 1 : 0;
                        $br     = (!isset($this->vars['dobr']['value']) || 1 === $this->vars['dobr']['value']) ? 1 : 0;
                        //----------------
                        if (1 === $html && 0 !== $br) {
                            $text = preg_replace(">((\015\012)|(\015)|(\012))/", '>', $ret);
                            $text = preg_replace("/((\015\012)|(\015)|(\012))</", '<', $ret);
                        }
                        $ret = $GLOBALS['SoapboxCleantags']->cleanTags($ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br));
                        //----------------
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts  = MyTextSanitizer::getInstance();
                        $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            case XOBJ_DTYPE_INT:
                $ret = (int)$ret;
                break;
            case XOBJ_DTYPE_ARRAY:
                $ret = unserialize($ret);
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
                        $ts  = MyTextSanitizer::getInstance();
                        $ret = $ts->stripSlashesGPC($ret);
                        break 1;
                    case 'f':
                    case 'formpreview':
                        $ts  = MyTextSanitizer::getInstance();
                        $ret = htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                        break 1;
                    case 'n':
                    case 'none':
                    default:
                        break 1;
                }
                break;
            default:
                if ('' !== $ret && '' !== $this->vars[$key]['options']) {
                    switch (strtolower($format)) {
                        case 's':
                        case 'show':
                            $selected = explode('|', $ret);
                            $options  = explode('|', $this->vars[$key]['options']);
                            $i        = 1;
                            $ret      = [];
                            foreach ($options as $op) {
                                if (in_array($i, $selected)) {
                                    $ret[] = $op;
                                }
                                ++$i;
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
     * @param $text
     * @return mixed
     */
    public function getJ_cleanLostByteTail($text)
    {
        if ('UTF-8' === strtoupper(_CHARSET)) {
            $text = preg_replace('/[\xC0-\xFD]$/', '', $text);
            $text = preg_replace('/[\xE0-\xFD][\x80-\xBF]$/', '', $text);
            $text = preg_replace('/[\xF0-\xFD][\x80-\xBF]{2}$/', '', $text);
            $text = preg_replace('/[\xF8-\xFD][\x80-\xBF]{3}$/', '', $text);
            $text = preg_replace('/[\xFC-\xFD][\x80-\xBF]{4}$/', '', $text);
            $text = preg_replace('/^([\x80-\xBF]+)/', '', $text);
        } elseif ('EUC-JP' === strtoupper(_CHARSET)) {
            if (preg_match('/[\x80-\xff]$/', $text)) {
                $tmp = preg_replace('/\x8F[\x80-\xff]{2}/', '', $text); //EUC-jp EX 3 byte Foreign string
                $tmp = preg_replace('/[\x80-\xff]{2}/', '', $tmp);
                if (preg_match('/[\x80-\xff]$/', $tmp)) {
                    $text = substr($text, 0, -1);
                }
                if (preg_match('/^[\x80-\xff]/', $tmp)) {
                    $text = substr($text, 1);
                }
            }
        } else {
            if (preg_match('/[\x80-\xff]$/', $text)) {
                $tmp = preg_replace('/[\x80-\xff]{2}/', '', $text);
                if (preg_match('/[\x80-\xff]$/', $tmp)) {
                    $text = substr($text, 0, -1);
                }
                if (preg_match('/^[\x80-\xff]/', $tmp)) {
                    $text = substr($text, 1);
                }
            }
        }

        return $text;
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        $ret  = [];
        $vars =& $this->getVars();
        foreach (array_keys($vars) as $i) {
            $ret[$i] =& $this->getVar($i);
        }

        return $ret;
    }
}

/**
 * Class SoapboxSbcolumnsHandler
 */
class SoapboxSbcolumnsHandler extends XoopsPersistableObjectHandler
{
    public $totalarts_AllPermcheck;

    /**
     * create a new category
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object SoapboxSbcolumns
     */
    public function create($isNew = true)
    {
        $sbcolumn = new SoapboxSbcolumns();
        if ($isNew) {
            $sbcolumn->setNew();
        }

        return $sbcolumn;
    }

    /**
     * retrieve a category
     *
     * @param  mixed|null $id
     * @param  null       $fields
     * @return mixed      reference to the <a href='psi_element://SoapboxSbcolumns'>SoapboxSbcolumns</a> object, FALSE if failed
     *                           object, FALSE if failed
     *                           object, FALSE if failed
     * @internal param int $columnID columnID of the category
     */
    public function get($id = null, $fields = null) //&get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('sbcolumns') . " WHERE columnID = '$id'";
            if (!$result = $this->db->query($sql)) {
                return $ret;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 === $numrows) {
                $sbcolumn = new SoapboxSbcolumns();
                $sbcolumn->assignVars($this->db->fetchArray($result));

                return $sbcolumn;
            }
        }

        return $ret;
    }

    /**
     * retrieve categorys from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement} conditions to be match
     * @param  bool            $id_as_key use the columnID as key for the array?
     * @param  bool            $as_object
     * @return array           array of <a href='psi_element://SoapboxSbcolumns'>SoapboxSbcolumns</a> objects
     *                                    objects
     */
    public function &getObjects(
        CriteriaElement $criteria = null,
        $id_as_key = false,
        $as_object = true
    ) //&getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('sbcolumns');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' !== $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $sbcolumn = new SoapboxSbcolumns();
            $sbcolumn->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $sbcolumn;
            } else {
                $ret[$myrow['columnID']] = $sbcolumn;
            }
            unset($sbcolumn);
        }
        $this->db->freeRecordSet($result);

        return $ret;
    }

    /**
     * insert a new category in the database
     *
     * @param  XoopsObject $sbcolumn reference to the {@link SoapboxSbcolumns} object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(XoopsObject $sbcolumn, $force = false)//insert($sbcolumn, $force = false)
    {
        if (strtolower(get_class($sbcolumn)) !== strtolower('SoapboxSbcolumns')) {
            return false;
        }
        if (!$sbcolumn->isDirty()) {
            return true;
        }
        if (!$sbcolumn->cleanVars()) {
            return false;
        }
        foreach ($sbcolumn->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        // RMV-NOTIFY
        if ($sbcolumn->isNew()) {
            $columnID = $this->db->genId($this->db->prefix('sbcolumns') . '_columnID_seq');
            $sql      = sprintf(
                'INSERT INTO %s (columnID, author, NAME, description, total, weight, colimage, created) VALUES (%u, %u, %s, %s, %u, %u, %s, %u)',
                $this->db->prefix('sbcolumns'),
                $columnID,
                $author,
                $this->db->quoteString($name),
                $this->db->quoteString($description),
                $total,
                $weight,
                                $this->db->quoteString($colimage),
                $created
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET author = %s, NAME = %s, description = %s, total = %u, weight = %u, colimage = %s, created = %u WHERE columnID = %u',
                $this->db->prefix('sbcolumns'),
                $author,
                $this->db->quoteString($name),
                $this->db->quoteString($description),
                $total,
                $weight,
                           $this->db->quoteString($colimage),
                $created,
                $columnID
            );
        }
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }
        if (empty($columnID)) {
            $columnID = $this->db->getInsertId();
        }
        $sbcolumn->assignVar('columnID', $columnID);

        return true;
    }

    /**
     * delete a category from the database
     *
     * @param  XoopsObject $sbcolumn reference to the category to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(XoopsObject $sbcolumn, $force = false)//delete($sbcolumn, $force = false)
    {
        if (strtolower(get_class($sbcolumn)) !== strtolower('SoapboxSbcolumns')) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE columnID = %u', $this->db->prefix('sbcolumns'), $sbcolumn->getVar('columnID'));
        if (false !== $force) {
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
     * count categorys matching a condition
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of categorys
     */
    public function getCount(CriteriaElement $criteria = null)//getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('sbcolumns');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * updates a single field in a Column record
     *
     * @param  object $entry      reference to the {@link SoapboxSbcolumns} object
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @param  bool   $force
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateByField($entry, $fieldName, $fieldValue, $force = false)
    {
        if (strtolower(get_class($entry)) !== strtolower('SoapboxSbcolumns')) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);

        return $this->insert($entry, $force);
    }
}
