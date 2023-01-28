<?php
// Initialization
require_once('tiki-setup.php');

if($feature_dynamic_content != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if($tiki_p_admin_dynamic != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display('error.tpl');
  die;  
}


if(isset($_REQUEST["remove"])) {
  $tikilib->remove_contents($_REQUEST["remove"]);  
}


$smarty->assign('description','');
$smarty->assign('contentId',0);

if(isset($_REQUEST["save"])) {
  $smarty->assign('description',$_REQUEST["description"]);
  $id = $tikilib->replace_content($_REQUEST["contentId"],$_REQUEST["description"]);
  $smarty->assign('contentId',$id);
}

if(isset($_REQUEST["edit"])) {
  $info = $tikilib->get_content($_REQUEST["edit"]);
  $smarty->assign('contentId',$info["contentId"]);
  $smarty->assign('description',$info["description"]);
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'contentId_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);


if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_content($offset,$maxRecords,$sort_mode,$find);
// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listpages["cant"] > ($offset + $maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('listpages',$listpages["data"]);
//print_r($listpages["data"]);

// Display the template
$smarty->assign('mid','tiki-list_contents.tpl');
$smarty->display('tiki.tpl');
?>
