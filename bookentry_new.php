<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
    <title>Book Entry/Modification</title>
    <meta name="description" content="Entry/Modification form for books for Darrel Raines." />
    <meta name="author" content="Darrel Raines" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles/draines.css" />
    <link rel="stylesheet" type="text/css" media="handheld" href="../styles/dr_handheld.css" />
    <script type="text/javascript" src="../javascript/common.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAABnkX6tPoiF8BSHjCjpn8iRQXTC8Yje-IQ79RHRtfRs96wzVeSBRLqt7sHvyA4d2ZiDy7GM5Cr4m8FQ"></script>
    <script type="text/javascript">
      /* <![CDATA[ */
      google.load('search', '1');
      /* ]]> */
    </script>
    <style type="text/css">
      .dim{opacity:0.7;}
      .dim:hover{opacity:1.0;}
    </style>
    <!--[if lte IE 7]>
      <style type="text/css">
        .dim{filter:alpha(opacity=70);}
        .dim:hover{filter:alpha(opacity=100);}
        .imgLibrary:img{float:left;}
      </style>
    <![endif]-->
  </head>
<?php
  require_once('dr_mysql_access.php');
  require_once('wed_php_mysql.inc');
  require_once('inc_dr_utils.php');
?>

  <body>
    <div id="header">
      <img src="../images/DR.png" alt="DR Icon" />
      <h1>Book Entry</h1>
    </div>

    <div id="container">
    <div id="main">
    <div class="innertube">
      <h1>Enter/Modify a Book Into the Database</h1>
<?php
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
    if (isset($_GET['bookid'])) {
      $a_bookid = filter_input(INPUT_GET, 'bookid', FILTER_VALIDATE_INT);
    } else {
      $a_bookid = 0;
    }
    if ($a_bookid != 0) {
      $w_sqlstr = "SELECT books.book_id, books.author_name, books.book_title, books.publisher, books.edition_number, books.copyright_year, books.isbn, books.cover_type, books.pages, books.date_purchased, books.purchase_price, books.genre, books.finished, books.notes FROM books WHERE (books.book_id = " . $a_bookid . ")";
      wed_read_process ("book_collection", $w_sqlstr, $w_record);
      $f_books_book_id = $w_record[0][0];
      $f_books_author_name = $w_record[0][1];
      $f_books_book_title = $w_record[0][2];
      $f_books_publisher = $w_record[0][3];
      $f_books_edition_number = $w_record[0][4];
      $f_books_copyright_year = $w_record[0][5];
      $f_books_isbn = $w_record[0][6];
      $f_books_cover_type = $w_record[0][7];
      $f_books_pages = $w_record[0][8];
      $f_books_date_purchased = $w_record[0][9];
      $f_books_purchase_price = $w_record[0][10];
      $f_books_genre = $w_record[0][11];
      $f_books_finished = $w_record[0][12];
      $f_books_notes = $w_record[0][13];
?>
      <form id='compareISBN' method="post" action="">
        <p>Click this button to compare database entry with ISBNdb.com:
        <input type='hidden' name='lookup_value' id='lookup_value' value='<?php echo $f_books_isbn ?>' />
        <input type='button' value='Compare' onclick="compareISBN();" />
        &nbsp;<span id="loading" class="highlight"></span>
        </p>
      </form>
      <p>This form contains data about the selected book. You may make changes to the entry by typing in corrections and then submitting the changes.</p>
<?php
    } else {
      $f_books_book_id = NULL;
      $f_books_author_name = NULL;
      $f_books_book_title = NULL;
      $f_books_publisher = NULL;
      $f_books_edition_number = NULL;
      $f_books_copyright_year = NULL;
      $f_books_isbn = NULL;
      $f_books_cover_type = "Hard Cover";
      $f_books_pages = NULL;
      $f_books_date_purchased = WED_CUR_DATE;
      $f_books_purchase_price = NULL;
      $f_books_genre = "Miscellaneous";
      $f_books_finished = 'No';
      $f_books_notes = NULL;
?>
      <form id='isbnForm' method='post' action=''>
        <p>Look up ISBN in online database: <input type='text' name='lookup_value' id='lookup_value' />
        <input type='button' value='Compare' onclick="getDBInfo();" />
        &nbsp;<span id="loading" class="highlight"></span></p>
      </form>
      <p>Use this form to enter data about new books into the database.</p>
<?php
    }
