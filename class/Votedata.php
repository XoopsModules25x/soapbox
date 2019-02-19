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
 * Class Votedata
 */
class Votedata extends \XoopsObject
{
    /**
     * Votedata constructor.
     */
    public function __construct()
    {
        $this->initVar('ratingid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('lid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('rating', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('ratinguser', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('ratinghostname', XOBJ_DTYPE_TXTBOX, '', false, 60);
        $this->initVar('ratingtimestamp', XOBJ_DTYPE_LTIME, 0, false);
        //not in table
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dobr', XOBJ_DTYPE_INT, 1, false);
    }

}
