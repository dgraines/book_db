<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>Books</title>
    <meta name="description" content="List of books owned by Darrel Raines." />
    <meta name="author" content="Darrel Raines" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles/draines.css" />
    <link rel="stylesheet" type="text/css" media="handheld" href="../styles/dr_handheld.css" />
    <script type="text/javascript" src="../javascript/common.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript">
    /* <![CDATA[ */
      function sortBooks(nsort) {
        document.getElementById("sorder").value = nsort;
        document.getElementById("sort_order").submit();
      }
    /* ]]> */
    </script>
<?php  // Database setup
  require_once('dr_mysql_access.php');
  require_once('wed_php_mysql.inc');
  require_once('isbn.inc');
  $a_startrow = filter_input(INPUT_GET, 'startrow', FILTER_VALIDATE_INT);
  $a_maxrows = filter_input(INPUT_GET, 'maxrows', FILTER_VALIDATE_INT);
  $title = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_title = str_replace("'", "''", stripslashes($title));
  $author = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_author = str_replace("'", "''", stripslashes($author));
  $publisher = filter_input(INPUT_GET, 'publisher', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_publisher = str_replace("'", "''", stripslashes($publisher));
  $isbn = filter_input(INPUT_GET, 'isbn', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $a_isbn = str_replace("'", "''", stripslashes($isbn));
  // Setup the number of books to display
  if (isset($_POST['tlen'])) {
    $tlen = $_POST['tlen'];
    setcookie('tlen', $tlen);
  } else if (!isset($_COOKIE['tlen'])) {
    setcookie("tlen", 10);
    $tlen = 10;
  } else {
    $tlen = $_COOKIE['tlen'];
  }
  // Setup the sort order of the books to display
  $seed = time();
  if (isset($_POST['sorder'])) {
    $sorder = $_POST['sorder'];
    setcookie('sorder', $sorder);
  } else if (!isset($_COOKIE['sorder'])) {
    setcookie('sorder', "books.book_title");
    $sorder = "books.book_title";
  } else {
    $sorder = $_COOKIE['sorder'];
    if (substr($sorder, 0, 4) == 'RAND') {
      $sorder = "RAND($seed) LIMIT $tlen";
    }
  }
?>
  </head>

  <body>
    <div id="header">
      <img src="../images/DR.png" alt="DR Icon" />
      <h1>Books</h1>
    </div>

    <div id="container">
    <div id="main">
    <div class="innertube">
      <h1>Books Owned by Darrel Raines</h1>
      <form method="post" action="books.php" id="sort_order">
        <p>The following table lists all the books owned by Darrel Raines. Click on the bottom numbers to navigate through the list.
        <input type="hidden" name="sorder" id="sorder" value="<?php echo $sorder; ?>" /></p>
      </form>

<?php  // Query database for current list of books
    $w_disprows = $tlen;
    $w_sqlstr = "SELECT books.book_title, books.author_name, books.publisher, books.isbn, books.book_id FROM books WHERE (books.book_title like '%$a_title%' AND books.author_name like '%$a_author%' AND books.publisher like '%$a_publisher%' AND books.isbn like '%$a_isbn%') ORDER BY $sorder";
    wed_read_list_process ("book_collection", $w_sqlstr, $w_record, $w_disprows, $a_startrow, $a_maxrows, $w_rows);
?>

      <table>
        <tr>
<?php
  if ($sorder == 'books.book_title') {
    echo "          <th class='highlight'>Title</th>\n";
  } else {
    echo "          <th><a href=\"javascript:sortBooks('books.book_title');\">Title</a></th>\n";
  }
  if ($sorder == 'books.author_name') {
    echo "          <th class='highlight'>Author</th>\n";
  } else {
    echo "          <th><a href=\"javascript:sortBooks('books.author_name');\">Author</a></th>\n";
  }
  if ($sorder == 'books.publisher') {
    echo "          <th class='highlight'>Publisher</th>\n";
  } else {
    echo "          <th><a href=\"javascript:sortBooks('books.publisher');\">Publisher</a></th>\n";
  }
  if ($sorder == 'books.isbn') {
    echo "          <th class='highlight'>ISBN</th>\n";
  } else {
    echo "          <th><a href=\"javascript:sortBooks('books.isbn');\">ISBN</a></th>\n";
  }
?>
        </tr>
<?php  // Grab a record for each row
    for ($w_i = 0; $w_i < $w_rows; $w_i++) {
    	$f_books_book_title = $w_record[$w_i][0];
    	$f_books_author_name = $w_record[$w_i][1];
    	$f_books_publisher = $w_record[$w_i][2];
    	$f_books_isbn = $w_record[$w_i][3];
    	$f_books_book_id = $w_record[$w_i][4];
?>
        <tr>
          <td><?php wed_write_link("./bookdetails.php", "bookid", $f_books_book_id, $f_books_book_title); ?></td>
          <td><?php wed_write($f_books_author_name); ?></td>
          <td><?php wed_write($f_books_publisher); ?></td>
          <td><?php wed_write($f_books_isbn); ?></td>
        </tr>
<?php
}
?>
        <tr>
          <th colspan="4">
<?php  // Format page index
  if (substr($sorder, 0, 4) == 'RAND') {
    echo "            [ <span class='highlight'>Random</span> ] [<a href='books.php'>>></a>]\n";
  } else {
    echo "            [ <a href=\"javascript:sortBooks('RAND($seed) LIMIT $tlen');\">Random</a> ] ";
    wed_list_move_page(10, $w_disprows, $a_startrow, $a_maxrows, "title=" . $a_title . "&amp;author=" . $a_author . "&amp;publisher=" . $a_publisher . "&amp;isbn=" . $a_isbn);
  }
?>
          </th>
        </tr>
      </table>
      <form method="get" action="books.php" id="search_books">
        <p>Title:
        <input type="text" name="title" maxlength="40" value="<?php echo $title; ?>" />
        Author:
        <input type="text" name="author" maxlength="40" value="<?php echo $author; ?>" />
        Publisher:
        <input type="text" name="publisher" maxlength="40" value="<?php echo $publisher; ?>" />
        ISBN:
        <input type="text" name="isbn" maxlength="17" value="<?php echo $isbn; ?>" />
        <input type="submit" value="Submit Search" /></p>
      </form>
      <form method="post" action="books.php" id="clear_search" class="inline">
        <p class="inline"><input type="submit" value="Show All Books" /></p>
      </form>
      <form id="list_details" method="post" action="bookdetails.php?startrow=<?php echo $a_startrow; ?>&amp;maxrows=<?php echo $a_maxrows; ?>&amp;title=<?php echo $title; ?>&amp;author=<?php echo $author; ?>&amp;publisher=<?php echo $publisher; ?>&amp;isbn=<?php echo $isbn; ?>" class="inline">
        <p class="inline">&nbsp;&nbsp;&nbsp;
        <input type="hidden" name="keep_sorder" value="<?php echo $sorder; ?>" />
        <input type="submit" value="List Details" />
        </p>
      </form>
      <form id="new_entry" method="post" action="bookentry.php" class="inline">
        <p class="inline">&nbsp;&nbsp;&nbsp;
        <input type="submit" value="New Entry" />
        </p>
      </form>
      <form method="post" action="books.php" class="inline">
        <p class="inline">&nbsp;&nbsp;&nbsp;Set table rows:
        <select name="tlen">
          <option value="5" <?php if ($tlen == 5) echo 'selected="selected"'; ?>>5</option>
          <option value="10" <?php if ($tlen == 10) echo 'selected="selected"'; ?>>10</option>
          <option value="15" <?php if ($tlen == 15) echo 'selected="selected"'; ?>>15</option>
          <option value="20" <?php if ($tlen == 20) echo 'selected="selected"'; ?>>20</option>
          <option value="25" <?php if ($tlen == 25) echo 'selected="selected"'; ?>>25</option>
          <option value="30" <?php if ($tlen == 30) echo 'selected="selected"'; ?>>30</option>
          <option value="40" <?php if ($tlen == 40) echo 'selected="selected"'; ?>>40</option>
          <option value="50" <?php if ($tlen == 50) echo 'selected="selected"'; ?>>50</option>
          <option value="100" <?php if ($tlen == 100) echo 'selected="selected"'; ?>>100</option>
        </select>
        <input type="submit" value="Set Rows" />
        </p>
      </form>
      <p>You may enter criteria for any of the fields to search the database. Books with any portion of the corresponding entry matching your criteria will be listed. Click the &quot;Show All Books&quot; button to list all books in the database and clear the previous search criteria. Click the &quot;List Details&quot; button to list the details for all currently selected books. Click on the &quot;New Entry&quot; button to start entering data for a new book. Finally, enter the number of rows you want to display in the table and then click on the &quot;Set Rows&quot; button to show that many table rows.</p>
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
