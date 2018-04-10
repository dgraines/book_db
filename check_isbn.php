<?php
  if (isset($_GET['response'])) {
    $response = $_GET['response'];
  } else {
    $response = "xml";
  }
  switch ($response) {
    case "json":
      header('Content-type: application/json');
      header("Cache-Control: no-cache, must-revalidate");
      break;
    default:
    case "xml":
      header('Content-Type: text/xml');
      header("Cache-Control: no-cache, must-revalidate");
      break;
  }
  require('inc_dr_utils.php');
  require('dr_mysql_access.php');

// Checks for an existing record with a field set to a given value.
  if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    $isbn = $_GET['isbn'];
    $isbn = ISBNConvert($isbn);
    $db = mysql_connect($servername, $userid, $passwd) or die ("Can not connect to database: ".mysql_error());
    mysql_select_db("book_collection",$db) or die ("Can not select the database: ".mysql_error());
    $query = "SELECT count(*) FROM books WHERE isbn = '$isbn'";
    $result = mysql_query($query);
    $myrow = mysql_fetch_array($result);
    $count = $myrow[0];
    $query = "SELECT book_id FROM books WHERE isbn = '$isbn'";
    $result = mysql_query($query);
    $myrow = mysql_fetch_array($result);
    mysql_close($db);
  } else {
    $isbn = "Error: Invalid or missing ISBN.";
  }
  switch ($response) {
    case "json":
      echo json_encode(array("isbn" => $isbn, "number" => $count, "bookid" => $myrow[0]));
      break;
    default:
    case "xml":
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<book>
  <isbn>$isbn</isbn>
  <number>$count</number>
  <bookid>$myrow[0]</bookid>
</book>";
      break;
  }
?>
