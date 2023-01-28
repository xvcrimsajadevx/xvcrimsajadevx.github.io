<?php
// Initialization
require_once('tiki-setup.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

//print($GLOBALS["HTTP_REFERER"]);

// Create the HomePage if it doesn't exist
if(!$tikilib->page_exists("HomePage")) {
  $tikilib->create_page("HomePage",0,'',date("U"),'Tiki initialization'); 
}

if(!isset($_SESSION["thedate"])) {
  $thedate = date("U");
} else {
  $thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $_REQUEST["page"]='HomePage';
  $page = 'HomePage';
  $smarty->assign('page','HomePage'); 
} else {
  $page = $_REQUEST["page"];
  $smarty->assign_by_ref('page',$_REQUEST["page"]); 
}



require_once('tiki-pagesetup.php');

// Check if we have to perform an action for this page
// for example lock/unlock
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='lock') {
    $tikilib->lock_page($page);
  } elseif ($_REQUEST["action"]=='unlock') {
    $tikilib->unlock_page($page);
  }  
}


// If the page doesn't exist then display an error
if(!$tikilib->page_exists($page)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display('error.tpl');
  die;
}


// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display('error.tpl');
  die;  
}


// Now increment page hits since we are visiting this page
$tikilib->add_hit($page);

// Get page data
$info = $tikilib->get_page_info($page);

// Verify lock status
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}

$pdata = $tikilib->parse_data($info["data"]);
$smarty->assign_by_ref('parsed',$pdata);
//$smarty->assign_by_ref('lastModif',date("l d of F, Y  [H:i:s]",$info["lastModif"]));
$smarty->assign_by_ref('lastModif',$info["lastModif"]);
if(empty($info["user"])) {
  $info["user"]='anonymous';  
}
$smarty->assign_by_ref('lastUser',$info["user"]);

/*
// force enable wiki comments (for development)
$feature_wiki_comments = 'y';
$smarty->assign('feature_wiki_comments','y');
*/

// Comments engine!
if($feature_wiki_comments == 'y') {
  $comments_per_page = $wiki_comments_per_page;
  $comments_default_ordering = $wiki_comments_default_ordering;
  $comments_vars=Array('page');
  $comments_prefix_var='wikipage';
  $comments_object_var='page';
  include_once("comments.php");
}


// Display the Index Template

$smarty->assign('mid','tiki-show_page.tpl');
$smarty->assign('show_page_bar','y');
$smarty->display('tiki.tpl');

?>