<ul>
  <{foreach item=artlink from=$block.artslist}>
    <li><a href="<{$xoops_url}>/modules/<{$artlink.dir}>/article.php?articleID=<{$artlink.id}>" title="[<{$artlink.new}>]"><{$artlink.linktext}></a></li>
  <{/foreach}>
</ul>