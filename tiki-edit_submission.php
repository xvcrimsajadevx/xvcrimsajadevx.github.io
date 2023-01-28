<?php
// Initialization
require_once('tiki-setup.php');

if($feature_submissions != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


// Now check permissions to access this page
if($tiki_p_submit_article != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot send submissions"));
  $smarty->display('error.tpl');
  die;  
}

if($tiki_p_admin != 'y') {
  if($tiki_p_use_HTML != 'y') {
    $_REQUEST["allowhtml"] = 'off';
  }
}

if(isset($_REQUEST["subId"])) {
  $subId = $_REQUEST["subId"];
} else {
  $subId = 0;
}
$smarty->assign('subId',$subId);
$smarty->assign('allowhtml','y');
$publishDate=date("U");
$smarty->assign('title','');
$smarty->assign('authorName','');
$smarty->assign('topicId','');
$smarty->assign('useImage','n');
$hasImage='n';
$smarty->assign('hasImage','n');
$smarty->assign('image_name','');
$smarty->assign('image_type','');
$smarty->assign('image_size','');
$smarty->assign('image_x',0);
$smarty->assign('image_y',0);
$smarty->assign('heading','');
$smarty->assign('body','');
$smarty->assign('publishDate',$publishDate);
$smarty->assign('edit_data','n');

if(isset($_REQUEST["subId"])) {
if($_REQUEST["subId"]>0) {
  if($tiki_p_edit_submission != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot edit submissions"));
    $smarty->display('error.tpl');
    die;  
  }
}
}

// If the submissionId is passed then get the submission data
if(isset($_REQUEST["subId"])) {
  $article_data = $tikilib->get_submission($_REQUEST["subId"]);
  $smarty->assign('title',$article_data["title"]);
  $smarty->assign('authorName',$article_data["authorName"]);
  $smarty->assign('topicId',$article_data["topicId"]);
  $smarty->assign('useImage',$article_data["useImage"]);
  $smarty->assign('image_name',$article_data["image_name"]);
  $smarty->assign('image_type',$article_data["image_type"]);
  $smarty->assign('image_size',$article_data["image_size"]);
  $smarty->assign('image_data',urlencode($article_data["image_data"]));
  $smarty->assign('reads',$article_data["reads"]);
  $smarty->assign('image_x',$article_data["image_x"]);
  $smarty->assign('image_y',$article_data["image_y"]);
  if(strlen($article_data["image_data"])>0) {
    $smarty->assign('hasImage','y');
    $hasImage='y';
  }
  $smarty->assign('heading',$article_data["heading"]);
  $smarty->assign('body',$article_data["body"]);
  $smarty->assign('publishDate',$article_data["publishDate"]);
  $smarty->assign('edit_data','y');
  
  $data = $article_data["image_data"];
  $imgname = $article_data["image_name"];
  
  if($hasImage=='y') {
     $tmpfname = tempnam ("/tmp", "TMPIMG").$imgname;     
     $fp = fopen($tmpfname,"w");
     if($fp) {
       fwrite($fp,$data);
       fclose($fp);
       $smarty->assign('tempimg',$tmpfname);
     } else {
       $smarty->assign('tempimg','n');
     }
  }
  
  $body = $article_data["body"];
  $heading = $article_data["heading"]; 
  $smarty->assign('parsed_body',$tikilib->parse_data($body));
  $smarty->assign('parsed_heading',$tikilib->parse_data($heading));
}

if(isset($_REQUEST["allowhtml"])) {
  if($_REQUEST["allowhtml"] == "on") {
    $smarty->assign('allowhtml','y');
  }
}


$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $smarty->assign('reads','0');
  $smarty->assign('preview',1); 
  $smarty->assign('edit_data','y');
  $smarty->assign('title',strip_tags($_REQUEST["title"],'<a><pre><p><img><hr>'));
  $smarty->assign('authorName',$_REQUEST["authorName"]);
  $smarty->assign('topicId',$_REQUEST["topicId"]);
  if(isset($_REQUEST["useImage"])&&$_REQUEST["useImage"]=='on') {
    $useImage = 'y';
  } else {
    $useImage = 'n';
  }
  
  $smarty->assign('image_data',$_REQUEST["image_data"]);
  if(strlen($_REQUEST["image_data"])>0) {
    $smarty->assign('hasImage','y');
    $hasImage = 'y';
  }
  $smarty->assign('image_name',$_REQUEST["image_name"]);
  $smarty->assign('image_type',$_REQUEST["image_type"]);
  $smarty->assign('image_size',$_REQUEST["image_size"]);
  $smarty->assign('image_x',$_REQUEST["image_x"]);
  $smarty->assign('image_y',$_REQUEST["image_y"]);
  $smarty->assign('useImage',$useImage);
  $imgname=$_REQUEST["image_name"];
  $data=urldecode($_REQUEST["image_data"]);

  $publishDate = mktime($_REQUEST["Time_Hour"],$_REQUEST["Time_Minute"],0,
                        $_REQUEST["Date_Month"],$_REQUEST["Date_Day"],$_REQUEST["Date_Year"]);
  $smarty->assign('publishDate',$publishDate);
  // Parse the information of an uploaded file and use it for the preview
  if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    $fp = fopen($_FILES['userfile1']['tmp_name'],"r");
    $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
    fclose($fp);
    $imgtype = $_FILES['userfile1']['type'];
    $imgsize = $_FILES['userfile1']['size'];
    $imgname = $_FILES['userfile1']['name'];
    $smarty->assign('image_data',urlencode($data));
    $smarty->assign('image_name',$imgname);
    $smarty->assign('image_type',$imgtype);
    $smarty->assign('image_size',$imgsize);
    $hasImage='y';
    $smarty->assign('hasImage','y');
  }
  
  if($hasImage=='y') {
     $tmpfname = tempnam ("/tmp", "TMPIMG").$imgname;     
     $fp = fopen($tmpfname,"w");
     if($fp) {
       fwrite($fp,$data);
       fclose($fp);
       $smarty->assign('tempimg',$tmpfname);
     } else {
       $smarty->assign('tempimg','n');
     }
  }
    
  $smarty->assign('heading',$_REQUEST["heading"]);
  $smarty->assign('edit_data','y');
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $body = $_REQUEST["body"];  
    $heading = $_REQUEST["heading"];
  } else {
    $body = strip_tags($_REQUEST["body"],'<a><pre><p><img><hr>');
    $heading = strip_tags($_REQUEST["heading"],'<a><pre><p><img><hr>');
  }
  $smarty->assign('size',strlen($body));
  $smarty->assign('body',$body);
  $smarty->assign('heading',$heading);
  $smarty->assign('parsed_body',$tikilib->parse_data($body));
  $smarty->assign('parsed_heading',$tikilib->parse_data($heading));
} 

