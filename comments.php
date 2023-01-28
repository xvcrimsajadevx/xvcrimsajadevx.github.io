<?php
// This file sets up the information needed to display
// the comments preferences, post-comment box and the
// list of comments. Finally it displays blog-comments.tpl
// using this information

// Setup URLS for the Comments next and prev buttons and use variables that
// cannot be aliased by normal tiki variables.
// Traverse each _REQUEST data adn put them in an array
if(!isset($comments_per_page)) {
  $comments_per_page = 10;
} 

if(!isset($comments_default_ordering)) {
  $comments_default_ordering='commentDate_desc';
}

$comments_aux=Array();
$comments_show='n';
$comments_t_query='';
$comments_first=1;

foreach($comments_vars as $c_name) {
  $comments_avar["name"]=$c_name;
  $comments_avar["value"]=$_REQUEST["$c_name"];
  $comments_aux[]=$comments_avar;
  if($comments_first) {
    $comments_first=0;
    $comments_t_query.="?$c_name=".$_REQUEST["$c_name"];
  } else {
    $comments_t_query.="&amp;$c_name=".$_REQUEST["$c_name"];
  }
}

$smarty->assign_by_ref('comments_request_data',$comments_aux);
if(!isset($_REQUEST['comments_threshold'])) {
  $_REQUEST['comments_threshold']=0;
}
$smarty->assign('comments_threshold',$_REQUEST['comments_threshold']);
// This sets up comments father as the father
$comments_server = $_SERVER["SERVER_NAME"];
$comments_parsed=parse_url($_SERVER["REQUEST_URI"]);
if(!isset($comments_parsed["query"])) {
  $comments_parsed["query"]='';
}
parse_str($comments_parsed["query"],$comments_query);
$comments_father='http://'.$comments_server.$comments_parsed["path"];
$comments_complete_father = $comments_father;

/*
if(count($comments_query)>0) {
  $comments_first=1;
  foreach($comments_query as $com_name => $com_val) {
    if($comments_first) {
      $comments_first=false;
      $comments_complete_father.='?'.$com_name.'='.$com_val;
    } else {
      $comments_complete_father.='&amp;'.$com_name.'='.$com_val;
    }
  }
}
*/
$comments_complete_father=$comments_father.$comments_t_query;
//print("Father: $comments_complete_father<br/>");
if(strstr($comments_complete_father,"?")) {
  $comments_complete_father.='&amp;';
} else {
  $comments_complete_father.='?';
}
//print("Father: $comments_father<br/>");
//print("Com: $comments_complete_father<br/>");
$smarty->assign('comments_father',$comments_father);
$smarty->assign('comments_complete_father',$comments_complete_father);

// Include the library for comments (if not included)
include_once("lib/commentslib.php");
$commentslib = new Comments($dbTiki);
if(!isset($comments_prefix_var)) {
   $comments_prefix_var='';
}
if(!isset($comments_object_var) || (!$comments_object_var) || !isset($_REQUEST[$comments_object_var])){
   die("the comments_object_var variable is not set or cannot be found as a REQUEST variable");
}
$comments_objectId = $comments_prefix_var.$_REQUEST["$comments_object_var"];
// Process a post form here 
if($tiki_p_post_comments == 'y') {
  if(isset($_REQUEST["comments_postComment"])) {
    $comments_show='y';
    if( (!empty($_REQUEST["comments_title"])) && (!empty($_REQUEST["comments_data"])) ){
      if(!isset($_REQUEST["comments_parentId"])) {
        $_REQUEST["comments_parentId"]=0;
      }
      //Replace things between square brackets by links
      $_REQUEST["comments_data"]=strip_tags($_REQUEST["comments_data"]);
      $commentslib->post_new_comment($comments_objectId, $_REQUEST["comments_parentId"], $user, $_REQUEST["comments_title"], nl2br($_REQUEST["comments_data"]));
    }
  }
}

if($tiki_p_vote_comments == 'y') {
  // Process a vote here
  if(isset($_REQUEST["comments_vote"])&&isset($_REQUEST["comments_threadId"])) {
   $comments_show='y';
   if(!$tikilib->user_has_voted($user,'comment'.$_REQUEST["comments_threadId"])) {
    $commentslib->vote_comment($_REQUEST["comments_threadId"],$user,$_REQUEST["comments_vote"]);
    $tikilib->register_user_vote($user,'comment'.$_REQUEST["comments_threadId"]);
   }
  }
}

if($tiki_p_remove_comments == 'y') {
  if(isset($_REQUEST["comments_remove"])&&isset($_REQUEST["comments_threadId"])) {
   $comments_show='y';
   $commentslib->remove_comment($_REQUEST["comments_threadId"]);
  }
}

// Check for settings
if(!isset($_REQUEST["comments_maxComments"])) {
 $_REQUEST["comments_maxComments"]=$comments_per_page;
} else {
 $comments_show='y';
}

if(!isset($_REQUEST["comments_sort_mode"])) {
 $_REQUEST["comments_sort_mode"]=$comments_default_ordering;
} else {
 $comments_show='y';
}

if(!isset($_REQUEST["comments_commentFind"])) {
 $_REQUEST["comments_commentFind"]='';
} else {
 $comments_show='y';
}

$smarty->assign('comments_maxComments',$_REQUEST["comments_maxComments"]);
$smarty->assign('comments_sort_mode',$_REQUEST["comments_sort_mode"]);
$smarty->assign('comments_commentFind',$_REQUEST["comments_commentFind"]);
$smarty->assign('comments_show',$comments_show);
//print("Show: $comments_show<br/>");
// Offset setting for the list of comments
if(!isset($_REQUEST["comments_offset"])) {
 $comments_offset = 0;
} else {
 $comments_offset = $_REQUEST["comments_offset"];
}

$smarty->assign('comments_offset',$comments_offset);

// Now check if we are displaying top-level comments or a specific comment
if(!isset($_REQUEST["comments_parentId"])) {
  $_REQUEST["comments_parentId"] = 0;
}
$smarty->assign('comments_parentId',$_REQUEST["comments_parentId"]);
$comments_coms = $commentslib->get_comments($comments_objectId,$_REQUEST["comments_parentId"],$comments_offset,$_REQUEST["comments_maxComments"],$_REQUEST["comments_sort_mode"], $_REQUEST["comments_commentFind"],$_REQUEST['comments_threshold']);
$comments_cant = $commentslib->count_comments($comments_objectId);
$smarty->assign('comments_below',$comments_coms["below"]);
$smarty->assign('comments_cant',$comments_cant);

//print_r($comments_coms);
// Offset management
$comments_maxRecords = $_REQUEST["comments_maxComments"];
$comments_cant_pages = ceil($comments_coms["cant"] / $comments_maxRecords);
$smarty->assign('comments_cant_pages',$comments_cant_pages);
$smarty->assign('comments_actual_page',1+($comments_offset/$comments_maxRecords));
if($comments_coms["cant"] > ($comments_offset+$comments_maxRecords)) {
$smarty->assign('comments_next_offset',$comments_offset + $comments_maxRecords);
} else {
  $smarty->assign('comments_next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($comments_offset>0) {
  $smarty->assign('comments_prev_offset',$comments_offset - $comments_maxRecords);  
} else {
  $smarty->assign('comments_prev_offset',-1); 
}
$smarty->assign('comments_coms',$comments_coms["data"]);
?>