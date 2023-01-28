<?php
// Initialization
require_once('tiki-setup.php');

/*
if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
*/

// Now check permissions to access this page
if($tiki_p_upload_files != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot upload images"));
  $smarty->display('error.tpl');
  die;  
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-upload_file","tiki-download_file",$foo["path"]);
$smarty->assign('url_browse',$_SERVER["SERVER_NAME"].$foo1);



$smarty->assign('show','n');
// Process an upload here
if(isset($_REQUEST["upload"])) {
  // Check here if it is an upload or an URL
  $gal_info = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
  // Check the user to be admin or owner or the gallery is public
  if($tiki_p_admin_file_galleries!='y' && (!$user || $user!=$gal_info["user"]) && $gal_info["public"]!='y') {
    $smarty->assign('msg',tra("Permission denied you can upload files but not to this file gallery"));
    $smarty->display('error.tpl');
    die;  
  }
  $error_msg='';
  /*
  if(!empty($_REQUEST["url"])) {
    // Get the file from a URL
     $data='';
     $fp = @fopen($file,"rb");
     if($fp) {
       while(!feof($fp))
       {
         $data.= fread($fp,1024);
       }
       fclose($fp);
       $url_info = parse_url($_REQUEST["url"]);
       $pinfo = pathinfo($url_info["path"]);
       $name = $pinfo["basename"];
       $size = strlen($data);
      } else {
        $error_msg=tra("Cannot get file from URL");
      }
   } else {
   */
     // We process here file uploads
     if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
       $fp = fopen($_FILES['userfile1']['tmp_name'],"r");
       $data = fread($fp,filesize($_FILES['userfile1']['tmp_name']));
       fclose($fp);
       $size = $_FILES['userfile1']['size'];
       $name = $_FILES['userfile1']['name'];
       $type = $_FILES['userfile1']['type'];
     } else {
       $error_msg=tra("cannot process upload");
    }
  /*}*/
  if(empty($_REQUEST["name"])) {
    $error_msg=tra("You have to provide a name to the image");
  }
  if($error_msg) {
    $smarty->assign('msg',$error_msg);
    $smarty->display('error.tpl');
    die;  
  }
  if(!isset($data) || strlen($data)<1) {
     $smarty->assign('msg',tra('Upload was not successful'));
     $smarty->display('error.tpl');
     die;  
  }
  if(isset($data)) {
      $smarty->assign('upload_name',$name);
      $smarty->assign('upload_size',$size);
      $fileId = $tikilib->insert_file($_REQUEST["galleryId"],$_REQUEST["name"],$_REQUEST["description"],$name, $data, $size, $type, $user);
      $smarty->assign_by_ref('fileId',$fileId);
      // Now that the image was inserted we can display the image here.
      $smarty->assign('show','y');
      $smarty->assign_by_ref('tmpfname',$tmpfname);
      $smarty->assign_by_ref('fname',$_REQUEST["url"]);
  }
}

// Get the list of galleries to display the select box in the template
if(isset($_REQUEST["galleryId"])) {
  $smarty->assign_by_ref('galleryId',$_REQUEST["galleryId"]);
} else {
  $smarty->assign('galleryId','');
}
$galleries = $tikilib->list_file_galleries(0,-1,'lastModif_desc', $user,'');
$smarty->assign_by_ref('galleries',$galleries["data"]);

// Display the template
$smarty->assign('mid','tiki-upload_file.tpl');
$smarty->display('tiki.tpl');
?>
