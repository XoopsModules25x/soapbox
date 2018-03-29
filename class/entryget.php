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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

use XoopsModules\Soapbox;
/** @var Soapbox\Helper $helper */
$helper = Soapbox\Helper::getInstance();

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
$moduleDirName = basename(dirname(__DIR__));
if ('soapbox' !== $moduleDirName && '' !== $moduleDirName && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES | ENT_HTML5));
}

require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/class/sbarticles.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/class/sbcolumns.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/class/sbvotedata.php';

require_once XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/class/Utility.php';

/**
 * Soapbox entrydata handler class.
 * This class provides simple interface (a facade class) for handling sbarticles/sbcolumns/sbvotedata
 * entrydata.
 *
 *
 * @author  domifara
 * @package modules
 */
class SoapboxEntrygetHandler extends XoopsPersistableObjectHandler
{
    /**#@+
     * holds reference to entry  handler(DAO) class
     * @access private
     */
    protected $sbArticleHandler;

    /**
     * holds reference to user handler(DAO) class
     */
    protected $sbColumnHandler;

    /**
     * holds reference to membership handler(DAO) class
     */
    protected $sbVoteHandler;

    /**
     * holds temporary module_id
     */
    protected $moduleId;
    protected $moduleDirName;
    /**#@-*/

    public $total_getArticlesAllPermcheck;
    public $total_getColumnsAllPermcheck;

    public $total_getArticlesByColumnID;
    public $total_getVotedatasByArticleID;
    public $total_getColumnsByAuthor;

    /**
     * constructor
     * @param XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db);
        $moduleDirName = basename(dirname(__DIR__));
        if ('soapbox' !== $moduleDirName && '' !== $moduleDirName && !preg_match('/^(\D+)(\d*)$/', $moduleDirName)) {
            echo('invalid dirname: ' . htmlspecialchars($moduleDirName, ENT_QUOTES | ENT_HTML5));
        }
        $this->sbArticleHandler = new SoapboxSbarticlesHandler($db);
        $this->sbColumnHandler  = new SoapboxSbcolumnsHandler($db);
        $this->sbVoteHandler    = new SoapboxSbvotedataHandler($db);
        $_mymoduleHandler       = xoops_getHandler('module');
        $_mymodule              = $_mymoduleHandler->getByDirname($moduleDirName);
        if (!is_object($_mymodule)) {
            exit('not found dirname');
        }
        $this->moduleDirName = $moduleDirName;
        $this->moduleId      = $_mymodule->getVar('mid');
    }

    /**
     * retrieve a Article
     *
     * @param  int $id ID for the Article
     * @return SoapboxSbarticles SoapboxSbarticles reference to the Article
     */
    public function &getArticle($id)
    {
        $ret = $this->sbArticleHandler->get($id);

        return $ret;
    }

    /**
     * retrieve a Column
     *
     * @param  int $id ID for the Article
     * @return SoapboxSbarticles SoapboxSbarticles reference to the Column
     */
    public function &getColumn($id)
    {
        $ret = $this->sbColumnHandler->get($id);

        return $ret;
    }

    /**
     * retrieve a Votedata
     *
     * @param  int $id ID for the Article
     * @return SoapboxSbvotedata SoapboxSbvotedata reference to the Votedata
     */
    public function &getVotedata($id)
    {
        $ret = $this->sbVoteHandler->get($id);

        return $ret;
    }

    /**
     * retrieve Articles from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key use the Article's ID as key for the array?
     * @return array  array of {@link SoapboxSbarticles} objects
     */
    public function &getArticles(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret = $this->sbArticleHandler->getObjects($criteria, $id_as_key);

        return $ret;
    }

    /**
     * retrieve Columns from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key use the Column's ID as key for the array?
     * @return array  array of {@link SoapboxSbcolumns} objects
     */
    public function &getColumns(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret = $this->sbColumnHandler->getObjects($criteria, $id_as_key);

        return $ret;
    }

