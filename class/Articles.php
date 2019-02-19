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
 * @author         XOOPS Development Team
 */

use XoopsModules\Soapbox;

// defined('XOOPS_ROOT_PATH') || die('Restricted access');
//require_once XOOPS_ROOT_PATH . '/modules/soapbox/include/cleantags.php';
if (!defined('XOBJ_SOAPBOX_DTYPE_FLOAT')) {
    define('XOBJ_SOAPBOX_DTYPE_FLOAT', 21);
}

/**
 * Class Articles
 */
class Articles extends \XoopsObject
{
    public $pre_offline;
    public $_sbcolumns;

    /**
     * Articles constructor.
     */
    public function __construct()
    {
        $this->initVar('articleID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('columnID', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('headline', XOBJ_DTYPE_TXTBOX, '', false, 255);
        $this->initVar('lead', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('bodytext', XOBJ_DTYPE_OTHER, '', false);
        $this->initVar('teaser', XOBJ_DTYPE_TXTAREA, '', false);
        $this->initVar('uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('submit', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('datesub', XOBJ_DTYPE_LTIME, time(), false);
        $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('html', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('smiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('xcodes', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('breaks', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('block', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('artimage', XOBJ_DTYPE_TXTBOX, 'blank.png', false, 255);
        $this->initVar('votes', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('rating', XOBJ_SOAPBOX_DTYPE_FLOAT, 0.0000, false);
        $this->initVar('commentable', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('offline', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('notifypub', XOBJ_DTYPE_INT, 0, false);

        $this->pre_offline = 1;
        $this->_sbcolumns  = false;
    }

}
