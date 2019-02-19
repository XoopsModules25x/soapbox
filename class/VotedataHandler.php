<?php

namespace XoopsModules\Soapbox;

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
 * @author         XOOPS Development Team, Jan Pedersen (Mithrandir)
 */

use XoopsModules\Soapbox;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
//require_once XOOPS_ROOT_PATH . '/modules/soapbox/include/cleantags.php';

/**
 * Class VotedataHandler
 */
class VotedataHandler extends \XoopsPersistableObjectHandler
{
    /**
     * create a new entry
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return object Votedata
     */
    public function create($isNew = true)
    {
        $entry = new Votedata();
        if ($isNew) {
            $entry->setNew();
        }

        return $entry;
    }

    /**
     * retrieve a entry
     *
     * @param  mixed|null $id
     * @param  null       $fields
     * @return mixed      reference to the <a href='psi_element://Entry'>Entry</a> object, FALSE if failed
     *                           object, FALSE if failed
     *                           object, FALSE if failed
     * @internal param int $ratingid ratingid of the entry
     */
    public function get($id = null, $fields = null)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('sbvotedata') . " WHERE ratingid = '$id'";
            if (!$result = $this->db->query($sql)) {
                return $ret;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 === $numrows) {
                $entry = new Votedata();
                $entry->assignVars($this->db->fetchArray($result));

                return $entry;
            }
        }

        return $ret;
    }

    /**
     * retrieve entrys from the database
     *
     * @param  \CriteriaElement $criteria  {@link CriteriaElement} conditions to be match
     * @param  bool             $id_as_key use the ratingid as key for the array?
     * @param  bool             $as_object
     * @return array           array of <a href='psi_element://Votedata'>Votedata</a> objects
     *                                     objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('sbvotedata');
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
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
            $entry = new Votedata();
            $entry->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $entry;
            } else {
                $ret[$myrow['ratingid']] = $entry;
            }
            unset($entry);
        }

        return $ret;
    }

    /**
     * insert a new entry in the database
     *
     * @param \XoopsObject $entry        reference to the {@link Votedata}
     *                                   object
     * @param  bool        $force
     * @return bool               FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $entry, $force = false)
    {
        if (mb_strtolower(get_class($entry)) !== mb_strtolower(Votedata::class)) {
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
            $ratingid = $this->db->genId($this->db->prefix('sbvotedata') . '_ratingid_seq');
            $sql      = sprintf('INSERT INTO `%s` (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES (%u, %u, %u, %u, %s, %u)', $this->db->prefix('sbvotedata'), $ratingid, $lid, $ratinguser, $rating, $this->db->quoteString($ratinghostname), $ratingtimestamp);
        } else {
            $sql = sprintf('UPDATE `%s` SET lid = %u, ratinguser = %u, rating = %u, ratinghostname = %s, ratingtimestamp = %uratingtimestamp WHERE ratingid = %u', $this->db->prefix('sbvotedata'), $ratinguser, $rating, $this->db->quoteString($ratinghostname), $ratingtimestamp, $ratingid);
        }
        if ($force) {
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
     * @param \XoopsObject $entry reference to the entry to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $entry, $force = false)
    {
        if (mb_strtolower(get_class($entry)) !== mb_strtolower(Votedata::class)) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE ratingid = %u', $this->db->prefix('sbvotedata'), $entry->getVar('ratingid'));
        if ($force) {
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
     * delete entry from the database
     *
     * @param  object $criteria {@link CriteriaElement} conditions to be match
     * @param  bool   $force
     * @return bool   FALSE if failed.
     */
    public function deleteEntrys($criteria = null, $force = false)
    {
        $sql = sprintf('DELETE FROM `%s` ', $this->db->prefix('sbvotedata'));
        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ($force) {
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
     * @param  \CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of entrys
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('sbvotedata');

        if (isset($criteria) && $criteria instanceof \CriteriaElement) {
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
     * updates a single field in a Votedata record
     *
     * @param  object $entry      reference to the {@link Votedata} object
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @param  bool   $force
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateByField($entry, $fieldName, $fieldValue, $force = false)
    {
        if (mb_strtolower(get_class($entry)) !== mb_strtolower(Votedata::class)) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);

        return $this->insert($entry, $force);
    }
}
