<?php
  header('Content-Type: text/html');
  header("Cache-Control: no-cache, must-revalidate");
  require ('inc_dr_utils.php');
  if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    $isbn = ISBN10($isbn);
    $ch = curl_init();
    file_put_contents("../images/new_cover.jpg", file_get_contents("http://images.amazon.com/images/P/".$isbn.".01._SL110_TZZZZZZZ.jpg"));
    if (filesize("../images/new_cover.jpg") >= 1000) {
      $parm = time();
      echo "<img src='../images/new_cover.jpg?=$parm' />";
    } else {
      echo "<img src='../images/no_image.jpg' />";
    }
//    curl_setopt($ch, CURLOPT_URL,"http://www.isbndb.com/search-all.html?kw=$isbn");
//    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
//    $result=curl_exec ($ch);
//    if (preg_match('/="(http:\/\/rcm.amazon[^"]+)"/',$result,$results)) {
//      $lookup_pages = $results[1];
//      curl_setopt($ch, CURLOPT_URL, $lookup_pages);
//      $result = curl_exec ($ch);
//      if (preg_match('/="(http:\/\/rcm-images[^"]+)"/',$result,$results)) {
//        $lookup_pages = $results[1];
//        file_put_contents("../images/new_cover.jpg", file_get_contents($lookup_pages));
//      } else {
//        $lookup_pages = "";
//      }
//    } else {
//      $lookup_pages = "";
//    }
//    curl_close ($ch);
//    if ($lookup_pages == "") {
//      echo "<img src='../images/no_image.jpg' />";
//    } else {
//      echo "<img src='../images/new_cover.jpg' />";
//    }
//  } else {
//    echo "<img src='../images/no_image.jpg' />";
  }
?>
