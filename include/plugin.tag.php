<?php

function soapbox_tag_iteminfo(&$items)
{
    $itemsId = array();
    foreach (array_keys($items) as $catId) {
        foreach (array_keys($items[$catId]) as $itemId) {
            $itemsId[] = (int)$itemId;
        }
    }
    $itemHandler = xoops_getModuleHandler('sbarticles', 'soapbox');
    $criteria    = new Criteria('articleID', '(' . implode(', ', $itemsId) . ')', 'IN');
    $itemsObj    = $itemHandler->getObjects($criteria, 'articleID');

    foreach (array_keys($items) as $catId) {
        foreach (array_keys($items[$catId]) as $itemId) {
            $itemObj                = $itemsObj[$itemId];
            $items[$catId][$itemId] = array(
                'title'   => $itemObj->getVar('headline'),
                'uid'     => $itemObj->getVar('uid'),
                'link'    => "article.php?articleID={$itemId}",
                'time'    => $itemObj->getVar('datesub'),
                'tags'    => tag_parse_tag($itemObj->getVar('item_tag', 'n')), // optional
                'content' => $itemObj->getVar('lead')
            );
        }
    }
    unset($itemsObj);
}

function soapbox_tag_synchronization($mid)
{
    // Optional
}