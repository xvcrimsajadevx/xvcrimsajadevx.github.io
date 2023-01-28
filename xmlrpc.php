<?
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
include_once('lib/userslib.php');
include_once("lib/xmlrpc.inc");
include_once("lib/xmlrpcs.inc");



$tikilib = new Tikilib($dbTiki);
$userlib = new Userslib($dbTiki);

if($tikilib->get_preference("feature_xmlrpc",'n') != 'y') {
  die;  
}


$map = array (
        "blogger.newPost" => array( "function" => "newPost"),
        "blogger.getUserInfo" => array( "function" => "getUserInfo"),
        "blogger.getPost" => array( "function" => "getPost"),
        "blogger.editPost" => array( "function" => "editPost"),
        "blogger.deletePost" => array( "function" => "deletePost"),
        "blogger.getRecentPosts" => array( "function" => "getRecentPosts"),
        "blogger.getUserInfo" => array( "function" => "getUserInfo"),
        "blogger.getUsersBlogs" => array( "function" => "getUserBlogs")
        
);

$s=new xmlrpc_server( $map );

/* Validates the user and returns user information */
function getUserInfo($params) {
 global $tikilib,$userlib;
 $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
 $usernamep=$params->getParam(1); $username=$usernamep->scalarval();
 $passwordp=$params->getParam(2); $password=$passwordp->scalarval();
 if($userlib->validate_user($username,$password)) {
   $myStruct=new xmlrpcval(array("nickname" => new xmlrpcval($username),
                                 "firstname" => new xmlrpcval("none"),
                                 "lastname" => new xmlrpcval("none"),
                                 "email" => new xmlrpcval("none"),
                                 "userid" => new xmlrpcval("$username"),
                                 "url" => new xmlrpcval("none")
                                 ),"struct");
   return new xmlrpcresp($myStruct);
 } else {
    return new xmlrpcresp(0, 101, "Invalid username or password");
 } 
}
 
/* Posts a new submission to the CMS */
function newPost($params) {
  global $tikilib,$userlib;
  $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
  $blogidp=$params->getParam(1); $blogid=$blogidp->scalarval();
  $usernamep=$params->getParam(2); $username=$usernamep->scalarval();
  $passwordp=$params->getParam(3); $password=$passwordp->scalarval();
  $passp=$params->getParam(4); $content=$passp->scalarval();
  $passp=$params->getParam(5); $publish=$passp->scalarval();

  // Now check if the user is valid and if the user can post a submission
  if(!$userlib->validate_user($username,$password)) {
    return new xmlrpcresp(0, 101, "Invalid username or password");
  }
 
  if(!$userlib->user_has_permission($username,'tiki_p_blog_post')) {
    return new xmlrpcresp(0, 101, "User is not allowed to post");
  }
  
  // User ok and can submit then submit an article
  $now=date("U");
  
  $id = $tikilib->blog_post($blogid,$content,$user);
   
  return new xmlrpcresp(new xmlrpcval("$id"));
}

// :TODO: editPost
function editPost($params) {
  global $tikilib,$userlib;
  $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
  $blogidp=$params->getParam(1); $postid=$blogidp->scalarval();
  $usernamep=$params->getParam(2); $username=$usernamep->scalarval();
  $passwordp=$params->getParam(3); $password=$passwordp->scalarval();
  $passp=$params->getParam(4); $content=$passp->scalarval();
  $passp=$params->getParam(5); $publish=$passp->scalarval();

  // Now check if the user is valid and if the user can post a submission
  if(!$userlib->validate_user($username,$password)) {
    return new xmlrpcresp(0, 101, "Invalid username or password");
  }
 
  if(!$userlib->user_has_permission($username,'tiki_p_blog_post')) {
    return new xmlrpcresp(0, 101, "User is not allowed to post");
  }
  
  // Now get the post information
  $post_data = $tikilib->get_post($postid);
  if(!$post_data) {
    return new xmlrpcresp(0, 101, "Post not found");
  }
  
  if($post_data["user"]!=$username) {
    if(!$userlib->user_has_permission($username,'tiki_p_blog_admin')) {
      return new xmlrpcresp(0, 101, "Permission denied to edit that post");
    }
  }
 
  // User ok and can submit then submit an article
  $now=date("U");
  $id = $tikilib->update_post($postid,$content,$user);
  return new xmlrpcresp(new xmlrpcval(1,"boolean"));
}

