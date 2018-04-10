<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>Book Details</title>
    <meta name="description" content="Details of books owned by Darrel Raines." />
    <meta name="author" content="Darrel Raines" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles/draines.css" />
    <link rel="stylesheet" type="text/css" media="handheld" href="../styles/dr_handheld.css" />
    <script type="text/javascript" src="../javascript/common.js"></script>
<?php  // Database setup
  require_once('dr_mysql_access.php');
  require_once('wed_php_mysql.inc');
  $a_startrow = filter_input(INPUT_GET, 'startrow', FILTER_VALIDATE_INT);
  $a_maxrows = filter_input(INPUT_GET, 'maxrows', FILTER_VALIDATE_INT);
  $a_title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_title = str_replace("'", "''", stripslashes($a_title));
  $a_author = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_author = str_replace("'", "''", stripslashes($a_author));
  $a_publisher = filter_input(INPUT_GET, 'publisher', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_publisher = str_replace("'", "''", stripslashes($a_publisher));
  $a_isbn = filter_input(INPUT_GET, 'isbn', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_isbn = str_replace("'", "''", stripslashes($a_isbn));
  $a_bookid = filter_input(INPUT_GET, 'bookid', FILTER_VALIDATE_INT);
  // Setup the number of books to display
  if (!isset($_COOKIE['tlen'])) {
    setcookie("tlen", 10);
    $tlen = 10;
  } else {
    $tlen = $_COOKIE['tlen'];
  }
  // Setup the sort order of the books to display
  if (isset($_POST['keep_sorder'])) {
    $sorder = $_POST['keep_sorder'];
    setcookie('sorder', $sorder);
  } else if (!isset($_COOKIE['sorder'])) {
    setcookie('sorder', "books.book_title");
    $sorder = "books.book_title";
  } else {
    $sorder = $_COOKIE['sorder'];
  }
?>
  </head>

  <body>
    <div id="header">
      <img src="../images/DR.png" alt="DR Icon" />
      <h1>Book Details</h1>
    </div>

    <div id="container">
    <div id="main">
    <div class="innertube">
      <h1>Details of a Book in the Database</h1>
      <p>The following entries match your selection from the database.</p>
      <table>
        <tr>
          <th colspan="3">Details for Selected Books</th>
        </tr>

<?php  // Get books one at a time from the database
    $w_disprows = $tlen;
    if ($a_bookid == "") {
      $w_sqlstr = "SELECT books.book_id, books.author_name, books.book_title, books.publisher, books.edition_number, books.copyright_year, books.isbn, books.cover_type, books.pages, books.date_purchased, books.purchase_price, books.genre, books.finished, books.notes FROM books WHERE (books.book_title like '%$a_title%' AND books.author_name like '%$a_author%' AND books.publisher like '%$a_publisher%' AND books.isbn like '%$a_isbn%') ORDER BY $sorder";
    } else {
      $w_sqlstr = "SELECT books.book_id, books.author_name, books.book_title, books.publisher, books.edition_number, books.copyright_year, books.isbn, books.cover_type, books.pages, books.date_purchased, books.purchase_price, books.genre, books.finished, books.notes FROM books WHERE (books.book_id like '$a_bookid')";
    }
    wed_read_list_process ("book_collection", $w_sqlstr, $w_record, $w_disprows, $a_startrow, $a_maxrows, $w_rows);

    for ($w_i = 0; $w_i < $w_rows; $w_i++) {
    	$f_books_book_id = $w_record[$w_i][0];
    	$f_books_author_name = $w_record[$w_i][1];
    	$f_books_book_title = $w_record[$w_i][2];
    	$f_books_publisher = $w_record[$w_i][3];
    	$f_books_edition_number = $w_record[$w_i][4];
    	$f_books_copyright_year = $w_record[$w_i][5];
    	$f_books_isbn = $w_record[$w_i][6];
    	$f_books_cover_type = $w_record[$w_i][7];
    	$f_books_pages = $w_record[$w_i][8];
    	$f_books_date_purchased = $w_record[$w_i][9];
    	$f_books_purchase_price = $w_record[$w_i][10];
    	$f_books_genre = $w_record[$w_i][11];
    	$f_books_finished = $w_record[$w_i][12];
    	$f_books_notes = $w_record[$w_i][13];
?>
        <tr class="min">
          <td colspan="2" class="w95"><?php wed_write_link("./bookmod.php", "bookid", $f_books_book_id, $f_books_book_title); ?> by <?php wed_write($f_books_author_name); ?>.</td>
          <td class="min"><span class="small"><sup>ID: <?php wed_write($f_books_book_id); ?></sup></span></td>
        </tr>
        <tr class="min">
          <td class="min">&nbsp;</td>
          <td class="w90">I own edition #<?php wed_write($f_books_edition_number); ?>, published by <?php wed_write($f_books_publisher); ?>, copyright <?php wed_write($f_books_copyright_year); ?>.</td>
          <td class="min" rowspan="5">
            <img src="get_mysql_image.php?db=books&amp;id=<?php echo $f_books_book_id ?>" alt="Cover Image" />
          </td>
        </tr>
        <tr class="min">
          <td class="min">&nbsp;</td>
          <td class="w90">This is a <?php wed_write($f_books_cover_type); ?> containing <?php wed_write($f_books_pages); ?> pages with an ISBN of <?php wed_write($f_books_isbn); ?>.</td>
        </tr>
        <tr class="min">
          <td class="min">&nbsp;</td>
          <td class="w90">The book cost $<?php wed_write($f_books_purchase_price); ?> when purchased on <?php wed_write($f_books_date_purchased); ?>.</td>
        </tr>
        <tr class="min">
          <td class="min">&nbsp;</td>
          <td class="w90">The genre is <?php wed_write($f_books_genre); ?> and the &quot;finished reading&quot; status is: <?php wed_write($f_books_finished); ?>.</td>
        </tr>
        <tr class="brow">
          <td class="min">&nbsp;</td>
          <td class="w90">Notes on the book: <?php wed_write($f_books_notes); ?></td>
        </tr>
<?php  // End Loop
    }
?>      <tr>
          <th colspan="3"><?php wed_list_move_page(10, $w_disprows, $a_startrow, $a_maxrows, "title=" . $a_title . "&amp;author=" . $a_author . "&amp;publisher=" . $a_publisher . "&amp;isbn=" . $a_isbn); ?></th>
        </tr>
      </table>
      <p>Other book options:</p>
      <form method="post" action="books.php" id="clear_search" class="inline">
        <p class="inline"><input type="submit" value="Show All Books" /></p>
      </form>
      <form id="new_entry" method="post" action="bookentry.php" class="inline">
        <p class="inline">&nbsp;&nbsp;&nbsp;
        <input type="submit" value="New Entry" />
        </p>
      </form>
      <p>Click the &quot;Show All Books&quot; button to list all books in the database and clear the previous search criteria. Click on the &quot;New Entry&quot; button to start entering data for a new book.</p>
    </div>
    </div>
    </div>

    <div id="sidebar">
    <div class="innertube">
<?php require("nav.php"); ?>
    </div>
    </div>

    <div id="footer">
<?php require("footer.html"); ?>
    </div>

  </body>
</html>
