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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

use XoopsModules\Soapbox;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
$moduleDirName = basename(dirname(__DIR__));
if ('soapbox' !== $moduleDirName && '' !== $moduleDirName && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
    echo('invalid dirname: ' . htmlspecialchars($this->mydirname, ENT_QUOTES | ENT_HTML5));
}

/**
 * Soapbox entrydata handler class.
 * This class provides simple interface (a facade class) for handling sbarticles/sbcolumns/sbvotedata
 * entrydata.
 *
 *
 * @author  domifara
 * @package modules
 */
class EntrydataHandler extends Soapbox\EntrygetHandler
{
    /**
     * constructor
     * @param \XoopsDatabase|null $db
     */
    public function __construct(\XoopsDatabase $db = null)
    {
        parent::__construct($db);
        global $moduleDirName;
        $this->sbArticleHandler = new ArticlesHandler($db);
        $this->sbColumnHandler  = new ColumnsHandler($db);
        $this->sbVoteHandler    = new VotedataHandler($db);
        $_mymoduleHandler       = xoops_getHandler('module');
        $_mymodule              = $_mymoduleHandler->getByDirname('soapbox');
        if (!is_object($_mymodule)) {
            exit('not found dirname');
        }
        $this->moduleDirName = $moduleDirName;
        $this->moduleId      = $_mymodule->getVar('mid');
    }

    /**
     * create a new Article
     *
     * @param  bool $isNew
     * @return object Articles reference to the new Article
     */
    public function createArticle($isNew = true)
    {
        $ret = $this->sbArticleHandler->create($isNew);

        return $ret;
    }

    /**
     * create a new Column
     *
     * @param  bool $isNew
     * @return object Columns reference to the new Column
     */
    public function createColumn($isNew = true)
    {
        $ret = $this->sbColumnHandler->create($isNew);

        return $ret;
    }

    /**
     * create a new Votedata
     *
     * @param  bool $isNew
     * @return object Votedata reference to the new Votedata
     */
    public function createVotedata($isNew = true)
    {
        $ret = $this->sbVoteHandler->create($isNew);

        return $ret;
    }

    /**
     * insert a new entry in the database
     *
     * @param  Articles $sbarticle reference to the {@link Articles} object
     * @param  bool     $force
     * @return bool              FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insertArticle(Articles $sbarticle, $force = false)
    {
        if (!$this->sbArticleHandler->insert($sbarticle, $force)) {
            return false;
        }
        // re count ----------------------------------
        $this->updateTotalByColumnID($sbarticle->getVar('columnID'));

        // re count ----------------------------------
        return true;
    }

    /**
     * insert a new Column in the database
     *
     * @param  object $sbcolumn reference to the {@link Columns} object
     * @param  bool   $force
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insertColumn($sbcolumn, $force = false)
    {
        if (!$this->sbColumnHandler->insert($sbcolumn, $force)) {
            return false;
        }

        return true;
    }

    /**
     * insert a new Votedata in the database
     *
     * @param  object $sbvotedata reference to the {@link Votedata} object
     * @param  bool   $force
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insertVotedata($sbvotedata, $force = false)
    {
        if (!$this->sbVoteHandler->insert($sbvotedata, $force)) {
            return false;
        }

        return true;
    }

    /**
     * delete a Article from the database
     *
     * @param  object $sbarticle reference to the Article to delete
     * @param  bool   $force
     * @param  bool   $re_count
     * @return bool   FALSE if failed.
     */
    public function deleteArticle($sbarticle, $force = false, $re_count = true)
    {
        // delete Article
        if (!$this->sbArticleHandler->delete($sbarticle, $force)) {
            return false;
        }
        // delete Votedata
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('lid', $sbarticle->getVar('articleID')));
        $this->sbVoteHandler->deleteEntrys($criteria, $force);
        unset($criteria);
        // delete comments
        xoops_comment_delete($this->moduleId, $sbarticle->getVar('articleID'));
        // re count ----------------------------------
        if ($re_count) {
            $this->updateTotalByColumnID($sbarticle->getVar('columnID'));
        }