// Pro
if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["allowhtml"]) && $_REQUEST["allowhtml"]=="on") {
    $body = $_REQUEST["body"];  
    $heading  = $_REQUEST["heading"];
  } else {
    $body = strip_tags($_REQUEST["body"],'<a><pre><p><img><hr>');
    $heading = strip_tags($_REQUEST["heading"],'<a><pre><p><img><hr>');
  }
  if(isset($_REQUEST["useImage"])&&$_REQUEST["useImage"]=='on') {
    $useImage = 'y';
  } else {
    $useImage = 'n';
  }

  $publishDate = mktime($_REQUEST["Time_Hour"],$_REQUEST["Time_Minute"],0,
                        $_REQUEST["Date_Month"],$_REQUEST["Date_Day"],$_REQUEST["Date_Year"]);
  $smarty->assign('publishDate',$publishDate);
  
  $imgdata=urldecode($_REQUEST["image_data"]);
  if(strlen($imgdata)>0) {
    $hasImage = 'y';
  }
  $imgname=$_REQUEST["image_name"];
  $imgtype=$_REQUEST["image_type"];
  $imgsize=$_REQUEST["image_size"];
  
  if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    $fp = fopen($_FILES['userfile1']['tmp_name'],"r");
    $imgdata = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
    fclose($fp);
    $imgtype = $_FILES['userfile1']['type'];
    $imgsize = $_FILES['userfile1']['size'];
    $imgname = $_FILES['userfile1']['name'];
  }
  
  // Parse $edit and eliminate image references to external URIs (make them internal)
  $body = $tikilib->capture_images($body);
  $heading = $tikilib->capture_images($heading);
  // If page exists
  $tikilib->replace_submission(strip_tags($_REQUEST["title"],'<a><pre><p><img><hr>'),
                            $_REQUEST["authorName"],
                            $_REQUEST["topicId"],
                            $useImage,
                            $imgname,
                            $imgsize,
                            $imgtype,
                            $imgdata,
                            $heading,
                            $body,
                            $publishDate,
                            $user,
                            $subId,
                            $_REQUEST["image_x"],
                            $_REQUEST["image_y"]);
  $links = $tikilib->get_links($body);
  $tikilib->cache_links($links);
  $links = $tikilib->get_links($heading);
  $tikilib->cache_links($links);
  header("location: tiki-list_submissions.php");
  die;
}

// Armar un select con los topics
$topics = $tikilib->list_topics();
$smarty->assign_by_ref('topics',$topics);


// Display the Index Template
$smarty->assign('mid','tiki-edit_submission.tpl');
$smarty->assign('show_page_bar','n');
$smarty->display('tiki.tpl');
?>