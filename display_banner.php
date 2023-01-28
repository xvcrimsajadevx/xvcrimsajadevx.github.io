<?
// Only to be called from edit_banner or view_banner to display the banner without adding
// impressions to the banner

if(!isset($_REQUEST["id"])) {
  die;
}
include_once('db/tiki-db.php');
include_once('lib/tikilib.php');
$tikilib = new Tikilib($dbTiki);

$data = $tikilib->get_banner($_REQUEST["id"]);
$id = $data["bannerId"];
switch($data["which"]) {
  case 'useHTML':
    $raw = $data["HTMLData"];
    break;
  case 'useImage':
    $raw = "<img border='0' src=\"banner_image.php?id=".$id."\" />";
    break;
  case 'useFixedURL':
    $fp = fopen($data["fixedURLData"],"r");
    if ($fp) {
      $raw = fread($fp,999999);
    }
    fclose($fp);
    break;
  case 'useText':
    $raw = $data["textData"];
    break;
}
print($raw);
?>