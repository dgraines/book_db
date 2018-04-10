<?php
  require_once('dr_mysql_access.php');
  require_once('wed_php_mysql.inc');
  require_once('inc_dr_utils.php');

  // start session if not already started
  session_start();
  if (!isset($_SESSION['logged_in'])) {
    exit ("Error: Not logged in.");
  }
  if (!isset($_SESSION['valid_user'])) {
    exit ("Error: Invalid User name.");
  }

  // Copy form values to local PHP variables. Strip slashes (encoded for Posts)
  // and modify single quotes so that they will go into the MySQL database.
  $a_books_author_name = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_author_name', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_book_title = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_book_title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_publisher = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_publisher', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_edition_number = filter_input(INPUT_POST, 'books_edition_number', FILTER_VALIDATE_INT);
  $a_books_copyright_year = filter_input(INPUT_POST, 'books_copyright_year', FILTER_VALIDATE_INT);
  $a_books_isbn = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_isbn', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_cover_type = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_cover_type', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_pages = filter_input(INPUT_POST, 'books_pages', FILTER_VALIDATE_INT);
  $a_books_date_purchased = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_date_purchased', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_purchase_price = filter_input(INPUT_POST, 'books_purchase_price', FILTER_VALIDATE_FLOAT);
  $a_books_genre = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_genre', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_finished = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_finished', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_books_notes = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'books_notes', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_processmode = filter_input(INPUT_POST, 'processmode', FILTER_VALIDATE_INT);
  $a_bookid = filter_input(INPUT_GET, 'bookid', FILTER_VALIDATE_INT);
  $a_lu_title = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_author = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_author', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_publisher = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_publisher', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_copyright = filter_input(INPUT_POST, 'lu_copyright', FILTER_VALIDATE_INT);
  $a_lu_genre = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_genre', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_cover = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_cover', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_isbn = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_isbn', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));
  $a_lu_pages = filter_input(INPUT_POST, 'lu_pages', FILTER_VALIDATE_INT);
  $a_lu_notes = htmlspecialchars_decode(str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'lu_notes', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES))));
  $a_lu_image = filter_input(INPUT_POST, 'lu_image', FILTER_UNSAFE_RAW);
  $a_coverFile = str_replace("'", "''", stripslashes(filter_input(INPUT_POST, 'coverFile', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES)));

  // Initial processing check
  if ($a_processmode == 1) {
    if ($a_lu_title != "") $a_books_book_title = $a_lu_title;
    if ($a_lu_author != "") $a_books_author_name = $a_lu_author;
    if ($a_lu_publisher != "") $a_books_publisher = $a_lu_publisher;
    if ($a_lu_copyright != "") $a_books_copyright_year = $a_lu_copyright;
    if ($a_lu_genre != "") $a_books_genre = $a_lu_genre;
    if ($a_lu_cover != "") $a_books_cover_type = $a_lu_cover;
    if ($a_lu_isbn != "") $a_books_isbn = $a_lu_isbn;
    if ($a_lu_pages != "") $a_books_pages = $a_lu_pages;
    if ($a_lu_notes != "") $a_books_notes = $a_lu_notes;
    if ($a_lu_image == "add") {
      $a_books_cover_image = mysql_real_escape_string(file_get_contents("../images/new_cover.jpg"));
      $set_cover = "cover_image = '$a_books_cover_image',";
    } else if ($a_coverFile != "") {
      $a_books_cover_image = mysql_real_escape_string(file_get_contents("thumbs/".$a_coverFile));
      $set_cover = "cover_image = '$a_books_cover_image',";
    } else {
      $a_books_cover_image = NULL;
      $set_cover = "";
    }
  	$w_sqlstr = "UPDATE books SET $set_cover notes = '$a_books_notes', finished = '$a_books_finished', pages = $a_books_pages, isbn = '$a_books_isbn', cover_type = '$a_books_cover_type', genre = '$a_books_genre', purchase_price = $a_books_purchase_price, date_purchased = '$a_books_date_purchased', copyright_year = $a_books_copyright_year, edition_number = $a_books_edition_number, publisher = '$a_books_publisher', author_name = '$a_books_author_name', book_title = '$a_books_book_title' WHERE books.book_id = $a_bookid";
  	wed_db_process("book_collection", $w_sqlstr);
    echo $a_bookid;
  } else if ($a_processmode == 2) {
    if ($a_lu_image == "add") {
      $a_books_cover_image = mysql_real_escape_string(file_get_contents("../images/new_cover.jpg"));
      $db_entry = "cover_image,";
      $db_content = "'".$a_books_cover_image."',";
    } else if ($a_coverFile != "") {
      $a_books_cover_image = mysql_real_escape_string(file_get_contents("thumbs/".$a_coverFile));
      $db_entry = "cover_image,";
      $db_content = "'".$a_books_cover_image."',";
    } else {
      $db_entry = "";
      $db_content = "";
    }
  	$w_sqlstr = "INSERT INTO books ($db_entry notes, finished, pages, isbn,cover_type, genre, purchase_price, date_purchased, copyright_year, edition_number, publisher, author_name, book_title) VALUES ($db_content '$a_books_notes', '$a_books_finished', $a_books_pages, '$a_books_isbn', '$a_books_cover_type', '$a_books_genre', $a_books_purchase_price, '$a_books_date_purchased', $a_books_copyright_year, $a_books_edition_number, '$a_books_publisher', '$a_books_author_name', '$a_books_book_title')";
  	wed_db_process("book_collection", $w_sqlstr);
    $w_sqlstr = "SELECT books.book_id, books.book_title FROM books WHERE (books.book_title like '%$a_books_book_title%' AND books.author_name like '%$a_books_author_name%' AND books.publisher like '%$a_books_publisher%' AND books.isbn like '%$a_books_isbn%') ORDER BY books.book_title";
    wed_read_process ("book_collection", $w_sqlstr, $w_record);
    echo $w_record[0][0];
  } else {
    echo "Error: Incorrect processing mode.";
  }

?>