?>
      <form id="bookEntry" method="post" action="save_book_db.php<?php if ($a_bookid != 0) {echo ("?bookid=$a_bookid");}; ?>">
        <table>
          <tr>
            <th id="iTitle">Title</th>
            <td colspan="3"><?php wed_input_str("books_book_title", $f_books_book_title, 254, WED_NULL_INT); ?></td>
            <th id="iAuthor">Author</th>
            <td colspan="3"><?php wed_input_str("books_author_name", $f_books_author_name, 254, WED_NULL_INT); ?></td>
          </tr>
          <tr id="exRow1" style="display:none">
            <th>db&nbsp;<input type="checkbox" name="lu_title" id="lu_title" value="--" /></th>
            <td id="newTitle" colspan='3'>--</td>
            <th>db&nbsp;<input type="checkbox" name="lu_author" id="lu_author" value="--" /></th>
            <td id="newAuthor" colspan='3'>--</td>
          </tr>
          <tr>
            <th id="iPublisher">Publisher</th>
            <td colspan="3"><?php wed_input_str("books_publisher", $f_books_publisher, 254, WED_NULL_INT); ?></td>
            <th id="iEdition">Edition Number</th>
            <td><?php wed_input_int("books_edition_number", $f_books_edition_number, 4, WED_NULL_INT); ?></td>
            <th id="iCopyright">Copyright Year</th>
            <td><?php wed_input_int("books_copyright_year", $f_books_copyright_year, 4, WED_NULL_INT); ?></td>
          </tr>
          <tr id="exRow2" style="display:none">
            <th>db&nbsp;<input type="checkbox" name="lu_publisher" id="lu_publisher" value="--" /></th>
            <td id="newPublisher" colspan='3'>--</td>
            <th>db</th>
            <td>&nbsp;</td>
            <th>db&nbsp;<input type="checkbox" name="lu_copyright" id="lu_copyright" value="--" /></th>
            <td id="newCopyright">--</td>
          </tr>
          <tr>
            <th id="iDate">Date Purchased</th>
            <td colspan="3"><?php wed_input_date("books_date_purchased", $f_books_date_purchased, "Year: yy   Month: mm   Day: dd"); ?></td>
            <th id="iPrice">Purchase Price</th>
            <td colspan="3"><?php wed_input_int("books_purchase_price", $f_books_purchase_price, 12, WED_NULL_INT); ?></td>
          </tr>
          <tr>
            <th id="iGenre">Genre</th>
            <td><?php wed_input_combo("book_collection", "SELECT genre.pick FROM genre", "books_genre", 0, 0, $f_books_genre); ?></td>
            <th id="iCover">Cover Type</th>
            <td><?php wed_input_combo("book_collection", "SELECT cover.pick FROM cover", "books_cover_type", 0, 0, $f_books_cover_type); ?></td>
            <th id="iISBN">ISBN</th>
            <td><?php wed_input_str("books_isbn", $f_books_isbn, 254, WED_NULL_INT); ?></td>
            <th id="iPages">Pages</th>
            <td><?php wed_input_int("books_pages", $f_books_pages, 6, WED_NULL_INT); ?></td>
          </tr>
          <tr id="exRow3" style="display:none">
            <th>db&nbsp;<input type="checkbox" name="lu_genre" id="lu_genre" value="--" /></th>
            <td id="newGenre">--</td>
            <th>db&nbsp;<input type="checkbox" name="lu_cover" id="lu_cover" value="--" /></th>
            <td id="newCover">--</td>
            <th>db&nbsp;<input type="checkbox" name="lu_isbn" id="lu_isbn" value="--" /></th>
            <td id="newISBN">--</td>
            <th>db&nbsp;<input type="checkbox" name="lu_pages" id="lu_pages" value="--" /></th>
            <td id="newPages">--</td>
          </tr>
          <tr>
            <th id="iFinished">Finished?</th>
           <td><?php wed_input_radio("book_collection", "SELECT yes_no.pick FROM yes_no", "books_finished", 0, 0, $f_books_finished); ?></td>
            <th id="iNotes">Notes</th>
            <td colspan="3"><?php wed_input_text("books_notes", $f_books_notes, 6, 40); ?></td>
            <th id="iImage">Image&nbsp;<input type="checkbox" name="lu_image" id="lu_image" value="add" /><span class="small"><br /><br /><a href="javascript:toggleImages();">-Library-</a><br /><br /><a href="javascript:scrapeGoogle();">-Scrape-</a></span><input type="hidden" name="coverFile" id="coverFile" value="" /></th>
            <td id="coverImage"><?php if ($a_bookid != 0) {echo '<img id="currentCover" src="get_mysql_image.php?db=books&amp;id='.$a_bookid.'" alt="Cover Image" />';} else {echo '--';} ?></td>
          </tr>
          <tr id="exRow4" style="display:none">
            <th>db</th>
            <td>&nbsp;</td>
            <th>db&nbsp;<input type="checkbox" name="lu_notes" id="lu_notes" value="--" /></th>
            <td id="newNotes" colspan='3'>--</td>
            <th>db&nbsp;<input type="checkbox" name="lu_image" id="lu_image" value="add" /></th>
            <td id='newImage' >--</td>
          </tr>
          <tr id="selectImage" style="display:none">
            <th id="sTitle">Select Cover:<span class="small"><br /><br /><a href="javascript:toggleForm();">-Upload-</a></span></th>
            <td id="allImages" class="imgLibrary" colspan="6">Cover images appear here. If the images do not appear in this space, then your web browser may not support/may have disabled Javascript.</td>
            <td><div class="trashWrap"><div class="trash"><div id="trashInner"></div></div></div></td>
          </tr>
          <tr id="selectScrape" style="display:none">
            <th>Grab Image:</th>
            <td id="allScrapes" class="imgLibrary" colspan="6">Google images appear here. If the images do not appear in this space, then your web browser may not support/may have disabled Javascript.</td>
            <td><div class="trashWrap"><div class="trash"><div id="vaultInner"></div></div></div></td>
          </tr>
          <tr>
            <td colspan="8"><span id="errorMsg" class="highlight"></span><input type="button" value="Submit Entry/Changes" onclick="checkInputs();" />&nbsp;&nbsp;&nbsp;<input type="reset" value="Reset to Defaults/Previous" /><input type="hidden" name="processmode" value="<?php if ($a_bookid == 0) {echo "2";} else {echo "1";}; ?>" /></td>
          </tr>
        </table>
        </form>
      <div id="popup" style="display:none">
        <form id="upload_thumb" action="image_to_thumb.php" enctype="multipart/form-data" method="post" target="upload_target" onsubmit="startUpload();">
        <p><span class="bold">Local Image file:</span>&nbsp;&nbsp;&nbsp;<input name="upfile" type="file" /><br />
        Image files must be in JPEG, GIF, or PNG format.</p>
        <p><span class="bold">URL (Remote file):</span>&nbsp;&nbsp;&nbsp;<input id="imageURL" name="imageURL" type="text" size="40" maxlength="256" /></p>
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
      <form id="new_entry" method="post" action="bookentry.php" class="inline">
        <p class="inline">&nbsp;&nbsp;&nbsp;
        <input type="submit" value="New Entry" />
        </p>
      </form>
      <p>Click the &quot;Show All Books&quot; button to list all books in the database and clear the previous search criteria. Click on the &quot;New Entry&quot; button to start entering data for a new book.</p>
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
  $("#<?php if ($a_bookid == 0) {echo "lookup_value";} else {echo "books_book_title";}; ?>").focus();
  var imageSearch;
  var first = true;
  var img_count = 0;
  document.onkeypress = checkKeys;
  
  $('#trashInner').droppable({
    accept: '.library',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      growImage(ui.draggable.attr('id'),ui.draggable.width(),ui.draggable.height(),0,0,1,function(imageName) {
        $.ajax({
          url: 'delete_file.php',
          type: 'POST',
          data: {fileName: imageName},
          error: function(jqxhr, errmsg) {$("#errorMsg").html("Image deletion error: " + errmsg + "&nbsp&nbsp&nbsp");},
          success: function() {$("#errorMsg").html("Image deleted from library.&nbsp&nbsp&nbsp");}
        });
        $("#"+imageName).remove();
      });
    }
  });
  $('#currentCover').droppable({
    accept: '.library',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      selectOne(ui.draggable.attr('id'));
    }
  });
  $('#vaultInner').droppable({
    accept: '.scrape',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      $("#selectImage").show();
      document.getElementById('upload_thumb').reset();
      $('#imageURL').val(ui.draggable.attr('alt'));
      $('#popup').show();
      startUpload();
      $('#upload_thumb').submit();
    }
  });

  //Function to check that the enter key was pressed (or equivalent for scanners) to activate form submission
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
        $("#coverImage").html("<img src='../images/ajax_loader_1.gif' alt='New Cover' />");
        $.getJSON("get_isbndb_info.php", {isbn: $("#lookup_value").val(), response: "json"}, get_isbn_info);
        $.ajax({url: "get_book_cover.php", data: {isbn: document.getElementById("lookup_value").value}, success: get_book_cover});
      }
    }
    $("#lookup_value").val("");
  }

  function get_isbn_info(json){
    if (json.notes.slice(0,5) == "Error") {
      $("#loading").html("<br /><br />" + json.notes);
      $("#books_isbn").val(json.isbn);
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
    $("#coverImage").html(bookHTML);
    if (bookHTML.match("new_cover") == null) {
      $("#lu_image").prop('checked', false);
    }
  }

  function compareISBN() {
    $("#loading").html("<img src='../images/ajax_loader_2.gif' alt='Loading' />");
    $("#newImage").html("<img src='../images/ajax_loader_1.gif' alt='New Cover' />");
    $("#exRow1").css({display: "table-row"});
    $("#exRow2").css({display: "table-row"});
    $("#exRow3").css({display: "table-row"});
    $("#exRow4").css({display: "table-row"});
    $.getJSON("get_isbndb_info.php", {isbn: "<?php $strip_isbn = preg_replace("/-/", "", $f_books_isbn); echo $strip_isbn; ?>", response: "json"}, compare_isbn_info);
    $.ajax({url: "get_book_cover.php", data: {isbn: "<?php echo $strip_isbn; ?>"}, success: compare_book_cover});
  }

  function compare_isbn_info(json) {
    if (json.notes.slice(0,5) == "Error") {
      $("#loading").html("<br /><br />" + json.notes);
      $("#lu_isbn").val(json.isbn);
      $("#newISBN").html(json.isbn);
    } else {
      $("#lu_title").val(json.title);
      $("#newTitle").html(json.title);
      $("#lu_author").val(json.author);
      $("#newAuthor").html(json.author);
      $("#lu_publisher").val(json.publisher);
      $("#newPublisher").html(json.publisher);
      $("#lu_copyright").val(json.copyright);
      $("#newCopyright").html(json.copyright);
      $("#lu_genre").val(json.genre);
      $("#newGenre").html(json.genre);
      $("#lu_cover").val(json.cover);
      $("#newCover").html(json.cover);
      $("#lu_isbn").val(json.isbn);
      $("#newISBN").html(json.isbn);
      $("#lu_pages").val(json.pages);
      $("#newPages").html(json.pages);
      $("#lu_notes").val(json.notes);
      $("#newNotes").html(json.notes);
      $("#loading").html("");
    }
  }

  function compare_book_cover(bookHTML) {
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
        url: "save_book_db.php<?php if ($a_bookid != 0) {echo ("?bookid=$a_bookid");}; ?>",
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
    if ($("#"+input).value == "") {
      $("#"+title).className = "highlight";
      return 1;
    } else {
      $("#"+title).className = "";
      return 0;
    }
  }

  function scrapeGoogle() {
    if ($("#selectScrape").is(":hidden")) {
      $("#selectScrape").show();
      $("#allScrapes").html("Loading scrapes ... <img src='../images/ajax_loader_1.gif' alt='Loading' />");
      first = true;
      img_count = 0;
      imageSearch = new google.search.ImageSearch();
      imageSearch.setResultSetSize(google.search.Search.LARGE_RESULTSET);
      imageSearch.setSearchCompleteCallback(this, searchComplete, null);
      imageSearch.execute($('#books_book_title').val() + ' ' + $('#books_author_name').val() + ' book cover');
    } else {
      $("#selectScrape").hide();
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
    var now = new Date();
    $.ajax({url: 'get_images.php', data: {time: now.getTime()}, success: function(response) {
      $('#allImages').html("");
      var names = $.parseJSON(response);
      for ( var i=0; i < names.length; i++ ) {
        var d=document.createElement('img');
        d.id = names[i];
        d.className = 'CustomDraggable dim library';
        d.src = "thumbs/" + names[i];
        d.alt = "Library Image #" + i;
        d.ondblclick = function() {
          selectOne(this.id);
        }
        document.getElementById("allImages").appendChild(d);
        $("#" + names[i].replace(/\./g,"\\.")).draggable({revert: 'invalid', helper: 'clone'});
      }
    } });
  }

  function toggleImages() {
    if ($("#selectImage").is(":hidden")) {
      $("#selectImage").show();
      $('#coverFile').html("");
      $('#currentCover').html("--");
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
      document.getElementById("upload_thumb").reset();
      positionPopup();
      $("#popup").show();
    } else {
      $("#popup").hide();
      getImages();
    }
  }

  function selectOne(imageName) {
    if ($('#coverFile').val() == "") {
      $('#coverFile').val(imageName);
      $('#popup').hide();
      $('#currentCover').css({opacity: 0.4});
      iHold = $('#sTitle').html();
      $('#sTitle').html("New Image<br /><br /><a href='javascript:selectOne(\"\");'>Re-select</a>");
      $('#allImages').html("<img src='thumbs/" + imageName +"' alt='" + imageName + "' />");
    } else {
      $('#coverFile').val("");
      $('#popup').hide();
      $('#currentCover').css({opacity: 1.0});
      $('#sTitle').html(iHold);
      getImages();
    }
  }

  function searchComplete() {
    if (imageSearch.results && imageSearch.results.length > 0) {
      var contentDiv = document.getElementById('allScrapes');
      if (first) contentDiv.innerHTML = '';
      var results = imageSearch.results;
      for (var i = 0; i < results.length; i++) {
        var result = results[i];
        var newImg = document.createElement('img');
        newImg.src = result.tbUrl;
	newImg.id = "search_result_" + img_count;
	img_count = img_count + 1;
        newImg.alt = result.url;
        newImg.className = 'CustomDraggable dim scrape';
        setImgDim(newImg, result.tbWidth, result.tbHeight);
        contentDiv.appendChild(newImg);
        $("#" + newImg.id).draggable({revert: 'invalid', helper: 'clone'});
      }
      if (imageSearch.cursor.currentPageIndex < imageSearch.cursor.pages.length) {
        imageSearch.gotoPage(imageSearch.cursor.currentPageIndex + 1);
      }
      first = false;
    }
  }

  function setImgDim(targetI, width, height) {
    wF = 110/width;
    hF = 110/height;
    if ((wF > 1.0) && (hF > 1.0)) {
      targetI.width = width;
      targetI.height = height;
    } else if (wF > hF) {
      targetI.width = hF * width;
      targetI.height = hF * height;
    } else {
      targetI.width = wF * width;
      targetI.height = wF * height;
    }
  }
/* ]]> */
</script>
  </body>
</html>
