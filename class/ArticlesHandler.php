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
if (!defined('XOBJ_SOAPBOX_DTYPE_FLOAT')) {
    define('XOBJ_SOAPBOX_DTYPE_FLOAT', 21);
}


/**
 * Class ArticlesHandler
 */
class ArticlesHandler extends \XoopsPersistableObjectHandler
{
    public $totalarts_AllPermcheck;

    /**
     * create a new entry
     *
     * @param  bool $isNew flag the new objects as "new"?
     * @return Articles Articles
     */
    public function create($isNew = true)
    {
        $sbarticle = new Articles();
        if ($isNew) {
            $sbarticle->setNew();
        }

        return $sbarticle;
    }

    /**
     * retrieve a entry
     *
     * @param  mixed|null $id
     * @param  null       $fields
     * @return mixed      reference to the <a href='psi_element://soapboxEntry'>soapboxEntry</a> object, FALSE if failed
     *                           object, FALSE if failed
     *                           object, FALSE if failed
     * @internal param int $articleID articleID of the entry
     */
    public function get($id = null, $fields = null) //&get($id)
    {
        $ret = false;
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('sbarticles') . " WHERE articleID = '$id'";
            if (!$result = $this->db->query($sql)) {
                return $ret;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 === $numrows) {
                $sbarticle = new Articles();
                $sbarticle->assignVars($this->db->fetchArray($result));
                //pre_offline value buckup
                if ($sbarticle->getVar('offline') || $sbarticle->getVar('submit')) {
                    $sbarticle->pre_offline = 1;
                }

                return $sbarticle;
            }
        }

        return $ret;
    }

    /**
     * retrieve entrys from the database
     *
     * @param  \CriteriaElement $criteria  {@link CriteriaElement} conditions to be match
     * @param  bool            $id_as_key use the articleID as key for the array?
     * @param  bool            $as_object
     * @return array           array of <a href='psi_element://Articles'>Articles</a> objects
     *                                    objects
     */
    public function &getObjects(\CriteriaElement $criteria = null, $id_as_key = false, $as_object = true)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix('sbarticles');
        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
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
            $sbarticle = new Articles();
            $sbarticle->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $sbarticle;
            } else {
                $ret[$myrow['articleID']] = $sbarticle;
            }
            unset($sbarticle);
        }
        $this->db->freeRecordSet($result);

        return $ret;
    }

    /**
     * insert a new entry in the database
     *
     * @param \XoopsObject $sbarticle reference to the {@link Articles}
     *                                object
     * @param  bool        $force
     * @return bool        FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $sbarticle, $force = false)
    {
        if ('soapboxsbarticles' !== strtolower(get_class($sbarticle))) {
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
            $articleID = $this->db->genId($this->db->prefix('sbarticles') . '_articleID_seq');
            $sql       = sprintf(
                'INSERT INTO `%s` (articleID, columnID, headline, lead, bodytext, teaser, uid, submit, datesub, counter, weight, html, smiley, xcodes, breaks, BLOCK, artimage, votes, rating, commentable, offline, notifypub) VALUES (%u, %u, %s, %s, %s, %s, %u, %u, %u, %u, %u, %u, %u, %u, %u, %u, %s, %u, %f, %u, %u, %u )',
                                 $this->db->prefix('sbarticles'),
                $articleID,
                $columnID,
                $this->db->quoteString($headline),
                $this->db->quoteString($lead),
                $this->db->quoteString($bodytext),
                $this->db->quoteString($teaser),
                $uid,
                $submit,
                $datesub,
                $counter,
                $weight,
                $html,
                $smiley,
                $xcodes,
                $breaks,
                                 $block,
                $this->db->quoteString($artimage),
                $votes,
                $rating,
                $commentable,
                $offline,
                $notifypub
            );
        } else {
            $sql = sprintf(
                'UPDATE `%s` SET columnID = %u , headline = %s , lead = %s , bodytext = %s , teaser = %s , uid = %u , submit = %u , datesub = %u , counter = %u , weight = %u , html = %u , smiley = %u , xcodes = %u , breaks = %u , BLOCK = %u , artimage = %s , votes = %u , rating = %f , commentable = %u , offline = %u , notifypub = %u WHERE articleID = %u',
                           $this->db->prefix('sbarticles'),
                $columnID,
                $this->db->quoteString($headline),
                $this->db->quoteString($lead),
                $this->db->quoteString($bodytext),
                $this->db->quoteString($teaser),
                $uid,
                $submit,
                $datesub,
                $counter,
                $weight,
                $html,
                $smiley,
                $xcodes,
                $breaks,
                $block,
                           $this->db->quoteString($artimage),
                $votes,
                $rating,
                $commentable,
                $offline,
                $notifypub,
                $articleID
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
        if (empty($articleID)) {
            $articleID = $this->db->getInsertId();
        }
        $sbarticle->assignVar('articleID', $articleID);

        return true;
    }

    /**
     * delete a entry from the database
     *
     * @param \XoopsObject $sbarticle reference to the entry to delete
     * @param  bool        $force
     * @return bool        FALSE if failed.
     */
    public function delete(\XoopsObject $sbarticle, $force = false)
    {
        global $xoopsModule;
        if (strtolower(get_class($sbarticle)) !== strtolower('Articles')) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE articleID = %u', $this->db->prefix('sbarticles'), $sbarticle->getVar('articleID'));
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
     * count entrys matching a condition
     *
     * @param  \CriteriaElement $criteria {@link CriteriaElement} to match
     * @return int             count of entrys
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('sbarticles');

        if (null !== $criteria && is_subclass_of($criteria, 'CriteriaElement')) {
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
     * updates a single field in a Article record
     *
     * @param  Articles $entry      reference to the {@link Articles} object
     * @param  string            $fieldName  name of the field to update
     * @param  string            $fieldValue updated value for the field
     * @param  bool              $force
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateByField($entry, $fieldName, $fieldValue, $force = false)
    {
        if (strtolower(get_class($entry)) !== strtolower('Articles')) {
            return false;
        }
        $entry->setVar($fieldName, $fieldValue);

        return $this->insert($entry, $force);
    }
}
