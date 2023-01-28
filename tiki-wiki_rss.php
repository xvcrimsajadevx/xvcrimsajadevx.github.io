<?
require_once('tiki-setup.php');
header("content-type: text/xml");
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-wiki_rss","tiki-index",$foo["path"]);
$foo2=str_replace("tiki-wiki_rss.php","img/tiki.jpg",$foo["path"]);
$home = 'http://'.$_SERVER["SERVER_NAME"].$foo1;
$img = 'http://'.$_SERVER["SERVER_NAME"].$foo2;
$title = $tikilib->get_preference("title","pepe");
$changes =   $tikilib->get_last_changes(10, 0, 20, $sort_mode = 'lastModif_desc');
//print_r($changes);die;
print('<');
print('?xml version="1.0" ?');
print('>');
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/">
<channel rdf:about="<?=$home?>">
  <title><?=$title?></title>
  <link><?=$home?></link>
  <description>
    Last modifications to the Wiki.
  </description>
  <image rdf:resource="<?=$img?>" />
  <items>
    <rdf:Seq>
      <?php
        // LOOP collecting last changes to the wiki
        foreach($changes["data"] as $chg) {
          print('<rdf:li resource="'.$home.'?page='.$chg["pageName"].'">'."\n");
          print('<title>'.$chg["pageName"].' '.$chg["action"].'</title>'."\n");
          print('<link>'.$home.'?page='.$chg["pageName"].'</link>'."\n");
          $data = date("m/d/Y h:i",$chg["lastModif"]);
          if(!empty($chg["comment"])) {
            $comment="(".$chg["comment"].")";
          } else {
            $comment='';
          }
          print('<description>'."[$data] :".$chg["action"]." ".$chg["pageName"].$comment.'</description>'."\n");
          print('</rdf:li>'."\n");
        }        
      ?>
    </rdf:Seq>  
  </items>
</channel>
</rdf:RDF>       