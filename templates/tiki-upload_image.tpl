<h1>{tr}Upload Image{/tr}</h1>
<div align="center">
<form enctype="multipart/form-data" action="tiki-upload_image.php" method="post">
<table>
<tr><td class="form">{tr}Image Name{/tr}:</td><td class="form"><input type="text" name="name" /></td></tr>
<tr><td class="form">{tr}Image Description{/tr}:</td><td class="form"><textarea rows="5" cols="40" name="description"></textarea></tr></td>
<tr><td class="form">{tr}Gallery{/tr}:</td><td class="form"> 
<select name="galleryId">
{section name=idx loop=$galleries}
<option  value="{$galleries[idx].id}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/section}
</select></td></tr>
<tr class="form"><td  class="form" colspan="2"><b>{tr}Now enter the image URL{/tr}{tr} or upload a local image from your disk{/tr}
<tr><td class="form">URL:</td><td class="form"><input size="50" type="text" name="url" /></td></tr>
<tr><td class="form">{tr}Upload from disk:{/tr}</td><td class="form">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
<tr><td>&nbsp;</td><td class="form"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
</div>
{if $show eq 'y'}
<br/>
<hr>
<h2>{tr}Upload succesful!{/tr}</h2>
<h3>{tr}The following image was succesfully uploaded{/tr}:</h3>
<div align="center">
<img src="http://{$url_show}?id={$imageId}" /><br/>
<b>{tr}Thumbnail{/tr}:</b><br/>
<img src="http://{$url_show}?id={$imageId}&thumb=1" /><br/><br/>
<div class="wikitext">
{tr}You can view this image in your browser using{/tr}: <a href="http://{$url_browse}?imageId={$imageId}">http://{$url_browse}?imageId={$imageId}</a><br/>
{tr}You can include the image in an HTML/Tiki page using{/tr} &lt;img src="http://{$url_show}?id={$imageId}" /&gt;
</div>
</div>
{/if}



