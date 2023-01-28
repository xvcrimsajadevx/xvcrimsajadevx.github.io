<h2>{tr}Assign permissions to group: {$group}{/tr}</h2>
<div>
<h3>Group Information</h3>
<table>
<tr><td class="heading">Name:</td><td class="text">{$group_info.groupName}</td></tr>
<tr><td class="heading">Desc:</td><td class="text">{$group_info.groupDesc}</td></tr>
<tr><td class="heading">Permissions:</td><td class="text">
{section name=grp loop=$group_info.perms}
{$group_info.perms[grp]}{if $group_info.perms[grp] != "Anonymous"}(<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;permission={$group_info.perms[grp]}&amp;group={$group}&amp;action=remove">x</a>){/if}&nbsp;
{/section}
</td></tr>
</table>
</div>
<br/>
<div align="center">
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr><td>Find</td>
   <td>
   <form method="get" action="tiki-assignpermission.php">
     <input type="text" name="find" />
     <input type="hidden" name="group" value="{$group}" />
     <input type="submit" value="find" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
   <td>
   [
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}">{tr}All{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=tiki">{tr}General{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=wiki">{tr}Wiki{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=file%20galleries">{tr}File galleries{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=comments">{tr}Comments{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=blogs">{tr}Blogs{/tr}</a>|
   <a class="link" href="tiki-assignpermission.php?sort_mode={$sort_mode}&amp;group={$group}&amp;type=image%20galleries">{tr}Image galleries{/tr}</a>
   ]
   </td>
</tr>
</table>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr>
<td class="heading"><a class="link" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'permName_desc'}permName_asc{else}permName_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a class="link" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'groupDesc_desc'}permDesc_asc{else}permDesc_desc{/if}">{tr}desc{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$perms}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$perms[user].permName}</td>
<td class="odd">{$perms[user].type}</td>
<td class="odd">{$perms[user].permDesc}</td>
<td class="odd">
<a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
</tr>
{else}
<tr>
<td class="even">{$perms[user].permName}</td>
<td class="even">{$perms[user].type}</td>
<td class="even">{$perms[user].permDesc}</td>
<td class="even"><a class="link" href="tiki-assignpermission.php?type={$type}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=assign&amp;perm={$perms[user].permName}&amp;group={$group}">{tr}assign{/tr}</a></td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-assignpermission.php?type={$type}&amp;group={$group}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>
