<?php
$ranking = $tikilib->list_galleries(0, $module_rows, 'lastModif_desc','admin','');
$smarty->assign('modLastGalleries',$ranking["data"]);
?>