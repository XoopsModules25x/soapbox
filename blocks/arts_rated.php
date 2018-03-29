<?php
/**
 *
 * Module: Soapbox
 * Author: hsalazar
 * Licence: GNU
 * @param $options
 * @return array
 */
// defined('XOOPS_ROOT_PATH') || die('Restricted access');
function b_arts_rated($options)
{
    $myts          = MyTextSanitizer:: getInstance();
    $block_outdata = [];
    $module_name   = 'soapbox';
    $hModule       = xoops_getHandler('module');
    $soapModule    = $hModule->getByDirname($module_name);
    if (!is_object($soapModule)) {
        return null;
    }
    $hModConfig = xoops_getHandler('config');
    $module_id  = $soapModule->getVar('mid');
    $soapConfig = $hModConfig->getConfigsByCat(0, $module_id);
    //-------------------------------------
    if (!in_array($options[0], ['datesub', 'weight', 'counter', 'rating', 'headline'])) {
        $options[0] = 'datesub';
    }
    $sortorder = 'DESC';
    if ('weight' === $options[0]) {
        $sortorder = 'ASC';
    }
    $entrydataHandler = xoops_getModuleHandler('entryget', $module_name);
    $_entryob_arr     = $entrydataHandler->getArticlesAllPermcheck((int)$options[1], 0, true, true, 0, 0, 1, $options[0], $sortorder, null, null, false, false);
    if (empty($_entryob_arr) || 0 === count($_entryob_arr)) {
        return $block_outdata;
    }
    //-------------------------------------
    foreach ($_entryob_arr as $_entryob) {
        if (is_object($_entryob)) {
            //-----------
            $newarts['linktext'] = xoops_substr($_entryob->getVar('headline'), 0, (int)$options[2]);
            $newarts['id']       = $_entryob->getVar('articleID');
            $newarts['dir']      = $module_name;
            $newarts['date']     = $myts->htmlSpecialChars(formatTimestamp($_entryob->getVar('datesub'), $soapConfig['dateformat']));
            if ('datesub' === $options[0]) {
                $newarts['new'] = $myts->htmlSpecialChars(formatTimestamp($_entryob->getVar('datesub'), $soapConfig['dateformat']));
            } elseif ('counter' === $options[0]) {
                $newarts['new'] = $_entryob->getVar('counter');
            } elseif ('weight' === $options[0]) {
                $newarts['new'] = $_entryob->getVar('weight');
            } elseif ('rating' === $options[0]) {
                $newarts['new']   = number_format($_entryob->getVar('rating'), 2, '.', '');
                $newarts['votes'] = $_entryob->getVar('votes');
            } else {
                $newarts['new'] = $myts->htmlSpecialChars(formatTimestamp($_entryob->getVar('datesub'), $soapConfig['dateformat']));
            }
            $block_outdata['artslist'][] = $newarts;
        }
    }

    return $block_outdata;
}

/**
 * @param $options
 * @return string
 */
function b_arts_rated_edit($options)
{
    $myts = MyTextSanitizer:: getInstance();
    $form = '' . _MB_SOAPBOX_ORDER . "&nbsp;<select name='options[]'>";

    $form .= "<option value='datesub'";
    if ('datesub' === $options[0]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_DATE . "</option>\n";

    $form .= "<option value='counter'";
    if ('counter' === $options[0]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_HITS . "</option>\n";

    $form .= "<option value='weight'";
    if ('weight' === $options[0]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_WEIGHT . "</option>\n";

    $form .= "<option value='rating'";
    if ('rating' === $options[0]) {
        $form .= ' selected';
    }
    $form .= '>' . _MB_SOAPBOX_RATING . "</option>\n";

    $form .= "</select>\n";
    $form .= '&nbsp;' . _MB_SOAPBOX_DISP . "&nbsp;<input type='text' name='options[]' value='" . $myts->htmlSpecialChars($options[1]) . "'>&nbsp;" . _MB_SOAPBOX_ARTCLS . '';
    $form .= '&nbsp;<br>' . _MB_SOAPBOX_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $myts->htmlSpecialChars($options[2]) . "'>&nbsp;" . _MB_SOAPBOX_LENGTH . '';

    return $form;
}
