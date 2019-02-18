<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');

defined('XOOPS_ROOT_PATH') || die('Restricted access');

require_once __DIR__ . '/preloads/autoloader.php';

$moduleDirName = basename(__DIR__);
xoops_load('xoopseditorhandler');
$editorHandler = \XoopsEditorHandler::getInstance();
$xoopsUrl = parse_url(XOOPS_URL);

// ------------------- Informations ------------------- //
$modversion = [
    'version'             =>  1.70,
    'module_status'       => 'Beta 2',
    'release_date'        => '2019/02/18',
    'name'                => _MI_SOAPBOX_NAME,
    'description'         => _MI_SOAPBOX_DESC,
    'official'            => 0,
    //1 indicates official XOOPS module supported by XOOPS Dev Team, 0 means 3rd party supported
    'author'              => 'hsalazar, domifara',
    'credits'             => 'XOOPS Development Team, Catzwolf, Mamba, Aerograf',
    'author_mail'         => 'hsalazar@xoops.org',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'help'                => 'page=help',
    // ------------------- Folders & Files -------------------
    'release_info'        => 'Changelog',
    'release_file'        => XOOPS_URL . "/modules/$moduleDirName/docs/changelog.txt",
    'manual'              => 'link to manual file',
    'manual_file'         => XOOPS_URL . "/modules/$moduleDirName/docs/install.txt",
    // images
    'image'               => 'assets/images/logoModule.png',
    'iconsmall'           => 'assets/images/iconsmall.png',
    'iconbig'             => 'assets/images/iconbig.png',
    'dirname'             => $moduleDirName,
    // Local path icons
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    // --------------------About ----------------------------
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb/viewforum.php?forum=28/',
    'support_name'        => 'Support Forum',
    'submit_bug'          => 'https://github.com/XoopsModules25x/' . $moduleDirName . '/issues',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    'author_realname'     => 'Horacio Salazar, domifara',
    'author_email'        => 'hsalazar@xoops.org',
    'warning'             => _MI_SOAPBOX_WARNING,
    'author_word'         => _MI_SOAPBOX_AUTHORMSG,
    // ------------------- Min Requirements -------------------
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    // ------------------- Admin Menu -------------------
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // ------------------- Main Menu -------------------
    'hasMain'             => 1,
//    'sub'                 => [
//        [
//            'name' => _MI_SOAPBOX_VIEW_SEARCH,
//            'url'  => 'index.php'
//        ],
//    ],

    // ------------------- Install/Update -------------------
    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
//    'onUninstall'         => 'include/onuninstall.php',
    // -------------------  PayPal ---------------------------
    'paypal'              => [
        'business'      => 'foundation@xoops.org',
        'item_name'     => 'Donation : ' . _MI_SOAPBOX_NAME,
        'amount'        => 0,
        'currency_code' => 'USD'
    ],
    // ------------------- Search ---------------------------
    'hasSearch'           => 1,
    'search'              => [
        'file' => 'include/search.inc.php',
        'func' => 'sb_search'
    ],

    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
//    'tables'              => [
//        $moduleDirName . '_' . 'XXX',
//        $moduleDirName . '_' . 'XXX',
//        $moduleDirName . '_' . 'XXX',
//        $moduleDirName . '_' . 'XXX',
//        $moduleDirName . '_' . 'XXX',
//        $moduleDirName . '_' . 'XXX',
//    ],
];



// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'sbcolumns';
$modversion['tables'][1] = 'sbarticles';
$modversion['tables'][2] = 'sbvotedata';


// Menu
$modversion['hasMain'] = 1;

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_SOAPBOX_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_SOAPBOX_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_SOAPBOX_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_SOAPBOX_SUPPORT, 'link' => 'page=support'],
];

//Install/Uninstall Functions
$modversion['onInstall']   = 'include/oninstall.php';
$modversion['onUpdate']    = 'include/onupdate.php';
$modversion['onUninstall'] = 'include/onuninstall.php';

