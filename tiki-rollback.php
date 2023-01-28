<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display('error.tpl');
  die;
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}

if(!isset($_REQUEST["version"])) {
  $smarty->assign('msg',tra("No version indicated"));
  $smarty->display('error.tpl');
  die;
} else {
  $version = $_REQUEST["version"];
  $smarty->assign_by_ref('version',$_REQUEST["version"]); 
}

if(!$tikilib->version_exists($page,$version)) {
  $smarty->assign('msg',tra("Unexistant version"));
  $smarty->display('error.tpl');
  die;
}

include_once("tiki-pagesetup.php");

// Now check permissions to access this page
if($tiki_p_rollback != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot rollback this page"));
  $smarty->display('error.tpl');
  die;  
}



$version = $tikilib->get_version($page,$version);
$version["data"] = $tikilib->parse_data($version["data"]);
$smarty->assign_by_ref('preview',$version);

// If the page doesn't exist then display an error
if(!$tikilib->page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display('error.tpl');
  die;
}

if(isset($_REQUEST["rollback"])) {
  $tikilib->use_version($_REQUEST["page"],$_REQUEST["version"]);
  header("location: tiki-index.php");
  die;  
}

$smarty->assign('mid','tiki-rollback.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display('tiki.tpl');
?>