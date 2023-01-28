<h1><a class="pagetitle" href="tiki-list_posts.php">{tr}Blogs{/tr}</a></h1>
<a class="link" href="tiki-edit_blog.php">edit blog</a>
<a class="link" href="tiki-blog_post.php">post</a>
<a class="link" href="tiki-list_blogs.php">list blogs</a>
<br/><br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-list_posts.php">
     <input type="text" name="find" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table  border="1" width="97%" cellpadding="0" cellspacing="0">
<tr>
<td class="heading"><a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'postId_desc'}postId_asc{else}postId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading">{tr}Blog Title{/tr}</td>
<td class="heading"><a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
<td class="heading">{tr}Size{/tr}</td>
<td class="heading"><a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=changes loop=$listpages}
<tr>
{if $smarty.section.changes.index % 2}
<td class="odd">&nbsp;{$listpages[changes].postId}&nbsp;</td>
<td class="odd">&nbsp;<a class="link" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">{$listpages[changes].blogTitle|truncate:10:"(...)":true}</a>&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="odd">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="odd">
<a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].postId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-blog_post.php?postId={$listpages[changes].postId}">{tr}Edit{/tr}</a>
</td>
{else}
<td class="even">&nbsp;{$listpages[changes].postId}&nbsp;</td>
<td class="even">&nbsp;<a class="link" href="tiki-edit_blog.php?blogId={$listpages[changes].blogId}">{$listpages[changes].blogTitle|truncate:10:"(...)":true}</a>&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].created|date_format:"%a %d of %b, %Y [%H:%M:%S]"}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].size}&nbsp;</td>
<td class="even">&nbsp;{$listpages[changes].user}&nbsp;</td>
<td class="even">
<a class="link" href="tiki-list_posts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$listpages[changes].postId}">{tr}Remove{/tr}</a>
<a class="link" href="tiki-blog_post.php?postId={$listpages[changes].postId}">{tr}Edit{/tr}</a>
</td>
{/if}
</tr>
{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-list_posts.php?offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-list_posts.php?offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