global $xoopsDB, $xoopsUser;
$hModule = xoops_getHandler('module');
$i       = 0;
if ($soapModule = $hModule->getByDirname('soapbox')) {
    $gpermHandler = xoops_getHandler('groupperm');
    $hModConfig   = xoops_getHandler('config');

    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    if (is_object($xoopsUser)) {
        $user  = $xoopsUser->getVar('uid');
        $query = $xoopsDB->query('SELECT author FROM ' . $xoopsDB->prefix('sbcolumns') . ' WHERE author = ' . $xoopsUser->getVar('uid'));
        if ($query) {
            $isauthor = $xoopsDB->getRowsNum($query);
            if ($isauthor >= 1 || $xoopsUser->isAdmin($soapModule->getVar('mid'))) {
                ++$i;
                $modversion['sub'][$i]['name'] = _MI_SOAPBOX_SUB_SMNAME1;
                $modversion['sub'][$i]['url']  = 'submit.php?op=add';
            }
        }
    }
    unset($isauthor);
    $module_id  = $soapModule->getVar('mid');
    $soapConfig = $hModConfig->getConfigsByCat(0, $soapModule->getVar('mid'));
    if (is_object($xoopsUser) && isset($soapConfig['colsinmenu']) && 1 === $soapConfig['colsinmenu']) {
        $sql = $xoopsDB->query('SELECT columnID, name FROM ' . $xoopsDB->prefix('sbcolumns') . '  ORDER BY weight');
        if ($sql) {
            while (false !== (list($columnID, $name) = $xoopsDB->fetchRow($sql))) {
                if ($gpermHandler->checkRight('Column Permissions', $columnID, $groups, $module_id)) {
                    ++$i;
                    $modversion['sub'][$i]['name'] = $name;
                    $modversion['sub'][$i]['url']  = 'column.php?columnID=' . $columnID . '';
                }
            }
        }
    }
}

$modversion['blocks'][] = [
    'file'        => 'arts_rated.php',
    'name'        => _MI_SOAPBOX_ARTSRATED,
    'description' => _MI_SOAPBOX_ARTSRATED_DSC,
    'show_func'   => 'b_arts_rated',
    'edit_func'   => 'b_arts_rated_edit',
    'options'     => 'rating|5|65',
    'template'    => 'arts_rated.tpl',
    'can_clone'   => true,
];

$modversion['blocks'][] = [
    'file'        => 'arts_new.php',
    'name'        => _MI_SOAPBOX_ARTSNEW,
    'description' => _MI_SOAPBOX_ARTSNEW_DSC,
    'show_func'   => 'b_arts_new_show',
    'edit_func'   => 'b_arts_new_edit',
    'options'     => 'datesub|5|65',
    'template'    => 'arts_new.tpl',
    'can_clone'   => true,
];

$modversion['blocks'][] = [
    'file'        => 'arts_top.php',
    'name'        => _MI_SOAPBOX_ARTSTOP,
    'description' => _MI_SOAPBOX_ARTSTOP_DSC,
    'show_func'   => 'b_arts_top_show',
    'edit_func'   => 'b_arts_top_edit',
    'options'     => 'counter|5|65',
    'template'    => 'arts_top.tpl',
    'can_clone'   => true,
];

$modversion['blocks'][] = [
    'file'        => 'arts_spot.php',
    'name'        => _MI_SOAPBOX_ARTSPOTLIGHT,
    'description' => _MI_SOAPBOX_ARTSPOTLIGHT_DSC,
    'show_func'   => 'b_arts_spot_show',
    'edit_func'   => 'b_arts_spot_edit',
    'options'     => '1|5|1|1|1|ver|1|datesub|65|0',
    'template'    => 'arts_spot.tpl',
    'can_clone'   => true,
];

$modversion['blocks'][] = [
    'file'        => 'columns_spot.php',
    'name'        => _MI_SOAPBOX_ARTSPOTLIGHT2,
    'description' => _MI_SOAPBOX_ARTSPOTLIGHT2_DSC,
    'show_func'   => 'b_columns_spot_show',
    'edit_func'   => 'b_columns_spot_edit',
    'options'     => '1|5|1|1|1|ver|1|datesub|65|0',
    'template'    => 'columns_spot.tpl',
    'can_clone'   => true,
];

// Templates

$modversion['templates'] = [
    ['file' => 'sb_column.tpl', 'description' => 'Display columns'],
    ['file' => 'sb_index.tpl', 'description' => 'Display index'],
    ['file' => 'sb_article.tpl', 'description' => 'Display article'],
];

// Config Settings (only for modules that need config settings generated automatically)

$modversion['config'][] = [
    'name'        => 'allowsubmit',
    'title'       => '_MI_SOAPBOX_ALLOWSUBMIT',
    'description' => '_MI_SOAPBOX_ALLOWSUBMITDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'autoapprove',
    'title'       => '_MI_SOAPBOX_AUTOAPPROVE',
    'description' => '_MI_SOAPBOX_AUTOAPPROVEDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'adminhits',
    'title'       => '_MI_SOAPBOX_ALLOWADMINHITS',
    'description' => '_MI_SOAPBOX_ALLOWADMINHITSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'perpage',
    'title'       => '_MI_SOAPBOX_PERPAGE',
    'description' => '_MI_SOAPBOX_PERPAGEDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50
    ],
];

