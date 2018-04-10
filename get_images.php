<?php
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: -1");
  $c_dir = scandir("thumbs");
  $o_dir[0] = "init";
  array_pop($o_dir);
  foreach($c_dir as $i) {
    if (preg_match('/.*?\.jpg$/is', $i)) {
      array_push($o_dir, $i);
    }
  }
  echo json_encode($o_dir);
?>
