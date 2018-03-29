<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 * @param $options
 * @return array
 */
/* This function spotlights a column, with a spotlight article and links to others */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');
function b_columns_spot_show($options)
{
    $block_outdata = [];
    //-------------------------------------
    $myts        = MyTextSanitizer:: getInstance();
    $module_name = 'soapbox';
    $hModule     = xoops_getHandler('module');
    $soapModule  = $hModule->getByDirname($module_name);
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
        $options[1] = 0;
    }
    if (0 === $options[1]) {
        $block_outdata['showartcles'] = 0;
    } else {
        $block_outdata['showartcles'] = 1;
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
    if (!in_array($sortname, ['datesub', 'weight', 'counter', 'rating', 'headline'])) {
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
    $entrydataHandler = xoops_getModuleHandler('entryget', $module_name);
    //-------------------------------------
    //get category object
    $categoryobArray = $entrydataHandler->getColumnsAllPermcheck(0, 0, true, 'weight', 'ASC', $columnIDs, null, true, false);
    if (empty($categoryobArray) || 0 === count($categoryobArray)) {
        $block_outdata['display'] = 0;

        return $block_outdata;
    }
    $block_outdata['display'] = 1;
    //-------------------------------------
    $block_outdata['totalcols']   = $entrydataHandler->total_getColumnsAllPermcheck;
    $block_outdata['moduledir']   = $module_name;
    $block_outdata['modulename']  = $soapModule->getVar('name');
    $block_outdata['sbuploaddir'] = $myts->htmlSpecialChars($soapConfig['sbuploaddir']);
    //-------------------------------------
    $i_col = 1;
    xoops_load('XoopsUserUtility');
    foreach ($categoryobArray as $_categoryob) {
        //----------------------------
        $category                   = $_categoryob->toArray(); //all assign
        $_outdata_arr               = [];
        $_outdata_arr               = $category;
        $_outdata_arr['authorname'] = XoopsUserUtility::getUnameFromId((int)$category['author']);
        //-------------------------------------
        if (0 === $options[1]) {
            $_outdata_arr['artdatas'] = [];
        } else {
            //-------------------------------------
            // Retrieve the latest article in the selected column
            $_entryob_arr              = $entrydataHandler->getArticlesAllPermcheck($options[1], 0, true, true, 0, 0, 1, $sortname, $sortorder, $category['columnID'], null, false, false);
            $_outdata_arr['totalarts'] = $entrydataHandler->total_getArticlesAllPermcheck;
            //----------------------------
            //xoops_load('XoopsUserUtility');
            $i = 1;
            foreach ($_entryob_arr as $key => $_entryob) {
                // get vars initialize
                //-------------------------------------
                $articles   = $_entryob->toArray();
                $articles[] = $articles;
                //spot
                $articles['poster'] = XoopsUserUtility::getUnameFromId($articles['uid']);
                $articles['date']   = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
                $articles['rating'] = number_format($articles['rating'], 2, '.', '');
                // -- Then the teaser text and as sorted data
                $articles['subhead']     = xoops_substr($articles['headline'], 0, $options[8]);
                $articles['sublead']     = xoops_substr($articles['lead'], 0, $options[8]);
                $articles['subteaser']   = xoops_substr($articles['teaser'], 0, $options[8]);
                $articles['subbodytext'] = xoops_substr($articles['bodytext'], 0, $options[8]);
                $articles['bodytext']    = '';

                if ('datesub' === $sortname) {
                    $articles['new'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
                } elseif ('counter' === $sortname) {
                    $articles['new'] = _MB_SOAPBOX_HITS . $articles['counter'];
                } elseif ('weight' === $sortname) {
                    $articles['new'] = _MB_SOAPBOX_WEIGHT . $articles['weight'];
                } elseif ('rating' === $sortname) {
                    $articles['new'] = _MB_SOAPBOX_RATING . number_format($articles['rating'], 2, '.', '') . _MB_SOAPBOX_VOTE . $articles['votes'];
                } else {
                    $articles['new'] = $myts->htmlSpecialChars(formatTimestamp($articles['datesub'], $soapConfig['dateformat']));
                }
                //--------------------
                $_outdata_arr['artdatas'][$i] = $articles;
                unset($articles);
                ++$i;
            }
        }
        //-------------------------------------
        $block_outdata['coldatas'][$i_col] = $_outdata_arr;
        unset($_outdata_arr);
        ++$i_col;
    }

    return $block_outdata;
}

/**
 * @param $options
 * @return string
 */
function b_columns_spot_edit($options)
{
    $myts        = MyTextSanitizer:: getInstance();
    $module_name = 'soapbox';
    $hModule     = xoops_getHandler('module');
    $soapModule  = $hModule->getByDirname($module_name);
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
    // Try to see what tabs are visibles (if we are in restricted view of course)
    $opt_columnIDs = [];
    if (!empty($options[9])) {
        $opt_columnIDs = array_slice($options, 9);
    }
    if (!empty($opt_columnIDs) && is_array($opt_columnIDs)) {
        foreach ($opt_columnIDs as $v) {
            $columnIDs[] = (int)$v;
        }
    }
    $entrydataHandler = xoops_getModuleHandler('entryget', $module_name);
    $categoryobArray  = $entrydataHandler->getColumns();
    $form             .= '<br>' . _MB_SOAPBOX_SPOTLIGHT_TOPIC . "<br><select name='options[]' multiple='multiple'>";
    $form             .= "<option value='0'>(ALL)</option>";
    if (!empty($categoryobArray)) {
        foreach ($categoryobArray as $_categoryob) {
            $categoryID = $_categoryob->getVar('columnID');
            $name       = $_categoryob->getVar('name');
            $sel        = '';
            if (in_array($categoryID, $columnIDs)) {
                $sel = ' selected="selected"';
            }
            $form .= "<option value='" . $categoryID . "' " . $sel . '>' . $categoryID . ' : ' . $name . '</option>';
        }
    }
    $form .= "</select><br>\n";

    return $form;
}
