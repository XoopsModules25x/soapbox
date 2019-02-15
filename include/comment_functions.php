<?php
//
/**
 *
 * Module: Soapbox
 * Version: v 1.5
 * Release Date: 25 April 2004
 * Author: hsalazar
 * Licence: GNU
 * @param $art_id
 * @param $total_num
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');
function sb_com_update($art_id, $total_num)
{
    //HACK
    //get soapbox moduleConfig
    global $xoopsModule;
    $hModConfig            = xoops_getHandler('config');
    $soapModuleConfig      = $hModConfig->getConfigList((int)$xoopsModule->getVar('mid'));
    $globaldisplaycomments = 0;
    if (isset($soapModuleConfig['globaldisplaycomments'])) {
        $globaldisplaycomments = $soapModuleConfig['globaldisplaycomments'];
    }
    if (0 === $globaldisplaycomments) {
        $db  = \XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'UPDATE ' . $db->prefix('sbarticles') . ' SET commentable = ' . (int)$total_num . ' WHERE articleID = ' . (int)$art_id;
        $db->query($sql);
    }
}

/**
 * @param $comment
 */
function sb_com_approve($comment)
{
    // notification mail here
}
