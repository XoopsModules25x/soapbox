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

/**
 * Class Columns
 */
class Columns extends \XoopsObject
{
    /**
     * Columns constructor.
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
}
