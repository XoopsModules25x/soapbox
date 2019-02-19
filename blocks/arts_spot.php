<?php
/**
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 * @param $options
 * @return array
 */

use XoopsModules\Soapbox;

/* This function spotlights a column, with a spotlight article and links to others */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');
/**
 * @param $options
 * @return array
 */
function b_arts_spot_show($options)
{
    $block_outdata = [];
    //-------------------------------------
    $myts          = \MyTextSanitizer:: getInstance();
    $helper        = \XoopsModules\Soapbox\Helper::getInstance();
    $module_name   = 'soapbox';
    $moduleHandler = xoops_getHandler('module');
    $soapModule    = $moduleHandler->getByDirname($module_name);
    if (!is_object($soapModule)) {
        return null;
    }

    $hModConfig = xoops_getHandler('config');
    $module_id  = $soapModule->getVar('mid');
    $soapConfig = $hModConfig->getConfigsByCat(0, $module_id);
    //-------------------------------------
    // To handle options in the template
    if (isset($options[0]) && 1 === $options[0]) {
        $block_outdata['showspotlight'] = 1;
    } else {
        $block_outdata['showspotlight'] = 0;
    }
    //-------------------------------------
    if (isset($options[1])) {
        $options[1] = (int)$options[1];
    } else {
        $options[1] = 1;
    }
    if (empty($options[1])) {
        $options[1] = 1;
    }
    //-------------------------------------
    if (isset($options[2]) && 1 === $options[2]) {
        $block_outdata['showdateask'] = 1;
    } else {
        $block_outdata['showdateask'] = 0;
    }
    //-------------------------------------
    if (isset($options[3]) && 1 === $options[3]) {
        $block_outdata['showbylineask'] = 1;
    } else {
        $block_outdata['showbylineask'] = 0;
    }
    //-------------------------------------
    if (isset($options[4]) && 1 === $options[4]) {
        $block_outdata['showstatsask'] = 1;
    } else {
        $block_outdata['showstatsask'] = 0;
    }
    //-------------------------------------
    if (isset($options[5]) && 'ver' === $options[5]) {
        $block_outdata['verticaltemplate'] = 1;
    } else {
        $block_outdata['verticaltemplate'] = 0;
    }
    //-------------------------------------
    if (isset($options[6]) && 1 === $options[6]) {
        $block_outdata['showpicask'] = 1;
    } else {
        $block_outdata['showpicask'] = 0;
    }
    //-------------------------------------
    $sortname = $options[7];
    if (!in_array($sortname, ['datesub', 'weight', 'counter', 'rating', 'headline'], true)) {
        $sortname = 'datesub';
    }
    $sortorder = 'DESC';
    if ('weight' === $sortname) {
        $sortorder = 'ASC';
    }
    //-------------------------------------
    if (isset($options[8]) && (int)$options[8] > 0) {
        $options[8] = (int)$options[8];
    } else {
        $options[8] = 65;
    }
    //-------------------------------------
    // Try to see what tabs are visibles (if we are in restricted view of course)
    $opt_columnIDs = [];
    if (!empty($options[9])) {
        $opt_columnIDs = array_slice($options, 9);
    }
    if (!empty($opt_columnIDs) && is_array($opt_columnIDs)) {
        foreach ($opt_columnIDs as $v) {
            $columnIDs[] = (int)$v;
        }
    } else {
        $columnIDs = null;
    }
    // Retrieve the column's name
    //    $resultB = $xoopsDB -> query( "SELECT name, colimage FROM ". $xoopsDB -> prefix( "sbcolumns" ) . " WHERE columnID = " . $options[0] . " " );
    //    list ( $name, $colimage ) = $xoopsDB -> fetchRow( $resultB );
    //-------------------------------------
    $entrydataHandler = $helper->getHandler('Entryget');
    //-------------------------------------
    // Retrieve the latest article in the selected column
    $entryobArray = $entrydataHandler->getArticlesAllPermcheck($options[1], 0, true, true, 0, 0, 1, $sortname, $sortorder, $columnIDs, null, false, false);
    $totalarts    = $entrydataHandler->total_getArticlesAllPermcheck;
    // If there's no article result (which means there's no article yet...
    if (empty($entryobArray) || 0 === count($entryobArray)) {
        $block_outdata['display'] = 0;

        return $block_outdata;
    }
    $block_outdata['display'] = 1;
    //-------------------------------------
    $block_outdata['moduledir']   = $module_name;
    $block_outdata['totalarts']   = (int)$totalarts;
    $block_outdata['modulename']  = $soapModule->getVar('name');
    $block_outdata['sbuploaddir'] = $myts->htmlSpecialChars($soapConfig['sbuploaddir']);
    //-------------------------------------
    $i = 1;
    xoops_load('XoopsUserUtility');
    foreach ($entryobArray as $key => $_entryob) {
        // get vars initialize
        //-------------------------------------
        $articles = $_entryob->toArray();
        //get category object
        $_categoryob = $_entryob->_sbcolumns;
        //get vars
        $category = $_categoryob->toArray();
        //spot
        $_outdata_arr           = [];
        $_outdata_arr           = $articles;
        $_outdata_arr['column'] = $category;

        $_outdata_arr['authorname'] = \XoopsUserUtility::getUnameFromId((int)$category['author']);
        $_outdata_arr['poster']     = \XoopsUserUtility::getUnameFromId($articles['uid']);
        $_outdata_arr['date']       = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
        $_outdata_arr['rating']     = number_format($articles['rating'], 2, '.', '');
        // -- Then the teaser text and as sorted data
        $_outdata_arr['subhead']      = xoops_substr($articles['headline'], 0, $options[8]);
        $_outdata_arr['sublead']      = xoops_substr($articles['lead'], 0, 255);
        $_outdata_arr['subteaser']    = xoops_substr($articles['teaser'], 0, 255);
        $_outdata_arr ['subbodytext'] = xoops_substr($articles['bodytext'], 0, 255);
        $_outdata_arr ['bodytext']    = '';

        if ('datesub' === $sortname) {
            $_outdata_arr['new'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
        } elseif ('counter' === $sortname) {
            $_outdata_arr['new'] = _MB_SOAPBOX_HITS . $articles['counter'];
        } elseif ('weight' === $sortname) {
            $_outdata_arr['new'] = _MB_SOAPBOX_WEIGHT . $articles['weight'];
        } elseif ('rating' === $sortname) {
            $_outdata_arr['new'] = _MB_SOAPBOX_RATING . number_format($articles['rating'], 2, '.', '') . _MB_SOAPBOX_VOTE . $articles['votes'];
        } else {
            $_outdata_arr['new'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
        }
        //--------------------
        $block_outdata['artdatas'][$i] = $_outdata_arr;
        unset($_outdata_arr);
        ++$i;
    }

    return $block_outdata;
}

/**
 * @param $options
 * @return string
 */
function b_arts_spot_edit($options)
{
    global $xoopsDB;
    $myts          = \MyTextSanitizer:: getInstance();
    $helper        = \XoopsModules\Soapbox\Helper::getInstance();
    $columnIDs     = [];
    $module_name   = 'soapbox';
    $moduleHandler = xoops_getHandler('module');
    $soapModule    = $moduleHandler->getByDirname($module_name);
    if (!is_object($soapModule)) {
        return null;
    }
    $form = '';
    //-----
    $chked = '';
    $form  .= _MB_SOAPBOX_SPOTLIGHT;
    if (1 === $options[0]) {
        $chked = ' checked';
    }
    $form  .= "<input type='radio' name='options[0]' value='1'" . $chked . '>&nbsp;' . _YES;
    $chked = '';
    if (0 === $options[0]) {
        $chked = ' checked';
    }
    $form .= "&nbsp;<input type='radio' name='options[0]' value='0'" . $chked . '>' . _NO . '<br>';
    //-----
    //-----
    $form .= _MB_SOAPBOX_ARTSTOSHOW . "<input type='text' name='options[1]' value='" . $myts->htmlSpecialChars($options[1]) . "'>&nbsp; " . _MB_SOAPBOX_ARTCLS . '.<br>';
    //-----
    $chked = '';
    $form  .= _MB_SOAPBOX_SHOWDATE;
    if (1 === $options[2]) {
        $chked = ' checked';
    }
    $form  .= "<input type='radio' name='options[2]' value='1'" . $chked . '>&nbsp;' . _YES;
    $chked = '';
    if (0 === $options[2]) {
        $chked = ' checked';
    }
    $form .= "&nbsp;<input type='radio' name='options[2]' value='0'" . $chked . '>' . _NO . '<br>';
    //-----
    $chked = '';
    $form  .= _MB_SOAPBOX_SHOWBYLINE;
    if (1 === $options[3]) {
        $chked = ' checked';
    }
    $form  .= "<input type='radio' name='options[3]' value='1'" . $chked . '>&nbsp;' . _YES;
    $chked = '';
    if (0 === $options[3]) {
        $chked = ' checked';
    }
    $form .= '&nbsp;<input type="radio" name="options[3]" value="0"' . $chked . '>' . _NO . '<br>';
    //-----
    $chked = '';
    $form  .= _MB_SOAPBOX_SHOWSTATS;
    if (1 === $options[4]) {
        $chked = ' checked';
    }
    $form  .= "<input type='radio' name='options[4]' value='1'" . $chked . '>&nbsp;' . _YES;
    $chked = '';
    if (0 === $options[4]) {
        $chked = ' checked';
    }
    $form .= "&nbsp;<input type='radio' name='options[4]' value='0' " . $chked . '>' . _NO . '<br>';

    $form .= _MB_SOAPBOX_TEMPLATE . "<select name='options[5]' >";
    $form .= "<option value='ver'";
    if ('ver' === $options[5]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_VERTICAL . "</option>\n";
    $form .= "<option value='hor'";
    if ('hor' === $options[5]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_HORIZONTAL . '</option>';
    $form .= '</select><br>';

    $chked = '';
    $form  .= _MB_SOAPBOX_SHOWPIC;
    if (1 === $options[6]) {
        $chked = ' checked';
    }
    $form .= "<input type='radio' name='options[6]' value='1' " . $chked . '>&nbsp;' . _YES;

    $chked = '';
    if (0 === $options[6]) {
        $chked = ' checked';
    }
    $form .= "&nbsp;<input type='radio' name='options[6]' value='0' " . $chked . '>' . _NO . '<br>';
    //---------- sortname ------
    $form .= '' . _MB_SOAPBOX_ORDER . "&nbsp;<select name='options[7]'>";

    $form .= "<option value='datesub'";
    if ('datesub' === $options[7]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_DATE . "</option>\n";

    $form .= "<option value='counter'";
    if ('counter' === $options[7]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_HITS . "</option>\n";

    $form .= "<option value='weight'";
    if ('weight' === $options[7]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_WEIGHT . "</option>\n";

    $form .= "<option value='rating'";
    if ('rating' === $options[7]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_RATING . "</option>\n";

    $form .= "</select>\n";

    $form .= '&nbsp;<br>' . _MB_SOAPBOX_CHARS . "&nbsp;<input type='text' name='options[8]' value='" . $myts->htmlSpecialChars($options[8]) . "'>&nbsp;" . _MB_SOAPBOX_LENGTH . '';

    //-------------------------------------
    $entrydataHandler = $helper->getHandler('Entryget');
    $categoryobArray  = $entrydataHandler->getColumns();
    $form             .= '<br>' . _MB_SOAPBOX_SPOTLIGHT_TOPIC . "<br><select name='options[]' multiple='multiple'>";
    $form             .= "<option value='0'>(ALL)</option>";
    if (!empty($categoryobArray)) {
        foreach ($categoryobArray as $_categoryob) {
            $categoryID = $_categoryob->getVar('columnID');
            $name       = $_categoryob->getVar('name');
            $sel        = '';
            if (in_array($categoryID, $columnIDs, true)) {
                $sel = ' selected="selected"';
            }
            $form .= "<option value='" . $categoryID . "' " . $sel . '>' . $categoryID . ' : ' . $name . '</option>';
        }
    }
    $form .= "</select><br>\n";

    return $form;
}