// :TODO: deletePost
function deletePost($params) {
  global $tikilib,$userlib;
  $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
  $blogidp=$params->getParam(1); $postid=$blogidp->scalarval();
  $usernamep=$params->getParam(2); $username=$usernamep->scalarval();
  $passwordp=$params->getParam(3); $password=$passwordp->scalarval();
  $passp=$params->getParam(4); $publish=$passp->scalarval();

  // Now check if the user is valid and if the user can post a submission
  if(!$userlib->validate_user($username,$password)) {
    return new xmlrpcresp(0, 101, "Invalid username or password");
  }
 
  
  // Now get the post information
  $post_data = $tikilib->get_post($postid);
  if(!$post_data) {
    return new xmlrpcresp(0, 101, "Post not found");
  }
  
  if($post_data["user"]!=$username) {
    if(!$userlib->user_has_permission($username,'tiki_p_blog_admin')) {
      return new xmlrpcresp(0, 101, "Permission denied to edit that post");
    }
  }
 
  // User ok and can submit then submit an article
  $now=date("U");
  $id = $tikilib->remove_post($postid);
  return new xmlrpcresp(new xmlrpcval(1,"boolean"));
}


// :TODO: getTemplate

// :TODO: setTemplate

// :TODO: getPost
function getPost($params) {
  global $tikilib,$userlib;
  $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
  $blogidp=$params->getParam(1); $postid=$blogidp->scalarval();
  $usernamep=$params->getParam(2); $username=$usernamep->scalarval();
  $passwordp=$params->getParam(3); $password=$passwordp->scalarval();

  // Now check if the user is valid and if the user can post a submission
  if(!$userlib->validate_user($username,$password)) {
    return new xmlrpcresp(0, 101, "Invalid username or password");
  }
 
  if(!$userlib->user_has_permission($username,'tiki_p_blog_post')) {
    return new xmlrpcresp(0, 101, "User is not allowed to post");
  }
  
  // Now get the post information
  $post_data = $tikilib->get_post($postid);
  if(!$post_data) {
    return new xmlrpcresp(0, 101, "Post not found");
  }

  $dateCreated=date("Ymd",$post_data["created"])."T".date("h:i:s",$post_data["created"]);

  $myStruct=new xmlrpcval(array("userid" => new xmlrpcval($username),
                                 "dateCreated" => new xmlrpcval($dateCreated),
                                 "content" => new xmlrpcval($post_data["data"]),
                                 "postid" => new xmlrpcval($post_data["postId"])
                                 ),"struct");
  
 
  // User ok and can submit then submit an article
  
  return new xmlrpcresp($myStruct);
}

// :TODO: getRecentPosts
function getRecentPosts($params) {
  global $tikilib,$userlib;
  $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
  $blogidp=$params->getParam(1); $blogid=$blogidp->scalarval();
  $usernamep=$params->getParam(2); $username=$usernamep->scalarval();
  $passwordp=$params->getParam(3); $password=$passwordp->scalarval();
  $passp=$params->getParam(4); $number=$passp->scalarval();

  // Now check if the user is valid and if the user can post a submission
  if(!$userlib->validate_user($username,$password)) {
    return new xmlrpcresp(0, 101, "Invalid username or password");
  }
 
  if(!$userlib->user_has_permission($username,'tiki_p_blog_post')) {
    return new xmlrpcresp(0, 101, "User is not allowed to post");
  }
  
  // Now get the post information
  $posts = $tikilib->list_blog_posts($blogid, 0, $number,'created_desc', '', '');
  if(count($posts)==0) {
    return new xmlrpcresp(0, 101, "No posts");
  }

  $arrayval = Array();
  foreach($posts["data"] as $post) {
    $dateCreated=date("Ymd",$post["created"])."T".date("h:i:s",$post["created"]);    
    $myStruct=new xmlrpcval(array("userid" => new xmlrpcval($username),
                                 "dateCreated" => new xmlrpcval($dateCreated),
                                 "content" => new xmlrpcval($post["data"]),
                                 "postid" => new xmlrpcval($post["postId"])
                                 ),"struct");
    $arrayval[]=$myStruct;
  }  

 
  // User ok and can submit then submit an article
  
 $myVal=new xmlrpcval($arrayval, "array");
 return new xmlrpcresp($myVal);
}


// :TODO: tiki.tikiPost

/* Get the topics where the user can post a new */
function getUserBlogs($params) {
 global $tikilib,$userlib;
 $appkeyp=$params->getParam(0); $appkey=$appkeyp->scalarval();
 $usernamep=$params->getParam(1); $username=$usernamep->scalarval();
 $passwordp=$params->getParam(2); $password=$passwordp->scalarval();
 
 $arrayVal=Array();
 
 $blogs = $tikilib->list_user_blogs($username,true);
 $foo = parse_url($_SERVER["REQUEST_URI"]);
 $foo1='http://'.$_SERVER["SERVER_NAME"].str_replace("xmlrpc","tiki-view_blog",$foo["path"]);

 foreach($blogs as $blog) {
   $myStruct=new xmlrpcval(array("blogName" => new xmlrpcval($blog["title"]),
                               "url" => new xmlrpcval($foo1."?blogId=".$blog["blogId"]),
                               "blogid" => new xmlrpcval($blog["blogId"])),"struct");
   $arrayVal[] = $myStruct;                              
 }
 
 $myVal=new xmlrpcval($arrayVal, "array");
 return new xmlrpcresp($myVal);
}


?>