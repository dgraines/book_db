<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>Book Entry</title>
    <meta name="description" content="Entry form for new books for Darrel Raines." />
    <meta name="author" content="Darrel Raines" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles/draines.css" />
    <link rel="stylesheet" type="text/css" media="handheld" href="../styles/dr_handheld.css" />
    <script type="text/javascript" src="../javascript/common.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAABnkX6tPoiF8BSHjCjpn8iRQXTC8Yje-IQ79RHRtfRs96wzVeSBRLqt7sHvyA4d2ZiDy7GM5Cr4m8FQ"></script>
  </head>

  <body>
    <div id="header">
      <img src="../images/DR.png" alt="DR Icon" />
      <h1>Book Entry</h1>
    </div>

    <div id="container">
    <div id="main">
    <div class="innertube">
      <h1>Enter a Book Into the Database</h1>
<?php
  require_once('dr_mysql_access.php');
  require_once('wed_php_mysql.inc');
  require_once('inc_dr_utils.php');

  // start session if not already started
  session_start();
  if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = 0;
  }
  if (!isset($_SESSION['valid_user'])) {
    $_SESSION['valid_user'] = '';
  }
  // check for valid user
  if ( verify() ) {
?>
	    <form id='isbnForm' method='post' action=''>
  	    <p>Look up ISBN in online database: <input type='text' name='lookup_value' id='lookup_value' />
        <input type='button' value='Compare' onclick="getDBInfo();" />
        &nbsp;<span id="loading" class="highlight"></span></p>
      </form>
      <p>Use this form to enter data about new books into the database.</p>
      <form id="bookEntry" method="post" action="save_book_db.php">
        <table>
          <tr>
            <th id="iTitle">Title</th>
            <td colspan="3"><?php wed_input_str("books_book_title", NULL, 254, WED_NULL_INT); ?></td>
            <th id="iAuthor">Author</th>
            <td colspan="3"><?php wed_input_str("books_author_name", NULL, 254, WED_NULL_INT); ?></td>
          </tr>
          <tr>
            <th id="iPublisher">Publisher</th>
            <td colspan="3"><?php wed_input_str("books_publisher", NULL, 254, WED_NULL_INT); ?></td>
            <th id="iEdition">Edition Number</th>
            <td><?php wed_input_int("books_edition_number", NULL, 4, WED_NULL_INT); ?></td>
            <th id="iCopyright">Copyright Year</th>
            <td><?php wed_input_int("books_copyright_year", NULL, 4, WED_NULL_INT); ?></td>
          </tr>
          <tr>
            <th id="iDate">Date Purchased</th>
            <td colspan="3"><?php wed_input_date("books_date_purchased", WED_CUR_DATE, "Year: yy   Month: mm   Day: dd"); ?></td>
            <th id="iPrice">Purchase Price</th>
            <td colspan="3"><?php wed_input_int("books_purchase_price", NULL, 12, WED_NULL_INT); ?></td>
          </tr>
          <tr>
            <th id="iGenre">Genre</th>
            <td><?php wed_input_combo("book_collection", "SELECT genre.pick FROM genre", "books_genre", 0, 0, "Miscellaneous"); ?></td>
            <th id="iCover">Cover Type</th>
            <td><?php wed_input_combo("book_collection", "SELECT cover.pick FROM cover", "books_cover_type", 0, 0, "Hard Cover"); ?></td>
            <th id="iISBN">ISBN</th>
            <td><?php wed_input_str("books_isbn", NULL, 254, WED_NULL_INT); ?></td>
            <th id="iPages">Pages</th>
            <td><?php wed_input_int("books_pages", NULL, 6, WED_NULL_INT); ?></td>
          </tr>
          <tr>
            <th id="iFinished">Finished?</th>
            <td><?php wed_input_radio("book_collection", "SELECT yes_no.pick FROM yes_no", "books_finished", 0, 0, 'No'); ?></td>
            <th id="iNotes">Notes</th>
            <td colspan="3"><?php wed_input_text("books_notes", NULL, 6, 40); ?></td>
            <th id="iImage">Image&nbsp;<input type="checkbox" name="lu_image" id="lu_image" value="add" /><br /><br /><a href="javascript:toggleImages();">-Library-</a><input type="hidden" name="coverFile" id="coverFile" value="" /></th>
            <td id="newImage">--</td>
          </tr>
          <tr id="selectImage" style="display:none">
            <th>Select Cover:<br /><br /><a href="javascript:toggleForm();">-Upload New-</a></th>
            <td id="allImages" colspan="7">Cover images appear here. If the images do not appear in this space, then your web browser may not support/may have disabled Javascript.</td>
          </tr>
          <tr>
            <td colspan="8">
              <span id="errorMsg" class="highlight"></span>
              <input type="button" value="Submit New Entry" onclick="checkInputs();" />
              <input type="reset" value="Reset to Defaults" />
              <input type="hidden" name="processmode" value="2" />
            </td>
          </tr>
        </table>
      </form>
      <div id="popup" style="display:none">
        <form id="upload_thumb" action="image_to_thumb.php" enctype="multipart/form-data" method="post" target="upload_target" onsubmit="startUpload();">
        <p><span class="bold">Local Image file:</span>&nbsp;&nbsp;&nbsp;<input name="upfile" type="file" /><br />
        Image files must be in JPEG, GIF, or PNG format.</p>
        <p><span class="bold">URL (Remote file):</span>&nbsp;&nbsp;&nbsp;<input name="imageURL" type="text" size="40" maxlength="256" /></p>
        <p><span class="bold">Filename (Optional):</span>&nbsp;&nbsp;&nbsp;<input name="newname" type="text" size="40" maxlength="64" /></p>
        <p><input type="button" value="Close - No Upload" onclick="javascript:toggleForm();" />&nbsp;&nbsp;&nbsp;<input type="submit" value="Upload Image" /></p>
        </form>
        <span id="uploadResults" style="display:none"></span>
      </div>
      <iframe id="upload_target" name="upload_target" src="" style="width:0;height:0;border:0px solid #FFFFFF;display:none"></iframe>
      <p>Other book options:</p>
      <form method="post" action="books.php" id="clear_search" class="inline">
        <p class="inline"><input type="submit" value="Show All Books" /></p>
      </form>
      <p>Click the &quot;Show All Books&quot; button to list all books in the database and clear the previous search criteria.</p>
<?php
  }