$modversion['config'][] = [
    'name'        => 'indexperpage',
    'title'       => '_MI_SOAPBOX_PERPAGEINDEX',
    'description' => '_MI_SOAPBOX_PERPAGEINDEXDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 5,
    'options'     => [
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50
    ],
];

$modversion['config'][] = [
    'name'        => 'sbimgdir',
    'title'       => '_MI_SOAPBOX_IMGDIR',
    'description' => '_MI_SOAPBOX_IMGDIRDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'modules/soapbox/assets/images',
];

$modversion['config'][] = [
    'name'        => 'sbuploaddir',
    'title'       => '_MI_SOAPBOX_UPLOADDIR',
    'description' => '_MI_SOAPBOX_UPLOADDIRDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => 'modules/soapbox/assets/images/uploads',
];

$modversion['config'][] = [
    'name'        => 'maximgwidth',
    'title'       => '_MI_SOAPBOX_IMGWIDTH',
    'description' => '_MI_SOAPBOX_IMGWIDTHDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 800,
];

$modversion['config'][] = [
    'name'        => 'maximgheight',
    'title'       => '_MI_SOAPBOX_IMGHEIGHT',
    'description' => '_MI_SOAPBOX_IMGHEIGHTDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 800,
];

$modversion['config'][] = [
    'name'        => 'maxfilesize',
    'title'       => '_MI_SOAPBOX_MAXFILESIZE',
    'description' => '_MI_SOAPBOX_MAXFILESIZEDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 250000,
];

$modversion['config'][] = [
    'name'        => 'dateformat',
    'title'       => '_MI_SOAPBOX_DATEFORMAT',
    'description' => '_MI_SOAPBOX_DATEFORMATDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _SHORTDATESTRING, //'d M Y',
];

$modversion['config'][] = [
    'name'        => 'globaldisplaycomments',
    'title'       => '_MI_SOAPBOX_ALLOWCOMMENTS',
    'description' => '_MI_SOAPBOX_ALLOWCOMMENTSDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'morearts',
    'title'       => '_MI_SOAPBOX_MOREARTS',
    'description' => '_MI_SOAPBOX_MOREARTSDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3,
    'options'     => ['3' => 3, '5' => 5, '10' => 10, '15' => 15, '20' => 20],
];

$modversion['config'][] = [
    'name'        => 'colsinmenu',
    'title'       => '_MI_SOAPBOX_COLSINMENU',
    'description' => '_MI_SOAPBOX_COLSINMENUDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'colsperindex',
    'title'       => '_MI_SOAPBOX_COLSPERINDEX',
    'description' => '_MI_SOAPBOX_COLSPERINDEXDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 3,
    'options'     => [
        '3'  => 3,
        '5'  => 5,
        '10' => 10,
        '15' => 15,
        '20' => 20,
        '25' => 25,
        '30' => 30,
        '50' => 50
    ],
];

$modversion['config'][] = [
    'name'        => 'includerating',
    'title'       => '_MI_SOAPBOX_ALLOWRATING',
    'description' => '_MI_SOAPBOX_ALLOWRATINGDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'introtitle',
    'title'       => '_MI_SOAPBOX_INTROTIT',
    'description' => '_MI_SOAPBOX_INTROTITDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => _MI_SOAPBOX_INTROTITDFLT,
];

$modversion['config'][] = [
    'name'        => 'introtext',
    'title'       => '_MI_SOAPBOX_INTROTEXT',
    'description' => '_MI_SOAPBOX_INTROTEXTDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => _MI_SOAPBOX_INTROTEXTDFLT,
];

