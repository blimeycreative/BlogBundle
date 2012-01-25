<?php
$output = array();
$directory = '../../../../../web/uploads/images'; // Use your correct (relative!) path here
if (is_dir($directory)) {
  $direc = opendir($directory);
  while ($file = readdir($direc)) {
    if (is_file("$directory/$file") && getimagesize("$directory/$file") != FALSE  && preg_match('/\-small/', $file)) {
      $output[] = array(utf8_encode($file), utf8_encode("/uploads/images/$file"));
    }
  }

  closedir($direc);
}

header('Content-type: text/javascript');

header('pragma: no-cache');
header('expires: 0');
?>
var tinyMCEImageList = <?= json_encode($output) ?>;