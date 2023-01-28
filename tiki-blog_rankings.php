<?php
// Initialization
require_once('tiki-setup.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($feature_blog_rankings != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

$allrankings = Array(
  Array( 'name'=> 'Top visited blogs', 'value'=> tra('blog_ranking_top_blogs')),
  Array( 'name'=> 'Last posts', 'value'=> tra('blog_ranking_last_posts')),
  Array( 'name'=> 'Top ative blogs', 'value'=> tra('blog_ranking_top_active_blogs'))
);
$smarty->assign('allrankings',$allrankings);

if(!isset($_REQUEST["which"])) {
  $which = 'blog_ranking_top_blogs';
} else {
  $which = $_REQUEST["which"];
}
$smarty->assign('which',$which);


// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["limit"])) {
  $limit = 10;
} else {
  $limit = $_REQUEST["limit"];
}

$smarty->assign_by_ref('limit',$limit);

// Rankings:
// Top Pages
// Last pages
// Top Authors
$rankings=Array();

$rk = $tikilib->$which($limit);
$rank["data"] = $rk["data"];
$rank["title"] = $rk["title"];
$rank["y"]=$rk["y"];
$rankings[] = $rank;



$smarty->assign_by_ref('rankings',$rankings);
$smarty->assign('rpage','tiki-blog_rankings.php');
// Display the template
$smarty->assign('mid','tiki-ranking.tpl');
$smarty->display('tiki.tpl');
?>
