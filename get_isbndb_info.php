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
  if (isset($_GET['isbn'])) {
    $isbn = $_GET['isbn'];
    //Look up the ISBN value on the ISBNdb.com system
    list($lookup_isbn, $lookup_book_title, $lookup_author_name, $lookup_publisher, $lookup_cover_type, $lookup_pages, $lookup_copyright_year, $lookup_notes, $lookup_genre) = read_isbndb_json($isbn);
  } else {
    $lookup_isbn = "";
    $lookup_book_title = ""; 
    $lookup_author_name = ""; 
    $lookup_publisher = ""; 
    $lookup_cover_type = ""; 
    $lookup_pages = ""; 
    $lookup_copyright_year = ""; 
    $lookup_notes = ""; 
    $lookup_genre = ""; 
  }
  switch ($response) {
    case "json":
      echo json_encode(array("isbn" => $lookup_isbn, "title" => $lookup_book_title, "author" => $lookup_author_name, "publisher" => $lookup_publisher, "cover" => $lookup_cover_type, "pages" => $lookup_pages, "copyright" => $lookup_copyright_year, "genre" => $lookup_genre, "notes" => $lookup_notes));
      break;
    default:
    case "xml":
      $lookup_book_title = htmlspecialchars($lookup_book_title);
      $lookup_author_name = htmlspecialchars($lookup_author_name);
      $lookup_publisher = htmlspecialchars($lookup_publisher);
      $lookup_notes = iconv("UTF-8", "ISO-8859-1//IGNORE", $lookup_notes);
      $lookup_notes = htmlspecialchars($lookup_notes);
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>
<book>
  <isbn>$lookup_isbn</isbn>
  <title>$lookup_book_title</title>
  <author>$lookup_author_name</author>
  <publisher>$lookup_publisher</publisher>
  <cover>$lookup_cover_type</cover>
  <pages>$lookup_pages</pages>
  <copyright>$lookup_copyright_year</copyright>
  <genre>$lookup_genre</genre>
  <notes>$lookup_notes</notes>
</book>";
      break;
  }
?>