        // re count ----------------------------------
        return true;
    }

    /**
     * delete a Column from the database
     *
     * @param  object $sbcolumn reference to the Column to delete
     * @param  bool   $force
     * @return bool   FALSE if failed.
     */
    public function deleteColumn($sbcolumn, $force = false)
    {
        // delete Column
        if (!$this->sbColumnHandler->delete($sbcolumn, $force)) {
            return false;
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('columnID', $sbcolumn->getVar('columnID')));
        $this->deleteArticlesEntrys($criteria, $force, false);
        unset($criteria);

        return true;
    }

    /**
     * delete a Votedata from the database
     *
     * @param  Votedata $sbvotedata reference to the Votedata to delete
     * @param  bool     $force
     * @return bool   FALSE if failed.
     */
    public function deleteVotedata($sbvotedata, $force = false)
    {
        // delete Votedata
        if (!$this->sbVoteHandler->delete($sbvotedata, $force)) {
            return false;
        }

        return true;
    }

    /**
     * delete  entrys from the database
     *
     * @param  \CriteriaElement $criteria {@link CriteriaElement} conditions to be match
     * @param  bool             $force
     * @param  bool             $re_count
     * @return bool   FALSE if failed.
     */
    public function deleteArticlesEntrys(\CriteriaElement $criteria = null, $force = false, $re_count = false)
    {
        $_sbarticles_arr = $this->getArticles($criteria);
        if (empty($_sbarticles_arr) || 0 === count($_sbarticles_arr)) {
            return false;
        }
        foreach ($_sbarticles_arr as $sbarticle) {
            $this->deleteArticle($sbarticle, $force, false);
        }

        return true;
    }

    /**
     * @param       $sbarticle
     * @param  bool $force
     * @return bool
     */
    public function updateRating(&$sbarticle, $force = false) // updates rating data in itemtable for a given item
    {
        if (mb_strtolower(get_class($sbarticle)) !== mb_strtolower(Articles::class)) {
            return false;
        }
        $totalrating = 0.00;
        $votesDB     = 0;
        $finalrating = 0;

        $sbvotedata_arr = $this->getVotedatasByArticleID($sbarticle->getVar('articleID'), true, 0, 0);
        $votesDB        = count($sbvotedata_arr);
        if (empty($sbvotedata_arr) || 0 === $votesDB) {
            return false;
        }
        foreach ($sbvotedata_arr as $sbvotedata) {
            if (is_object($sbvotedata)) {
                $totalrating += $sbvotedata->getVar('rating');
            }
        }
        if (0 !== $totalrating && 0 !== $votesDB) {
            $finalrating = ($totalrating / $votesDB) + 0.00005;
            $finalrating = number_format($finalrating, 4);
        }

        $sbarticle->setVar('rating', $finalrating);
        $sbarticle->setVar('votes', $votesDB);
        if (!$this->insertArticle($sbarticle, $force)) {
            return false;
        }

        return true;
    }

    /**
     * @param       $columnID
     * @param  bool $force
     * @return bool
     */
    public function updateTotalByColumnID($columnID, $force = false)
    {
        // re count ----------------------------------
        $sbcolumns = $this->getColumn($columnID);
        if (is_object($sbcolumns)) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('datesub', time(), '<'));
            $criteria->add(new \Criteria('datesub', 0, '>'));
            $criteria->add(new \Criteria('submit', 0));
            $criteria->add(new \Criteria('offline', 0));
            $sbcolumns->setVar('total', $this->getArticleCount($criteria));
            unset($criteria);
            $this->insertColumn($sbcolumns, $force);
        }

        // re count ----------------------------------
        return true;
    }

    /**
     * updates a single field in a Article record
     *
     * @param  Articles $sbarticle  reference to the {@link Articles} object
     * @param  string   $fieldName  name of the field to update
     * @param  string   $fieldValue updated value for the field
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateArticleByField($sbarticle, $fieldName, $fieldValue)
    {
        return $this->sbArticleHandler->updateByField($sbarticle, $fieldName, $fieldValue);
    }

    /**
     * updates a single field in a Column record
     *
     * @param  Columns $sbcolumns  reference to the {@link Columns} object
     * @param  string  $fieldName  name of the field to update
     * @param  string  $fieldValue updated value for the field
     * @return bool   TRUE if success or unchanged, FALSE on failure
     */
    public function updateColumnByField($sbcolumns, $fieldName, $fieldValue)
    {
        return $this->sbColumnHandler->updateByField($sbcolumns, $fieldName, $fieldValue);
    }

    /**
     * updates a single field in a Votedata record
     *
     * @param         $sbvotedata
     * @param  string $fieldName  name of the field to update
     * @param  string $fieldValue updated value for the field
     * @return bool   TRUE if success or unchanged, FALSE on failure
     * @internal param object $user reference to the {@link Columns} object object
     */
    public function updateVotedataByField(&$sbvotedata, $fieldName, $fieldValue)
    {
        return $this->sbVoteHandler->updateByField($sbvotedata, $fieldName, $fieldValue);
    }

    /**
     * weight update
     *
     * @param  array $weight to match
     * @return bool  FALSE if failed.
     */
    public function reorderColumnsUpdate($weight)
    {
        if (!isset($weight) || empty($weight) || !is_array($weight)) {
            return false;
        }
        foreach ($weight as $columnID => $order) {
            $weight[$columnID] = (int)$order;
        }
        array_unique($weight);
        foreach ($weight as $columnID => $order) {
            if (isset($columnID) && is_numeric($columnID) && isset($order)) {
                $sbcolumn = $this->getColumn($columnID);
                if (is_object($sbcolumn)) {
                    if (!is_numeric($order) || empty($order)) {
                        $order = 0;
                    }
                    $this->updateColumnByField($sbcolumn, 'weight', $order);
                }
            }
        }

        return true;
    }

    /**
     * weight update
     *
     * @param  array $weight to match
     * @return bool  FALSE if failed.
     */
    public function reorderArticlesUpdate($weight)
    {
        if (!isset($weight) || empty($weight) || !is_array($weight)) {
            return false;
        }
        foreach ($weight as $articleID => $order) {
            $weight[$articleID] = (int)$order;
        }
        array_unique($weight);
        foreach ($weight as $articleID => $order) {
            if (isset($articleID) && is_numeric($articleID) && isset($order)) {
                $sbarticle = $this->getArticle($articleID);
                if (is_object($sbarticle)) {
                    if (!is_numeric($order) || empty($order)) {
                        $order = 0;
                    }
                    $this->updateArticleByField($sbarticle, 'weight', $order);
                }
            }
        }

        return true;
    }

    /**
     * newarticleTriggerEvent a category of this columnID from the database
     *
     * @param  object $sbcolumn reference to the category to delete
     * @param  string $events
     * @return bool   FALSE if failed.
     * @internal param bool $force
     */
    public function newColumnTriggerEvent($sbcolumn, $events = 'new_column')
    {
        if (mb_strtolower(get_class($sbcolumn)) !== mb_strtolower(Columns::class)) {
            return false;
        }
        if (1 !== $sbcolumn->getVar('notifypub')) {
            return false;
        }
        // Notify of new link (anywhere) and new link in category
        $tags                = [];
        $tags['COLUMN_NAME'] = $sbcolumn->getVar('name');
        $tags['COLUMN_URL']  = XOOPS_URL . '/modules/' . $this->moduleDirName . '/column.php?columnID=' . $sbcolumn->getVar('columnID');
        /** @var \XoopsNotificationHandler $notificationHandler */
        $notificationHandler = xoops_getHandler('notification');
        $notificationHandler->triggerEvent('global', 0, 'new_column', $tags);

        return true;
    }

    /**
     * new articleTriggerEvent counter a new entry in the database
     *
     * @param         $sbarticle
     * @param  string $events
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     * @internal param object $criteria {@link CriteriaElement} conditions to be match conditions to be match
     */
    public function newArticleTriggerEvent(&$sbarticle, $events = 'new_article')
    {
        if (mb_strtolower(get_class($sbarticle)) !== mb_strtolower(Articles::class)) {
            return false;
        }
        $sbcolumns = $this->getColumn($sbarticle->getVar('columnID'));
        if (!is_object($sbcolumns)) {
            return false;
        }
        // Notify of new link (anywhere) and new link in category
        $tags                 = [];
        $tags['ARTICLE_NAME'] = $sbarticle->getVar('headline');
        $tags['ARTICLE_URL']  = XOOPS_URL . '/modules/' . $this->moduleDirName . '/article.php?articleID=' . $sbarticle->getVar('articleID');
        $tags['COLUMN_NAME']  = $sbcolumns->getVar('name');
        $tags['COLUMN_URL']   = XOOPS_URL . '/modules/' . $this->moduleDirName . '/column.php?columnID=' . $sbarticle->getVar('columnID');
        // Notify of to admin only for approve article_submit
        $tags['WAITINGSTORIES_URL'] = XOOPS_URL . '/modules/' . $this->moduleDirName . '/admin/submissions.php?op=col';
        $notificationHandler        = xoops_getHandler('notification');
        //approve evevt
        if ('article_submit' === $events) {
            $notificationHandler->triggerEvent('global', 0, 'article_submit', $tags);
            $notificationHandler->triggerEvent('column', $sbarticle->getVar('columnID'), 'article_submit', $tags);
        } elseif ('approve' === $events) {
            $notificationHandler->triggerEvent('article', $sbarticle->getVar('articleID'), 'approve', $tags);
        }
        //online
        if (0 === $sbarticle->getVar('offline') && 0 === $sbarticle->getVar('submit')) {
            //when offline ,update offline changed to visible --> event
            if (1 === $sbarticle->pre_offline && 1 === $sbarticle->getVar('notifypub')) {
                $notificationHandler->triggerEvent('global', 0, 'new_article', $tags);
                $notificationHandler->triggerEvent('column', $sbarticle->getVar('columnID'), 'new_article', $tags);
            }
        }

        return true;
    }
}
