<?php namespace XoopsModules\Soapbox;

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

use XoopsModules\Soapbox;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
//require_once XOOPS_ROOT_PATH . '/modules/soapbox/include/cleantags.php';


/**
 * Class ColumnsHandler
 */
class ColumnsHandler extends \XoopsPersistableObjectHandler
{
    public $totalarts_AllPermcheck;

    /**
     * create a new category
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object Columns
     */
    public function create($isNew = true)
    {
        $sbcolumn = new Columns();
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
     * @return mixed      reference to the <a href='psi_element://Columns'>Columns</a> object, FALSE if failed
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
                $sbcolumn = new Columns();
                $sbcolumn->assignVars($this->db->fetchArray($result));

                return $sbcolumn;
            }
        }

        return $ret;
    }

    /**
     * retrieve categorys from the database
     *
     * @param  \CriteriaElement $criteria  {@link CriteriaElement} conditions to be match
     * @param  bool            $id_as_key use the columnID as key for the array?
     * @param  bool            $as_object
     * @return array           array of <a href='psi_element://Columns'>Columns</a> objects
     *                                    objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true) //&getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('sbcolumns');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
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
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $sbcolumn = new Columns();
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
     * @param \XoopsObject $sbcolumn reference to the {@link Columns}
     *                               object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $sbcolumn, $force = false)//insert($sbcolumn, $force = false)
    {
        if (strtolower(get_class($sbcolumn)) !== strtolower('Columns')) {
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
     * @param \XoopsObject $sbcolumn reference to the category to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $sbcolumn, $force = false)//delete($sbcolumn, $force = false)
    {
        if (strtolower(get_class($sbcolumn)) !== strtolower('Columns')) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE columnID = %u', $this->db->prefix('sbcolumns'), $sbcolumn->getVar('columnID'));
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
     * @param  \CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of categorys
     */
    public function getCount(\CriteriaElement $criteria = null)//getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('sbcolumns');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
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
     * @param  object $entry      reference to the {@link Columns} object
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @param  bool   $force
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateByField($entry, $fieldName, $fieldValue, $force = false)
    {
        if (strtolower(get_class($entry)) !== strtolower('Columns')) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);

        return $this->insert($entry, $force);
    }
}