?>
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

<script type="text/javascript">
/* <![CDATA[ */
  window.onresize=positionPopup;
  $("#lookup_value").focus();
  document.onkeypress = checkKeys;
  
  function checkKeys(evt) {
    var evt = evt ? evt : ((event) ? event : null);
    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.id == "lookup_value")) {
      getDBInfo();
      return false;
    }
  }

  function getDBInfo() {
    $("#loading").html("<img src='../images/ajax_loader_2.gif' alt='Loading' />");
    $.getJSON("check_isbn.php", {isbn: $("#lookup_value").val(), response: "json"}, check_isbn);
  }

  function check_isbn(json) {
    if (json.isbn.slice(0,5) == "Error") {
      $("#loading").html("<br /><br />This value: " + $("#lookup_value").val() + " is not a valid ISBN!");
    } else {
      if (json.number != "0") {
        $("#loading").html("<br /><br />This ISBN is already in the database. Click here to edit: <a href=\"./bookmod.php?bookid=" + json.bookid + "\" title=\"Edit this book\">" + json.isbn + "</a>");
      } else {
        $("#books_book_title").focus();
        $("#newImage").html("<img src='../images/ajax_loader_1.gif' alt='New Cover' />");
        $.getJSON("get_isbndb_info.php", {isbn: $("#lookup_value").val(), response: "json"}, get_isbn_info);
        $.ajax({url: "get_book_cover.php", data: {isbn: document.getElementById("lookup_value").value}, success: get_book_cover});
      }
    }
    $("#lookup_value").val("");
  }

  function get_isbn_info(json){
    if (json.isbn.slice(0,5) == "Error") {
      $("#loading").html("<br /><br />" + json.isbn);
    } else {
      $("#books_book_title").val(json.title);
      $("#books_author_name").val(json.author);
      $("#books_publisher").val(json.publisher);
      $("#books_copyright_year").val(json.copyright);
      $("#books_genre").val(json.genre);
      $("#books_cover_type").val(json.cover);
      $("#books_isbn").val(json.isbn);
      $("#books_pages").val(json.pages);
      $("#books_notes").val(json.notes);
      $("#loading").html("");
    }
  }

  function get_book_cover(bookHTML) {
    $("#newImage").html(bookHTML);
    if (bookHTML.match("new_cover") == null) {
      $("#lu_image").prop('checked', false);
    }
  }

  function checkInputs() {
    var inputsBad = 0;
    inputsBad += checkNull("books_book_title", "iTitle");
    inputsBad += checkNull("books_author_name", "iAuthor");
    inputsBad += checkNull("books_publisher", "iPublisher");
    inputsBad += checkNull("books_edition_number", "iEdition");
    inputsBad += checkNull("books_copyright_year", "iCopyright");
    inputsBad += checkNull("books_isbn", "iISBN");
    inputsBad += checkNull("books_purchase_price", "iPrice");
    if (inputsBad == 0) {
      $("#errorMsg").html("Updating database ... <img src='../images/ajax_loader_2.gif' alt='Updating' />   ");
      form_data_serial = $("#bookEntry").serialize();
      $.ajax({
        url: "save_book_db.php?",
        type: "POST",
        data: form_data_serial,
        success: function(response) {
          if (response.match(/Error/i)) {
            $("#errorMsg").html(response + "   ");
          } else {
            window.location = "bookdetails.php?bookid=" + response;
          }
        }
      })
    } else {
      $("#errorMsg").html("Error: Enter highlighted items.  ");
    }
  }

  function checkNull(input, title) {
    if (document.getElementById(input).value == "") {
      document.getElementById(title).className = "highlight";
      return 1;
    } else {
      document.getElementById(title).className = "";
      return 0;
    }
  }

  function startUpload() {
    $('#upload_thumb').hide();
    $('#uploadResults').html("<p>Loading images ... <img src='../images/ajax_loader_1.gif' alt='Loading' /></p>");
    $('#uploadResults').show();
    positionPopup();
  }

  function finishUpload(result) {
    $("#uploadResults").html("<p>" + result + "<br /><br /><input type=\"button\" value=\"Close Window\" onclick=\"javascript:toggleForm();\" /></p>");
    positionPopup();
  }

  function getImages() {
    $("#allImages").html("Loading images ... <img src='../images/ajax_loader_1.gif' alt='Loading' />");
    $("#allImages").load('get_images.php');
  }

  function toggleImages() {
    if ($("#selectImage").is(":hidden")) {
      $("#selectImage").show();
      $('#coverFile').html("");
      $('#newImage').html("--");
      getImages();
    } else {
      $("#selectImage").hide();
      $("#popup").hide();
    }
  }

  function positionPopup() {
    offset = $("#allImages").offset();
    $("#popup").offset(offset);
  }

  function toggleForm() {
    if ($("#popup").is(":hidden")) {
      $("#uploadResults").hide();
      $("#upload_thumb").show();
      $("#upload_thumb").reset();
      positionPopup();
      $("#popup").show();
    } else {
      $("#popup").hide();
      getImages();
    }
  }

  function selectOne(imageName) {
    $('#coverFile').html = imageName;
    $('#popup').hide();
    $('#newImage').html("<img src='thumbs/" + imageName +"' alt='" + imageName + "' />");
    $('#selectImage').hide();
  }
/* ]]> */
</script>
  </body>
</html>