    /**
     * retrieve Votedatas from the database
     *
     * @param  CriteriaElement $criteria  {@link CriteriaElement}
     * @param  bool            $id_as_key use the Votedata's ID as key for the array?
     * @return array  array of {@link SoapboxSbvotedata} objects
     */
    public function &getVotedatas(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret = $this->sbVoteHandler->getObjects($criteria, $id_as_key);

        return $ret;
    }

    /**
     * count Article matching certain conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return int
     */
    public function getArticleCount(CriteriaElement $criteria = null)
    {
        return $this->sbArticleHandler->getCount($criteria);
    }

    /**
     * count Column matching certain conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return int
     */
    public function getColumnCount(CriteriaElement $criteria = null)
    {
        return $this->sbColumnHandler->getCount($criteria);
    }

    /**
     * count Votedata matching certain conditions
     *
     * @param  CriteriaElement $criteria {@link CriteriaElement} object
     * @return int
     */
    public function getVotedataCount(CriteriaElement $criteria = null)
    {
        return $this->sbVoteHandler->getCount($criteria);
    }

    /**
     * array return . from int or objects
     *
     * @param $sbcolumns is int , array or {@link SoapboxSbcolumns} objec
     * @return array ( int columnID's)
     */
    public function getColumnsItemIDs($sbcolumns)
    {
        $ret       = [];
        $columnIDs = [];
        if (!isset($sbcolumns) || empty($sbcolumns)) {
            return $ret;
        }
        if (is_object($sbcolumns)) {
            if (strtolower(get_class($sbcolumns)) === strtolower('SoapboxSbcolumns')) {
                $columnIDs[] = $sbcolumns->getVar('columnID');
            }
        } else {
            if (is_array($sbcolumns)) {
                if (0 === count($sbcolumns)) {
                    return $ret;
                }
                $sbcolumns = array_unique($sbcolumns);
                foreach ($sbcolumns as $k => $v) {
                    if (is_object($v)) {
                        if (strtolower(get_class($v)) === strtolower('SoapboxSbcolumns')) {
                            $columnIDs[] = $v->getVar('columnID');
                        }
                    } else {
                        $columnIDs[] = (int)$v;
                    }
                }
            } else {
                $columnIDs[] = (int)$sbcolumns;
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
    public function getArticlesItemIDs($sbarticles)
    {
        $ret        = [];
        $articleIDs = [];
        if (!isset($sbarticles) || empty($sbarticles)) {
            return $ret;
        }
        if (is_object($sbarticles)) {
            if (strtolower(get_class($sbarticles)) === strtolower('SoapboxSbarticles')) {
                $articleIDs[] = $sbarticles->getVar('articleID');
            }
        } else {
            if (is_array($sbarticles)) {
                if (0 === count($sbarticles)) {
                    return $ret;
                }
                $sbarticles = array_unique($sbarticles);
                foreach ($sbarticles as $k => $v) {
                    if (is_object($v)) {
                        if (strtolower(get_class($v)) === strtolower('SoapboxSbarticles')) {
                            $articleIDs[] = $v->getVar('articleID');
                        }
                    } else {
                        $articleIDs[] = (int)$v;
                    }
                }
            } else {
                $articleIDs[] = (int)$sbarticles;
            }
        }
        $ret = array_unique($articleIDs);

        return $ret;
    }

    /**
     * get sbcolumns objects with pemission check , sort
     *
     * @param int       $limit        number of records to return
     * @param int       $start        offset of first record to return
     * @param bool      $checkRight   true is with pemission check
     * @param string    $sortname     sort oder by filed name
     * @param string    $sortorder    sort oder by option (one filed name)
     * @param int|array $sbcolumns    is select int columnID or array columnIDs ,object or objects
     * @param int|array $NOTsbcolumns is no select columnID or array columnIDs ,object or objects
     * @param bool      $id_as_key    use the Column's ID as key for the array?
     * @set int total count of entrys to total_getColumnsAllPermcheck
     * @return array array of {@link SoapboxSbcolumns} objects
     */
    public function &getColumnsAllPermcheck(
        $limit = 0,
        $start = 0,
        $checkRight = true,
        $sortname = 'weight',
        $sortorder = 'ASC',
        $sbcolumns = null,
        $NOTsbcolumns = null,
        $id_as_key = false
    ) {
        global $xoopsUser;
        $ret                                = [];
        $this->total_getColumnsAllPermcheck = 0;
        $groups                             = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $columnIDs                          = [];
        $notcolumnIDs                       = [];
        $can_read_columnIDs                 = [];
        //if obect -- change --> array
        if (isset($sbcolumns)) {
            $columnIDs = $this->getColumnsItemIDs($sbcolumns);
        }
        //if obect -- change --> array
        if (isset($NOTsbcolumns)) {
            $notcolumnIDs = $this->getColumnsItemIDs($NOTsbcolumns);
        }
        if ($checkRight) {
            $gperm_name         = 'Column Permissions';
            $gpermHandler       = xoops_getHandler('groupperm');
            $can_read_columnIDs = $gpermHandler->getItemIds($gperm_name, $groups, $this->moduleId);
        }
        //--------------------------
        $criteria      = new \CriteriaCompo();
        $criteria_used = false;
        if (!empty($columnIDs) && count($columnIDs) > 0) {
            $criteria->add(new \Criteria('columnID', '(' . implode(',', array_unique($columnIDs)) . ')', 'IN'));
            $criteria_used = true;
        }
        if (!empty($notcolumnIDs) && count($notcolumnIDs) > 0) {
            $criteria->add(new \Criteria('columnID', '(' . implode(',', array_unique($notcolumnIDs)) . ')', 'NOT IN'));
            $criteria_used = true;
        }
        if (!empty($can_read_columnIDs) && count($can_read_columnIDs) > 0) {
            $criteria->add(new \Criteria('columnID', '(' . implode(',', array_unique($can_read_columnIDs)) . ')', 'IN'));
            $criteria_used = true;
        }
        //------ hold all count
        if ($criteria_used) {
            $this->total_getColumnsAllPermcheck = $this->getColumnCount($criteria);
        } else {
            $this->total_getColumnsAllPermcheck = $this->getColumnCount();
        }
        if (empty($this->total_getColumnsAllPermcheck)) {
            return $ret;
        }
        if (isset($sortname) && '' !== trim($sortname)) {
            $criteria->setSort($sortname);
        }
        if (isset($sortorder) && '' !== trim($sortorder)) {
            $criteria->setOrder($sortorder);
        }
        $criteria->setLimit((int)$limit);
        $criteria->setStart((int)$start);
        $ret =& $this->getColumns($criteria, $id_as_key);

        unset($criteria);

        return $ret;
        //-------------------------------------
    }

    /**
     * get sbcolumns objects with pemission check , sort
     *
     * @param int    $limit            number of records to return
     * @param int    $start            offset of first record to return
     * @param bool   $checkRight       true is with pemission check
     * @param bool   $published        true is with datesub check
     * @param int    $submit           for submit check where submit = $submit
     * @param int    $offline          for offline check where offline = $offline
     * @param int    $block            for block check where block = $block
     * @param string $sortname         sort oder by filed name
     * @param string $sortorder        sort oder by option (one filed name)
     * @param var    $select_sbcolumns is select int columnID or array columnIDs ,object or objects
     * @param var    $NOTsbarticles    is no select articleID or array articleIDs ,object or objects
     * @param bool   $approve_submit   with author articles of column non check else offline
     * @param bool   $id_as_key        use the articleID's ID as key for the array?
     *
     * @set int total count of entrys to total_getArticlesAllPermcheck
     * @return array array of {@link SoapboxSbarticles} objects
     */
    public function &getArticlesAllPermcheck(
        $limit = 0,
        $start = 0,
        $checkRight = true,
        $published = true,
        $submit = 0,
        $offline = 0,
        $block = null,
        $sortname = 'datesub',
        $sortorder = 'DESC',
        $select_sbcolumns = null,
        $NOTsbarticles = null,
        $approve_submit = false,
        $id_as_key = false
    ) {
        global $xoopsUser;
        $ret                                 = [];
        $this->total_getArticlesAllPermcheck = 0;
        //getColmuns
        $can_read_columnIDs      = [];
        $can_read_column_authors = [];
        $NOTarticleIDs           = [];
        if ($checkRight || isset($select_sbcolumns) || $approve_submit) {
            //get category object
            $_sbcolumns_arr =& $this->getColumnsAllPermcheck(0, 0, $checkRight, null, null, $select_sbcolumns, null, true);
            if (empty($_sbcolumns_arr) || 0 === count($_sbcolumns_arr)) {
                return $ret;
            }
            foreach ($_sbcolumns_arr as $key => $_sbcolumn) {
                $can_read_columnIDs[] = $_sbcolumn->getVar('columnID');
                if (is_object($xoopsUser)) {
                    if ($xoopsUser->isAdmin($this->moduleId)
                        || $xoopsUser->getVar('uid') === $_sbcolumn->getVar('author')) {
                        $can_read_column_authors[] = $_sbcolumn->getVar('columnID');
                    }
                }
            }
            if (empty($can_read_columnIDs)) {
                return $ret;
            }
        } else {
            //get category object all
            $_sbcolumns_arr =& $this->getColumns(null, true);
        }
        //getArticles
        $criteria             = new \CriteriaCompo();
        $criteria_used        = false;
        $criteria_public      = new \CriteriaCompo();
        $criteria_public_used = false;
        if (count($can_read_columnIDs) > 0) {
            $criteria_public->add(new \Criteria('columnID', '(' . implode(',', array_unique($can_read_columnIDs)) . ')', 'IN'));
            $criteria_public_used = true;
        }
        if ($checkRight) {
            if ($published) {
                $criteria_public->add(new \Criteria('datesub', time(), '<'));
                $criteria_public->add(new \Criteria('datesub', 0, '>'));
                $criteria_public_used = true;
            }
        }
        //------
        if (isset($submit)) {
            $criteria_public->add(new \Criteria('submit', (int)$submit));
            $criteria_public_used = true;
        }
        if (isset($offline)) {
            $criteria_public->add(new \Criteria('offline', (int)$offline));
            $criteria_public_used = true;
        }
        if (isset($block)) {
            $criteria_public->add(new \Criteria('block', (int)$block));
            $criteria_public_used = true;
        }
        if (isset($NOTsbarticles)) {
            $notarticleIDs = $this->getColumnsItemIDs($NOTsbarticles);
            $criteria_public->add(new \Criteria('articleID', '(' . implode(',', array_unique($notarticleIDs)) . ')', 'NOT IN'));
            $criteria_public_used = true;
        }

        if ($criteria_public_used) {
            $criteria      = new \CriteriaCompo($criteria_public);
            $criteria_used = true;
        }
        unset($criteria_public);

        // approve submit for column_authors
        if ($approve_submit && count($can_read_column_authors) > 0) {
            $crit_approve_submit = new \CriteriaCompo();
            $crit_approve_submit->add(new \Criteria('columnID', '(' . implode(',', array_unique($can_read_column_authors)) . ')', 'IN'));
            if (isset($NOTsbarticles)) {
                $notarticleIDs = $this->getColumnsItemIDs($NOTsbarticles);
                if (count($notarticleIDs) > 0) {
                    $crit_approve_submit->add(new \Criteria('articleID', '(' . implode(',', array_unique($notarticleIDs)) . ')', 'NOT IN'));
                }
            }
            //            $crit_approve_submit->add(new \Criteria( 'submit', 1 ));
            $crit_approve_submit->add(new \Criteria('offline', 0));
            $criteria->add($crit_approve_submit, 'OR');
            $criteria_used = true;
            unset($crit_approve_submit);
        }
        //------
        if ($criteria_public_used) {
            $this->total_getArticlesAllPermcheck = $this->getArticleCount($criteria);
        } else {
            $this->total_getArticlesAllPermcheck = $this->getArticleCount();
        }
        if (empty($this->total_getArticlesAllPermcheck)) {
            return $ret;
        } else {
            if (isset($sortname) && '' !== trim($sortname)) {
                $criteria->setSort($sortname);
            }
            if (isset($sortorder) && '' !== trim($sortorder)) {
                $criteria->setOrder($sortorder);
            }
            $criteria->setLimit((int)$limit);
            $criteria->setStart((int)$start);
            $sbarticle_arr =& $this->getArticles($criteria, $id_as_key);
            foreach ($sbarticle_arr as $k => $sbarticle) {
                $sbarticle_arr[$k]->_sbcolumns = $_sbcolumns_arr[$sbarticle->getVar('columnID')];
            }
        }
        unset($criteria);

        return $sbarticle_arr;
        //-------------------------------------
    }

    /**
     * get object with check Perm a entry
     *
     * @param        $id
     * @param  bool  $checkRight
     * @param  bool  $approve_submit
     * @return mixed reference to the {@link SoapboxSbarticles} object, FALSE if failed
     *                              object, FALSE if failed
     * @internal param int $articleID articleID of the entry
     * @internal param bool $force
     */
    public function &getArticleOnePermcheck($id, $checkRight = true, $approve_submit = false)
    {
        global $xoopsUser;
        $ret       = false;
        $sbarticle =& $this->getArticle($id);
        if (!is_object($sbarticle)) {
            return $ret;
        }
        //        $gperm_name = 'Column Permissions';
        //        $groups = ( is_object($xoopsUser) ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;
        //        $gpermHandler = xoops_getHandler( 'groupperm' );
        //        if ( !$gpermHandler -> checkRight( $gperm_name,$sbarticle->getVar('columnID'), $groups, $this->moduleId ) ) {
        //            return $ret;
        //        }
        //get category object
        $_sbcolumns_arr =& $this->getColumnsAllPermcheck(1, 0, $checkRight, null, null, $sbarticle->getVar('columnID'), null, true);
        if ($checkRight) {
            if (empty($_sbcolumns_arr) || 0 === count($_sbcolumns_arr)) {
                return $ret;
            }
            $sbarticle->_sbcolumns = $_sbcolumns_arr[$sbarticle->getVar('columnID')];
            if (0 !== $sbarticle->getVar('offline')) {
                return $ret;
            }
            if (is_object($xoopsUser)) {
                if ($approve_submit) {
                    if ($xoopsUser->isAdmin($this->moduleId)
                        || $xoopsUser->getVar('uid') === $sbarticle->_sbcolumns->getVar('author')) {
                        //true
                        $ret = $sbarticle;

                        return $ret;
                    }
                }
            }
            if (0 === $sbarticle->getVar('datesub')) {
                return $ret;
            }
            if ($sbarticle->getVar('datesub') > time()) {
                return $ret;
            }
            if (0 !== $sbarticle->getVar('submit')) {
                return $ret;
            }
        }
        //true
        $ret = $sbarticle;

        return $ret;
    }

    //----------------------------------------------------------------------

    /**
     * get a list of Articles belonging to a column
     *
     * @param  int  $columnID  ID of the Column
     * @param  bool $asobject  return the users as objects?
     * @param  int  $limit     number of users to return
     * @param  int  $start     index of the first user to return
     * @param  null $sortname
     * @param  null $sortorder
     * @return array Array of {@link SoapboxSbarticles} objects (if $asobject is TRUE)
     *                         objects (if $asobject is TRUE)
     */
    public function &getArticlesByColumnID(
        $columnID,
        $asobject = false,
        $limit = 0,
        $start = 0,
        $sortname = null,
        $sortorder = null
    ) {
        $ret                               = [];
        $this->total_getArticlesByColumnID = 0;

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('columnID', $columnID));
        $this->total_getArticlesByColumnID = $this->getArticleCount($criteria);
        if (empty($this->total_getArticlesByColumnID) || 0 === $this->total_getArticlesByColumnID) {
            return $ret;
        }
        if (isset($sortname) && '' !== trim($sortname)) {
            $criteria->setSort($sortname);
        }
        if (isset($sortorder) && '' !== trim($sortorder)) {
            $criteria->setOrder($sortorder);
        }
        $criteria->setLimit((int)$limit);
        $criteria->setStart((int)$start);
        $sbarticle_arr =& $this->getArticles($criteria, true);
        unset($criteria);
        if (empty($sbarticle_arr) || 0 === count($sbarticle_arr)) {
            return $ret;
        }
        if ($asobject) {
            $ret = $sbarticle_arr;
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
     * @param  int  $articleID ID of the Article
     * @param  bool $asobject  return the users as objects?
     * @param  int  $limit     number of users to return
     * @param  int  $start     index of the first user to return
     * @param  null $sortname
     * @param  null $sortorder
     * @return array Array of {@link SoapboxSbvotedata} objects (if $asobject is TRUE)
     *                         objects (if $asobject is TRUE)
     */
    public function &getVotedatasByArticleID(
        $articleID,
        $asobject = false,
        $limit = 0,
        $start = 0,
        $sortname = null,
        $sortorder = null
    ) {
        $ret                                 = [];
        $this->total_getVotedatasByArticleID = 0;
        $criteria                            = new \CriteriaCompo();
        $criteria->add(new \Criteria('lid', $articleID));
        $this->total_getVotedatasByArticleID = $this->getVotedataCount($criteria);
        if (empty($this->total_getVotedatasByArticleID) || 0 === $this->total_getVotedatasByArticleID) {
            return $ret;
        }
        if (isset($sortname) && '' !== trim($sortname)) {
            $criteria->setSort($sortname);
        }
        if (isset($sortorder) && '' !== trim($sortorder)) {
            $criteria->setOrder($sortorder);
        }
        $criteria->setLimit((int)$limit);
        $criteria->setStart((int)$start);
        $sbvotedata_arr =& $this->getVotedatas($criteria, true);
        unset($criteria);
        if (empty($sbvotedata_arr) || 0 === count($sbvotedata_arr)) {
            return $ret;
        }
        if ($asobject) {
            $ret = $sbvotedata_arr;
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
     * @param  int  $user_id  ID of the user
     * @param  bool $asobject return groups as {@link SoapboxSbcolumns} objects or arrays?
     * @param  int  $limit
     * @param  int  $start
     * @param  null $sortname
     * @param  null $sortorder
     * @return array array of objects or arrays
     */
    public function &getColumnsByAuthor(
        $user_id,
        $asobject = false,
        $limit = 0,
        $start = 0,
        $sortname = null,
        $sortorder = null
    ) {
        $ret                            = [];
        $this->total_getColumnsByAuthor = 0;
        $criteria                       = new \CriteriaCompo();
        $criteria->add(new \Criteria('author', $user_id));
        $this->total_getColumnsByAuthor = $this->getColumnCount($criteria);
        if (empty($this->total_getColumnsByAuthor) || 0 === $this->total_getColumnsByAuthor) {
            return $ret;
        }
        if (isset($sortname) && '' !== trim($sortname)) {
            $criteria->setSort($sortname);
        }
        if (isset($sortorder) && '' !== trim($sortorder)) {
            $criteria->setOrder($sortorder);
        }
        $criteria->setLimit((int)$limit);
        $criteria->setStart((int)$start);
        $sbcolumns_arr =& $this->getColumns($criteria, true);
        unset($criteria);
        if (empty($sbcolumns_arr) || 0 === count($sbcolumns_arr)) {
            return $ret;
        }
        if ($asobject) {
            $ret = $sbcolumns_arr;
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
     * @param        $_sbarticle_arr
     * @param  bool  $asobject
     * @param  int   $limit
     * @param  int   $start
     * @param  null  $sortname
     * @param  null  $sortorder
     * @return array array of {@link SoapboxSbcolumns} objects
     *                              objects
     * @internal param object $sbarticle reference to the {@link SoapboxSbarticles} object object
     * @internal param bool $id_as_key use the columnID as key for the array?
     */
    public function &getColumnsByArticles(
        $_sbarticle_arr,
        $asobject = false,
        $limit = 0,
        $start = 0,
        $sortname = null,
        $sortorder = null
    ) {
        $ret       = [];
        $columnIDs = [];
        if (is_array($_sbarticle_arr)) {
            foreach ($_sbarticle_arr as $sbarticle) {
                if (strtolower(get_class($sbarticle)) !== strtolower('SoapboxSbarticles')) {
                    $columnIDs[] = $sbarticle->getVar('columnID');
                }
            }
        } else {
            if (strtolower(get_class($sbarticle)) !== strtolower('SoapboxSbarticles')) {
                $columnIDs[] = $_sbarticle_arr->getVar('columnID');
            }
        }
        if (!empty($columnIDs) && count($columnIDs) > 0) {
            return $ret;
        }
        $columnIDs = array_unique($columnIDs);

        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('columnID', '(' . implode(',', array_unique($columnIDs)) . ')', 'IN'));
        if (isset($sortname) && '' !== trim($sortname)) {
            $criteria->setSort($sortname);
        }
        if (isset($sortorder) && '' !== trim($sortorder)) {
            $criteria->setOrder($sortorder);
        }
        $criteria->setLimit((int)$limit);
        $criteria->setStart((int)$start);
        $sbcolumns_arr =& $this->getColumns($criteria, true);
        unset($criteria);
        if (empty($sbcolumns_arr) || 0 === count($sbcolumns_arr)) {
            return $ret;
        }
        if ($asobject) {
            $ret = $sbcolumns_arr;
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
     * @param  int $articleID ID of the group
     * @return int
     */
    public function getVotedataCountByArticleID($articleID)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('lid', $articleID));

        return $this->getVotedataCount($criteria);
    }

    /**
     * count Article belonging to a columnID
     *
     * @param $columnID
     * @return int
     * @internal param int $articleID ID of the group
     */
    public function getArticleCountByColumnID($columnID)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('columnID', $columnID));

        return $this->getArticleCount($criteria);
    }

    /**
     * update count up hit's counter of sbarticles obects ,with author user check
     *
     * @param  SoapboxSbarticles $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param  bool              $force
     * @return bool   FALSE if failed, TRUE
     */
    public function getUpArticlecount(SoapboxSbarticles $sbarticle, $force = false)
    {
        global $xoopsUser;
        /** @var Soapbox\Helper $helper */
        $helper = Soapbox\Helper::getInstance();

        if (strtolower(get_class($sbarticle)) !== strtolower('SoapboxSbarticles')) {
            return false;
        }
        $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        //update count
        $hitcount_update = false;
        if (XOOPS_GROUP_ANONYMOUS === $groups) {
            $hitcount_update = true;
        } else {
            if (1 === $helper->getConfig('adminhits')) {
                $hitcount_update = true;
            } else {
                if ($xoopsUser->isAdmin($this->moduleId)) {
                    $hitcount_update = false;
                } else {
                    $hitcount_update = false;
                    if ($sbarticle->getVar('uid') !== $xoopsUser->uid()) {
                        $hitcount_update = true;
                    }
                }
            }
        }
        if ($hitcount_update) {
            $hitcount = $sbarticle->getVar('counter') + 1;

            return $this->sbArticleHandler->updateByField($sbarticle, 'counter', $hitcount, $force);
        }

        return false;
    }

    /**
     * get edit icon display html layout for admin or author
     *
     * @param  SoapboxSbarticles $sbarticle reference to the {@link SoapboxSbarticles} object
     * @param                    $sbcolumns
     * @return string (html tags)
     */
    public function getadminlinks(SoapboxSbarticles $sbarticle, &$sbcolumns)
    {
        global $xoopsUser, $xoopsModule;
        $pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);
        $myts       = MyTextSanitizer:: getInstance();
        // Functional links
        $ret = '';
        if (is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($this->moduleId)) {
                if (0 !== $sbarticle->getVar('submit')) {
                    $ret = '<a href="'
                           . XOOPS_URL
                           . '/modules/'
                           . $this->moduleDirName
                           . '/admin/submissions.php?op=mod&articleID='
                           . $sbarticle->getVar('articleID')
                           . '" target="_blank">'
                           . "<img src='"
                           . $pathIcon16
                           . "/edit.png' border=\"0\" alt=\""
                           . _MD_SOAPBOX_EDITART
                           . '" width="16" height="16">'
                           . '</a>&nbsp;';
                } else {
                    $ret = '<a href="'
                           . XOOPS_URL
                           . '/modules/'
                           . $this->moduleDirName
                           . '/admin/article.php?op=mod&articleID='
                           . $sbarticle->getVar('articleID')
                           . '" target="_blank">'
                           . "<img src='"
                           . $pathIcon16
                           . "/edit.png' border=\"0\" alt=\""
                           . _MD_SOAPBOX_EDITART
                           . '" width="16" height="16">'
                           . '</a>&nbsp;';
                }
                $ret .= '<a href="'
                        . XOOPS_URL
                        . '/modules/'
                        . $this->moduleDirName
                        . '/admin/article.php?op=del&articleID='
                        . $sbarticle->getVar('articleID')
                        . '" target="_blank">'
                        . "<img src='"
                        . $pathIcon16
                        . "/delete.png' border=\"0\" alt=\""
                        . _MD_SOAPBOX_DELART
                        . '" width="16" height="16">'
                        . '</a>&nbsp;';
            } elseif ($xoopsUser->uid() === $sbcolumns->getVar('author')) {
                $ret = '<a href="'
                       . XOOPS_URL
                       . '/modules/'
                       . $this->moduleDirName
                       . '/submit.php?op=edit&articleID='
                       . $sbarticle->getVar('articleID')
                       . '" target="_blank">'
                       . "<img src='"
                       . $pathIcon16
                       . "/edit.png' border=\"0\" alt=\""
                       . _MD_SOAPBOX_EDITART
                       . '" width="16" height="16">'
                       . '</a>&nbsp;';
            } else {
                $ret = '';
            }
        }

        return $ret;
    }

    /**
     * get print ,mail icon html layout for guest or nomal user
     *
     * @param  SoapboxSbarticles $sbarticle reference to the {@link SoapboxSbarticles} object
     * @return string (html tags)
     */
    public function getuserlinks(SoapboxSbarticles $sbarticle)
    {
        global $xoopsConfig, $xoopsModule;
        $pathIcon16 = Xmf\Module\Admin::iconUrl('', 16);

        $myts = MyTextSanitizer:: getInstance();
        // Functional links
        $ret            = '';
        $mbmail_subject = sprintf(_MD_SOAPBOX_INTART, $xoopsConfig['sitename']);
        $mbmail_body    = sprintf(_MD_SOAPBOX_INTARTFOUND, $xoopsConfig['sitename']);
        $al             = SoapboxUtility::getAcceptLang();
        if ('ja' === $al) {
            if (function_exists('mb_convert_encoding') && function_exists('mb_encode_mimeheader')
                && @mb_internal_encoding(_CHARSET)) {
                $mbmail_subject = mb_convert_encoding($mbmail_subject, 'SJIS', _CHARSET);
                $mbmail_body    = mb_convert_encoding($mbmail_body, 'SJIS', _CHARSET);
            }
        }
        $mbmail_subject = rawurlencode($mbmail_subject);
        $mbmail_body    = rawurlencode($mbmail_body);
        $ret            = '<a href="'
                          . XOOPS_URL
                          . '/modules/'
                          . $this->moduleDirName
                          . '/print.php?articleID='
                          . $sbarticle->getVar('articleID')
                          . '" target="_blank">'
                          . "<img src='"
                          . $pathIcon16
                          . "/printer.png' border=\"0\" alt=\""
                          . _MD_SOAPBOX_PRINTART
                          . '" width="16" height="16">'
                          . '</a>&nbsp;'
                          . '<a href="mailto:?subject='
                          . $myts->htmlSpecialChars($mbmail_subject)
                          . '&amp;body='
                          . $myts->htmlSpecialChars($mbmail_body)
                          . ':  '
                          . XOOPS_URL
                          . '/modules/'
                          . $this->moduleDirName
                          . '/article.php?articleID='
                          . $sbarticle->getVar('articleID')
                          . ' " target="_blank">'
                          . "<img src='"
                          . $pathIcon16
                          . "/mail_forward.png' border=\"0\" alt=\""
                          . _MD_SOAPBOX_SENDTOFRIEND
                          . '" width="16" height="16">'
                          . '</a>&nbsp;';

        return $ret;
    }
}
