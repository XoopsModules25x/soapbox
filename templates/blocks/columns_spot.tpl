<{if $block.display != 0}>
<{if $block.verticaltemplate == 1}>
<{if $block.showspotlight != 1}>
    <{foreach item=column from=`$block.coldatas`}>
        <div style='font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;'><a
                    href='<{$xoops_url}>/modules/<{$block.moduledir}>/column.php?columnID=<{$column.columnID}>'><{$column.name}>
        </div>
        <{foreach item=art from=`$column.artdatas` key=key}>
            <{if $key == 1}>
                <ul>
            <{/if}>
            <li>
                <a href="<{$xoops_url}>/modules/<{$block.moduledir}>/article.php?articleID=<{$art.articleID}>"><{$art.subhead}></a>
                [<{$art.new}>]
            </li>
        <{/foreach}>
        <{if $key >= 1}>
            </ul>
        <{/if}>
    <{/foreach}>
<{else}>
    <{foreach item=column from=`$block.coldatas`}>
        <div style="font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;"><a
                    href="<{$xoops_url}>/modules/<{$block.moduledir}>/column.php?columnID=<{$column.columnID}>"><{$column.name}>
        </div>
        <{if $block.showbylineask == 1}><{$smarty.const._MB_SOAPBOX_BY}><{$column.authorname}><br><{/if}>
        <div style="margin: 8px 0 2px 0;">
            <{if $block.showpicask == 1}>
                <{if $column.colimage != "blank.png" }>
                    <div style="float: left; width: 80px; margin-right: 10px; border: 1px #000000; "><img
                                src="<{$xoops_url}>/<{$block.sbuploaddir}>/<{$column.colimage}>" width="80"></div>
                <{/if}>
            <{/if}>
            <{$column.description}></div>
        <div style="height: 0; clear: both;"></div>
        <{foreach item=art from=`$column.artdatas` key=key }>
            <{if $key == 1}>
                <div>
                    <h4 style="margin: 6px 0;"><a
                                href="<{$xoops_url}>/modules/<{$block.moduledir}>/article.php?articleID=<{$art.articleID}>"><{$art.headline}></a>
                    </h4>
                    <{if $block.showdateask == 1}>
                        <div style="font-size: xx-small; margin: 0 0 6px 0;"><{$art.date}></div><{/if}>
                    <div style="margin: 8px 0 2px 0;">
                        <{$art.teaser}></div>
                    <{if $block.showstatsask == 1}>
                        <div style="font-size: xx-small; margin-top: 4px;"><{$smarty.const._MB_SOAPBOX_HIT}><{$art.counter}><{$smarty.const._MB_SOAPBOX_RATE}><{$art.rating}><{$smarty.const._MB_SOAPBOX_VOTE}><{$art.votes}></div><{/if}>
                </div>
            <{/if}>
            <{if $key == 2 }>
                <div style="font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 6px 0 0 0;">
                    <a
                            href="<{$xoops_url}>/modules/<{$block.moduledir}>/column.php?columnID=<{$art.column.columnID}>"><{$smarty.const._MB_SOAPBOX_MOREHERE}></a>
                </div>
                <ul>
            <{/if}>
            <{if $key >= 2 }>
                <li>
                    <a href="<{$xoops_url}>/modules/<{$block.moduledir}>/article.php?articleID=<{$art.articleID}>"><{$art.subhead}></a>
                    [<{$art.new}>]
                </li>
            <{/if}>
        <{/foreach}>
        <{if $key >= 2 }>
            </ul>
        <{/if}>
    <{/foreach}>
<{/if}>
<{elseif verticaltemplate == 0}>
<{if $block.showspotlight != 1}>
<{foreach item=column from=`$block.coldatas`}>
<div style='float:left; width: 48%; margin-right: 10px;'>
    <div style='font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;'><a
                href='<{$xoops_url}>/modules/<{$block.moduledir}>/column.php?columnID=<{$column.columnID}>'><{$column.name}>
    </div>
    <{foreach item=art from=`$column.artdatas` key=key}>
    <{if $key == 1}>
    <ul>
        <{/if}>
        <li>
            <a href="<{$xoops_url}>/modules/<{$block.moduledir}>/article.php?articleID=<{$art.articleID}>"><{$art.subhead}></a>
            [<{$art.new}>]
        </li>
        <{/foreach}>
        <{if $key >= 1}>
    </ul>
    <{/if}>
    <{cycle values="</div>,</div><div style='height: 0; clear: both;'></div>" }>
    <{/foreach}>
    <{else}>
    <{foreach item=column from=`$block.coldatas`}>
        <div style="float:left; width: 48%; margin-right: 10px;">
            <div style="font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;"><a
                        href="<{$xoops_url}>/modules/<{$block.moduledir}>/column.php?columnID=<{$column.columnID}>"><{$column.name}>
            </div>
            <{if $block.showbylineask == 1}><{$smarty.const._MB_SOAPBOX_BY}><{$column.authorname}><br><{/if}>
            <div style="margin: 8px 0 2px 0;">
                <{if $block.showpicask == 1}>
                    <{if $column.colimage != "blank.png" }>
                        <div style="float: left; width: 80px; margin-right: 10px; border: 1px #000000; "><img
                                    src="<{$xoops_url}>/<{$block.sbuploaddir}>/<{$column.colimage}>" width="80"></div>
                    <{/if}>
                <{/if}>
                <{$column.description}></div>
        </div>
        <{if $block.showartcles != 1}>
            <{cycle values=",</div><div style='height: 0; clear: both;'></div>" }>
        <{else}>
            <{foreach item=art from=`$column.artdatas` key=key }>
                <{if $key == 1 }>
                    <div>
                    <div style="font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;">
                        <a
                                href="<{$xoops_url}>/modules/<{$block.moduledir}>/index.php"><{$smarty.const._MB_SOAPBOX_MOREHERE}></a>
                    </div>
                    <ul>
                <{/if}>
                <li>
                    <a href="<{$xoops_url}>/modules/<{$block.moduledir}>/article.php?articleID=<{$art.articleID}>"><{$art.subhead}></a>
                    [<{$art.new}>]
                </li>
            <{/foreach}>
            <{if $key >= 1 }>
                </ul>
                </div>
            <{/if}>
            <div style="height: 0; clear: both;"></div>
        <{/if}>
    <{/foreach}>
    <{/if}>
    <{/if}>
    <{elseif $block.diaplay == 0}>
    <div style="font-size: 12px; font-weight: bold; background-color: #ccc; padding: 2px 6px; margin: 0;"><{$smarty.const._MB_SOAPBOX_NOTHINGYET}></div>
    <{/if}>
