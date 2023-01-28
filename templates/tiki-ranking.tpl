<h2><a class="pagetitle" href="{$rpage}">{tr}Rankings{/tr}</a></h2>
<form action="{$rpage}" method="post">
<select name="which">
{section name=ix loop=$allrankings}
<option value="{$allrankings[ix].value}" {if $which eq $allrankings[ix].value}selected="selected"{/if}>{$allrankings[ix].name}</option>
{/section}
</select>
<select name="limit">
<option value="10" {if $limit eq 10}selected="selected"{/if}>{tr}Top 10{/tr}</option>
<option value="20" {if $limit eq 20}selected="selected"{/if}>{tr}Top 20{/tr}</option>
<option value="50" {if $limit eq 50}selected="selected"{/if}>{tr}Top 50{/tr}</option>
<option value="100" {if $limit eq 100}selected="selected"{/if}>{tr}Top 100{/tr}</option>
</select>
<input type="submit" name="selrank" value="{tr}view{/tr}" />
</form>
<br/><br/>
{section name=ix loop=$rankings}
<div class="cbox">
<div class="cbox-title">
<table width="98%">
<td>{$rankings[ix].title}</td>
<td align="right">
{$rankings[ix].y}&nbsp;&nbsp;
</td>
</table>
</div>
<div class="cbox-data">
<table width="100%">
{section name=xi loop=$rankings[ix].data}
<tr><td align="left" width="2%">{$smarty.section.xi.index_next})</td><td align="left"><a class="link" href="{$rankings[ix].data[xi].href}">{$rankings[ix].data[xi].name}</a></td><td align="right">{$rankings[ix].data[xi].hits}</td></tr>
{/section}
</table>
</div>
</div>
<br/>
{/section}