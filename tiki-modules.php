<?php

clearstatcache();
$now=date("U");

if($user != 'admin') {
  $user_groups = $userlib->get_user_groups($user);
  } else {
  $allgroups = $userlib->get_groups(0,-1,'groupName_desc',''); 	
  $user_groups = Array();
  foreach($allgroups["data"] as $grp) {
    $user_groups[]=$grp["groupName"];	
  }
}




// Now get left modules from the assigned modules
$left_modules = $tikilib->get_assigned_modules('l');
// Now get right modules from the assigned modules
$right_modules = $tikilib->get_assigned_modules('r');
//print_r($right_modules);

for($i=0;$i<count($left_modules);$i++) {
    $r=&$left_modules[$i];
    
    $pass = 'y';
    if($modallgroups != 'y') {
      // Check for the right groups
      if($r["groups"]) {
        $module_groups = unserialize($r["groups"]);
      } else {
        $module_groups = Array();	
      }
      $pass = 'n';
      foreach($module_groups as $mod_group) {
        if(in_array($mod_group,$user_groups)) {
          $pass = 'y';	
        }	
      }
    }

    if($pass == 'y') {
    $cachefile = 'modules/cache/mod-'.$r["name"].'.tpl.cache';       
    $phpfile = 'modules/mod-'.$r["name"].'.php';
    $template= 'modules/mod-'.$r["name"].'.tpl';
    $nocache= 'templates/modules/mod-'.$r["name"].'.tpl.nocache';
    //print("Cache: $cachefile PHP: $phpfile Template: $template<br/>");
    if(!$r["rows"]) $r["rows"]=10;
    $module_rows=$r["rows"];
    
    //$mnm = $r["name"]."_module_title";
    //$smarty->assign_by_ref($mnm,$r["title"]);
    //$smarty->assign_by_ref('module_rows',$r["rows"]);
    if((!file_exists($cachefile)) || (file_exists($nocache)) || ( ($now - filemtime($cachefile))>$r["cache_time"] )){
      //print("Refrescar cache<br/>");
      $r["data"]='';
      if(file_exists($phpfile)) {
        //print("Haciendo el include<br/>");
        // If we have a php file then use it!
        include_once($phpfile);
      }
      // If there's a template we use it
      $template_file = 'templates/'.$template;
      //print("Template file: $template_file<br/>");
      if(file_exists($template_file)) {
        //print("FETCH<br/>");
        $data = $smarty->fetch($template);
      } else {
        if($tikilib->is_user_module($r["name"])) {
          //print("Es user module");
          $info = $tikilib->get_user_module($r["name"]);
          // Ahora usar el template de user
          $smarty->assign_by_ref('user_title',$info["title"]);
          $smarty->assign_by_ref('user_data',$info["data"]);
          $data = $smarty->fetch('modules/user_module.tpl');
        }
      }
      $r["data"] = $data;
      $fp = fopen($cachefile,"w+");
      fwrite($fp,$data,strlen($data));
      fclose($fp);
    } else {
      //print("Usando cache<br/>");
      $fp = fopen($cachefile,"r");
      $data = fread($fp,filesize($cachefile));
      fclose($fp);
      $r["data"] = $data;
    }
    }
}


for($i=0;$i<count($right_modules);$i++) {
    $r=&$right_modules[$i];
    $pass = 'y';
    if($modallgroups != 'y') {
      // Check for the right groups
      if($r["groups"]) {
        $module_groups = unserialize($r["groups"]);
      } else {
        $module_groups = Array();	
      }
      $pass = 'n';
      foreach($module_groups as $mod_group) {
        if(in_array($mod_group,$user_groups)) {
          $pass = 'y';	
        }	
      }
    }
    
    if($pass == 'y'){
    $cachefile = 'modules/cache/mod-'.$r["name"].'.tpl.cache';       
    $phpfile = 'modules/mod-'.$r["name"].'.php';
    $template= 'modules/mod-'.$r["name"].'.tpl';
    $nocache= 'templates/modules/mod-'.$r["name"].'.tpl.nocache';
    if(!$r["rows"]) $r["rows"]=10;
    $module_rows=$r["rows"];
    //print("Cache: $cachefile PHP: $phpfile Template: $template<br/>");
    if((!file_exists($cachefile)) || (file_exists($nocache)) || ( ($now - filemtime($cachefile))>$r["cache_time"] )){
      $r["data"]='';
      if(file_exists($phpfile)) {
        //print("Haciendo el include<br/>");
        // If we have a php file then use it!
        include_once($phpfile);
      }
      // If there's a template we use it
      $template_file = 'templates/'.$template;
      //print("Template file: $template_file<br/>");
      if(file_exists($template_file)) {
        //print("FETCH<br/>");
        $data = $smarty->fetch($template);
      } else {
        if($tikilib->is_user_module($r["name"])) {
          //print("Es user module");
          $info = $tikilib->get_user_module($r["name"]);
          
          // Ahora usar el template de user
          $smarty->assign_by_ref('user_title',$info["title"]);
          $smarty->assign_by_ref('user_data',$info["data"]);
          $data = $smarty->fetch('modules/user_module.tpl');
        }
      }
      $r["data"] = $data;
      $fp = fopen($cachefile,"w+");
      fwrite($fp,$data,strlen($data));
      fclose($fp);
    } else {
      //print("Usando cache<br/>");
      $fp = fopen($cachefile,"r");
      $data = fread($fp,filesize($cachefile));
      fclose($fp);
      $r["data"] = $data;
    }
    }
}


$smarty->assign_by_ref('right_modules',$right_modules);
$smarty->assign_by_ref('left_modules',$left_modules);

?>