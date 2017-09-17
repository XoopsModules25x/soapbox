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
 * @author       XOOPS Development Team
 */

if (!defined('SOAPBOX_MODULE_PATH')) {
    define('SOAPBOX_DIRNAME', basename(dirname(__DIR__)));
    define('SOAPBOX_URL', XOOPS_URL . '/modules/' . SOAPBOX_DIRNAME);
    define('SOAPBOX_IMAGE_URL', SOAPBOX_URL . '/assets/images/');
    define('SOAPBOX_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . SOAPBOX_DIRNAME);
    define('SOAPBOX_IMAGE_PATH', SOAPBOX_ROOT_PATH . '/assets/images');
    define('SOAPBOX_ADMIN_URL', SOAPBOX_URL . '/admin/');
    define('SOAPBOX_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . SOAPBOX_DIRNAME);
    define('SOAPBOX_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . SOAPBOX_DIRNAME);
}
xoops_loadLanguage('common', SOAPBOX_DIRNAME);

require_once SOAPBOX_ROOT_PATH . '/class/utility.php';
//require_once SOAPBOX_ROOT_PATH . '/include/constants.php';
//require_once SOAPBOX_ROOT_PATH . '/include/seo_functions.php';
//require_once SOAPBOX_ROOT_PATH . '/class/metagen.php';
//require_once SOAPBOX_ROOT_PATH . '/class/session.php';
//require_once SOAPBOX_ROOT_PATH . '/class/xoalbum.php';
//require_once SOAPBOX_ROOT_PATH . '/class/request.php';

$debug = false;
//$xoalbum = XoalbumXoalbum::getInstance($debug);