$modversion['config'][] = [
    'name'        => 'buttonsadmin',
    'title'       => '_MI_SOAPBOX_BUTTSTXT',
    'description' => '_MI_SOAPBOX_BUTTSTXTDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

xoops_load('xoopseditorhandler');
$editorHandler  = XoopsEditorHandler::getInstance();


$modversion['config'][] = [
    'name'        => 'editorAdmin',
    'title'       => 'MI_SOAPBOX_EDITOR_ADMIN',
    'description' => 'MI_SOAPBOX_EDITOR_DESC_ADMIN',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => array_flip($editorHandler->getList()),
    'default'     => 'tinymce'
];

$modversion['config'][] = [
    'name'        => 'editorUser',
    'title'       => 'MI_SOAPBOX_EDITOR_USER',
    'description' => 'MI_SOAPBOX_EDITOR_DESC_USER',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'options'     => array_flip($editorHandler->getList()),
    'default'     => 'dhtmltextarea'
];


// Теги
$modversion['config'][] = [
    'name'        => 'usetag',
    'title'       => '_MI_SOAPBOX_USETAG',
    'description' => '_MI_SOAPBOX_USETAGDSC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => '_MI_SOAPBOX_SHOW_SAMPLE_BUTTON',
    'description' => '_MI_SOAPBOX_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

// -------------------- Comments -----------------------------

$modversion['hasComments']          = 1;
$modversion['comments']['itemName'] = 'articleID';
$modversion['comments']['pageName'] = 'article.php';

// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'sb_com_approve';
$modversion['comments']['callback']['update']  = 'sb_com_update';

// Notification
$modversion['hasNotification']                                = 1;
$modversion['notification']['lookup_file']                    = 'include/notification.inc.php';
$modversion['notification']['lookup_func']                    = 'sb_notify_iteminfo';

$modversion['notification']['category'][] = [
    'name'           => 'global',
    'title'          => _MI_SOAPBOX_GLOBAL_NOTIFY,
    'description'    => _MI_SOAPBOX_GLOBAL_NOTIFYDSC,
    'subscribe_from' => ['index.php', 'column.php', 'article.php'],
];

$modversion['notification']['category'][] = [
    'name'           => 'column',
    'title'          => _MI_SOAPBOX_COLUMN_NOTIFY,
    'description'    => _MI_SOAPBOX_COLUMN_NOTIFYDSC,
    'subscribe_from' => ['index.php'],
    'item_name'      => 'columnID',
    'allow_bookmark' => 1,
];

$modversion['notification']['category'][] = [
    'name'           => 'article',
    'title'          => _MI_SOAPBOX_ARTICLE_NOTIFY,
    'description'    => _MI_SOAPBOX_ARTICLE_NOTIFYDSC,
    'subscribe_from' => 'article.php',
    'item_name'      => 'articleID',
    'allow_bookmark' => 1,
];

$modversion['notification']['event'][] = [
    'name'          => 'new_column',
    'category'      => 'global',
    'title'         => _MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFY,
    'caption'       => _MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYDSC,
    'mail_template' => 'global_newcolumn_notify',
    'mail_subject'  => _MI_SOAPBOX_GLOBAL_NEWCOLUMN_NOTIFYSBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'article_submit',
    'category'      => 'global',
    'admin_only'    => 1,
    'title'         => _MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFY,
    'caption'       => _MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYDSC,
    'mail_template' => 'global_articlesubmit_notify',
    'mail_subject'  => _MI_SOAPBOX_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'new_article',
    'category'      => 'global',
    'title'         => _MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFY,
    'caption'       => _MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYDSC,
    'mail_template' => 'global_newarticle_notify',
    'mail_subject'  => _MI_SOAPBOX_GLOBAL_NEWARTICLE_NOTIFYSBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'article_submit',
    'category'      => 'column',
    'admin_only'    => 1,
    'title'         => _MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFY,
    'caption'       => _MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYDSC,
    'mail_template' => 'column_articlesubmit_notify',
    'mail_subject'  => _MI_SOAPBOX_COLUMN_ARTICLESUBMIT_NOTIFYSBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'new_article',
    'category'      => 'column',
    'title'         => _MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFY,
    'caption'       => _MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYDSC,
    'mail_template' => 'column_newarticle_notify',
    'mail_subject'  => _MI_SOAPBOX_COLUMN_NEWARTICLE_NOTIFYSBJ,
];

$modversion['notification']['event'][] = [
    'name'          => 'approve',
    'category'      => 'article',
    'invisible'     => 1,
    'title'         => _MI_SOAPBOX_ARTICLE_APPROVE_NOTIFY,
    'caption'       => _MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYCAP,
    'description'   => _MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYDSC,
    'mail_template' => 'article_approve_notify',
    'mail_subject'  => _MI_SOAPBOX_ARTICLE_APPROVE_NOTIFYSBJ,
];


// On Update
if (!empty($_POST['fct']) && !empty($_POST['op']) && !empty($_POST['diranme']) && 'modulesadmin' === $_POST['fct']
    && 'update_ok' === $_POST['op']
    && $_POST['dirname'] === $modversion['dirname']) {
    include __DIR__ . '/include/onupdate.inc.php';
}
