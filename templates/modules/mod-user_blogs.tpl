{if $user}
{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{tr}My blogs{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modUserBlogs}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-view_blog.php?blogId={$modUserBlogs[ix].blogId}">{$modUserBlogs[ix].title}</a></div>
{/section}
</div>
</div>
{/if}
{/if}