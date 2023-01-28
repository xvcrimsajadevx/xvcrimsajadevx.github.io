<?php

class timer
        {
        function parseMicro($micro)
                {list($micro,$sec)=explode(' ',microtime()); return $sec+$micro;}
        function start($timer='default')
                {$this->timer[$timer]=$this->parseMicro(microtime());}
        function stop($timer='default')
                {return $this->current($timer);}
        function elapsed($timer='default')
                {return $this->parseMicro(microtime()) - $this->timer[$timer];}
        }

$tiki_timer = new timer();
$tiki_timer->start();

require_once("setup.php");
require_once("lib/tikilib.php");
require_once("lib/userslib.php");

$userlib = new UsersLib($dbTiki);
if(isset($_SESSION["user"])) {
  $user = $_SESSION["user"];  
} else {
  $user = false;
}

// The votes array stores the votes the user has made
if(!isset($_SESSION["votes"])) {
  $votes=Array();
  session_register("votes");
}

$allperms = $userlib->get_permissions(0,-1,'permName_desc','','');
$allperms = $allperms["data"];
foreach($allperms as $vperm) {
  $perm=$vperm["permName"];
  if($user != 'admin') {
    $$perm = 'n';
    $smarty->assign("$perm",'n');  
  } else {
    $$perm = 'y';
    $smarty->assign("$perm",'y');    
  }
}

// Permissions
// Get group permissions here
$perms = $userlib->get_user_permissions($user);
foreach($perms as $perm) {
  //print("Asignando permiso global : $perm<br/>");
  $smarty->assign("$perm",'y');  
  $$perm='y';
}


// Constants for permissions here
define('TIKI_PAGE_RESOURCE','tiki-page');
define('TIKI_P_EDIT','tiki_p_edit');
define('TIKI_P_VIEW','tiki_p_view');


$appname="tiki";
if(!isset($_SESSION["appname"])) {
  session_register("appname");
}
$smarty->assign("appname","tiki");


$tikilib = new TikiLib($dbTiki);
if(isset($GLOBALS["PHPSESSID"])) {
  $tikilib->update_session($GLOBALS["PHPSESSID"]);
}

$home_blog = 0;
$home_gallery = 0;
$home_file_gallery = 0;
$feature_xmlrpc = 'n';
$feature_blog_rankings = 'y';
$feature_cms_rankings = 'y';
$feature_gal_rankings = 'y';
$feature_wiki_rankings = 'y';
$feature_lastChanges =  'y';
$feature_dump =  'y';
$feature_ranking = 'y';
$feature_listPages = 'y';
$feature_history = 'y';
$feature_backlinks = 'y';
$feature_likePages = 'y';
$feature_search = 'y';
$feature_sandbox = 'y';
$feature_userPreferences = 'n';
$feature_userVersions = 'y';
$feature_galleries = 'y';
$feature_featuredLinks = 'y';
$feature_hotwords = 'y';
$feature_banners = 'n';
$feature_top_banner = 'n';
$feature_wiki = 'y';
$feature_articles = 'n';
$feature_submissions = 'n';
$feature_blogs = 'n';
$feature_xmlrpc = 'n';
$feature_edit_templates = 'n';
$feature_dynamic_content = 'n';
$feature_wiki_comments = 'n';
$wiki_comments_default_ordering = 'points_desc';
$wiki_comments_per_page = 10;
$feature_warn_on_edit ='n';
$feature_file_galleries = 'n';
$feature_file_galleries_rankings = 'n';
$language = 'en';
$smarty->assign('feature_file_galleries',$feature_file_galleries);
$smarty->assign('feature_file_galleries_rankings',$feature_file_galleries_rankings);
$smarty->assign('language',$language);
$smarty->assign('home_blog',$home_blog);
$smarty->assign('home_gallery',$home_gallery);
$smarty->assign('home_file_gallery',$home_file_gallery);
$smarty->assign('feature_dynamic_content',$feature_dynamic_content);
$smarty->assign('feature_edit_templates',$feature_edit_templates);
$smarty->assign('feature_top_banner',$feature_top_banner);
$smarty->assign('feature_banners',$feature_banners);
$smarty->assign('feature_xmlrpc',$feature_xmlrpc);
$smarty->assign('feature_cms_rankings',$feature_cms_rankings);
$smarty->assign('feature_blog_rankings',$feature_blog_rankings);
$smarty->assign('feature_gal_rankings',$feature_gal_rankings);
$smarty->assign('feature_wiki_rankings',$feature_wiki_rankings);
$smarty->assign('feature_hotwords',$feature_hotwords);
$smarty->assign('feature_lastChanges',$feature_lastChanges);
$smarty->assign('feature_dump',$feature_dump);
$smarty->assign('feature_ranking',$feature_ranking);
$smarty->assign('feature_listPages', $feature_listPages);
$smarty->assign('feature_history', $feature_history);
$smarty->assign('feature_backlinks', $feature_backlinks);
$smarty->assign('feature_likePages', $feature_likePages);
$smarty->assign('feature_search', $feature_search);
$smarty->assign('feature_sandbox', $feature_sandbox);
$smarty->assign('feature_userPreferences', $feature_userPreferences);
$smarty->assign('feature_userVersions', $feature_userVersions);
$smarty->assign('feature_galleries',$feature_galleries);
$smarty->assign('feature_featuredLinks',$feature_featuredLinks);
$smarty->assign('feature_wiki',$feature_wiki);
$smarty->assign('feature_articles',$feature_articles);
$smarty->assign('feature_submissions',$feature_submissions);
$smarty->assign('feature_blogs',$feature_blogs);
$smarty->assign('feature_xmlrpc',$feature_xmlrpc);
$smarty->assign('feature_wiki_comments',$feature_wiki_comments);
$smarty->assign('wiki_comments_default_ordering',$wiki_comments_default_ordering);
$smarty->assign('wiki_comments_per_page',$wiki_comments_per_page);
$smarty->assign('feature_warn_on_edit',$feature_warn_on_edit);

// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$modallgroups = $tikilib->get_preference("modallgroups",'y');
$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php');
$cachepages = $tikilib->get_preference("cachepages",'y');
$cacheimages = $tikilib->get_preference("cacheimages",'y');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$title = $tikilib->get_preference("title","");
$maxRecords = $tikilib->get_preference("maxRecords",10);
$maxArticles = $tikilib->get_preference("maxArticles",10);
$smarty->assign('tikiIndex',$tikiIndex);
$smarty->assign('maxArticles',$maxArticles);
$smarty->assign('popupLinks',$popupLinks);
$smarty->assign('modallgroups',$modallgroups);
$smarty->assign('anonCanEdit',$anonCanEdit);
$smarty->assign('allowRegister',$allowRegister);
$smarty->assign('cachepages',$cachepages);
$smarty->assign('cacheimages',$cacheimages);


$prefs = $tikilib->get_all_preferences();
foreach($prefs as $name => $val) {
  $$name = $val;
  $smarty->assign("$name",$val);
}


global $lang;
include_once('lang/'.$language.'/language.php');
function tra($content)
{
    global $lang;
    if ($content) {
      if(isset($lang[$content])) {
        return $lang[$content];  
      } else {
        return $content;        
      }
    }
}



// Check for FEATURES
$user_style = $tikilib->get_preference("style", 'default.css');
if($user) {
  $user_style = $tikilib->get_user_preference($user,'theme','default.css');
  if($user_style) {
    $style = $user_style;
  }
}
$smarty->assign('style',$user_style);


$smarty->assign('user',$user);
$smarty->assign('lock',false);
$smarty->assign('title',$title);
$smarty->assign('maxRecords',$maxRecords);
include_once("tiki-modules.php");
$smarty->assign('beingEdited','n');

if($feature_warn_on_edit == 'y') {
// Check if the page is being edited
if(isset($_REQUEST["page"])) {
 $chkpage = $_REQUEST["page"];
} else {
 $chkpage = 'HomePage';
}
// Notice if a page is being edited or if it was being edited and not anymore
//print($GLOBALS["HTTP_REFERER"]);
// IF isset the referer and if the referer is editpage then unset taking the pagename from the
// query or homepage if not query

if(isset($GLOBALS["HTTP_REFERER"])) {
  if(strstr($GLOBALS["HTTP_REFERER"],'tiki-editpage')) {
    $purl = parse_url($GLOBALS["HTTP_REFERER"]);
    if(!isset($purl["query"])) {
      $purl["query"]='';
    }
    parse_str($purl["query"],$purlquery);
    if(!isset($purlquery["page"])) {
      $purlquery["page"]='HomePage';
    }
    $tikilib->semaphore_unset($purlquery["page"]);
  }
}
if(strstr($_SERVER["REQUEST_URI"],'tiki-editpage')) {
  $purl = parse_url($_SERVER["REQUEST_URI"]);
  if(!isset($purl["query"])) {
    $purl["query"]='';
  }
  parse_str($purl["query"],$purlquery);
  if(!isset($purlquery["page"])) {
    $purlquery["page"]='HomePage';
  }
  $tikilib->semaphore_set($purlquery["page"]);
}
if($tikilib->semaphore_is_set($chkpage)) {
  $smarty->assign('beingEdited','y');
  $beingedited='y';
} else {
  $smarty->assign('beingEdited','n');
  $beingedited='n';
}

}

?>