<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>Book Modify</title>
    <meta name="description" content="Modify a book entry for Darrel Raines." />
    <meta name="author" content="Darrel Raines" />
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../styles/draines.css" />
    <link rel="stylesheet" type="text/css" media="handheld" href="../styles/dr_handheld.css" />
    <script type="text/javascript" src="../javascript/common.js"></script>
    <script type="text/javascript" src="../javascript/prototype.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAABnkX6tPoiF8BSHjCjpn8iRQXTC8Yje-IQ79RHRtfRs96wzVeSBRLqt7sHvyA4d2ZiDy7GM5Cr4m8FQ"></script>
    <script type="text/javascript">
      /* <![CDATA[ */
      google.load('search', '1');
      var $j = jQuery.noConflict();
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

  <body>
    <div id="header">
      <img src="../images/DR.png" alt="DR Icon" />
      <h1>Book Modify</h1>
    </div>

    <div id="container">
    <div id="main">
    <div class="innertube">
      <h1>Details of a Book in the Database</h1>
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
    $a_bookid = filter_input(INPUT_GET, 'bookid', FILTER_VALIDATE_INT);
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
      <p>This form contains data about the selected book. You may make changes to the entry by typing in corrections and then submitting the changes.</p>
      <form method="post" action="">
        <p>Click this button to compare database entry with ISBNdb.com:
        <input type='hidden' name='lookup_value' value='<?php echo $f_books_isbn ?>' />
        <input type='button' value='Compare' onclick="getDBInfo();" />
        &nbsp;<span id="loading"></span>
        </p>
      </form>
      <form id="modForm" method="post" action="save_book_db.php?bookid=<?php echo $a_bookid; ?>">
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
            <td><?php wed_input_combo("book_collection", "SELECT genre.pick FROM genre", "books_genre", 0, 0, $f_books_genre, NULL, NULL); ?></td>
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
            <td><?php wed_input_radio("book_collection", "SELECT yes_no.pick FROM yes_no", "books_finished", 0, 0, $f_books_finished, NULL, NULL); ?></td>
            <th id="iNotes">Notes</th>
            <td colspan="3"><?php wed_input_text("books_notes", $f_books_notes, 6, 40); ?></td>
            <th>Image<span class="small"><br /><br /><a href="javascript:toggleImages();">-Library-</a><br /><br /><a href="javascript:scrapeGoogle();">-Scrape-</a></span><input type="hidden" name="coverFile" id="coverFile" value="" /></th>
            <td><img id="currentCover" src="get_mysql_image.php?db=books&amp;id=<?php echo $a_bookid ?>" alt="Cover Image" /></td>
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
            <td colspan="8"><span id="errorMsg" class="highlight"></span><input type="button" value="Submit Changes" onclick="checkInputs();" />&nbsp;&nbsp;&nbsp;<input type="reset" value="Reset to Previous" /><input type="hidden" name="processmode" value="1" /></td>
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
  document.getElementById("modForm").books_book_title.focus();
  window.onresize=positionPopup;
  var imageSearch;
  var first = true;
  var img_count = 0;

  $j('#trashInner').droppable({
    accept: '.library',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      growImage(ui.draggable.attr('id'),ui.draggable.width(),ui.draggable.height(),0,0,1,function(imageName) {
        new Ajax.Request('delete_file.php',{
          method: 'post',
          parameters: {fileName: imageName},
          onFailure: function(transport) {null;},
          onSuccess: function(transport) {null;}
        });
        $(imageName).remove();
      });
    }
  });
  $j('#currentCover').droppable({
    accept: '.library',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      selectOne(ui.draggable.attr('id'));
    }
  });
  $j('#vaultInner').droppable({
    accept: '.scrape',
    activeClass: 'bYellow',
    hoverClass: 'bRed',
    drop: function(event, ui) {
      $('upload_thumb').reset();
      $('imageURL').value = ui.draggable.attr('alt');
      $('popup').show();
      startUpload();
      $('upload_thumb').submit();
    }
  });

  var request = null;
  var request2 = null;
  try {
   request = new XMLHttpRequest();
   request2 = new XMLHttpRequest();
  } catch (trymicrosoft) {
   try {
     request = new ActiveXObject("Msxml2.XMLHTTP");
     request2 = new ActiveXObject("Msxml2.XMLHTTP");
   } catch (othermicrosoft) {
     try {
       request = new ActiveXObject("Microsoft.XMLHTTP");
       request2 = new ActiveXObject("Microsoft.XMLHTTP");
     } catch (failed) {
       request = null;
       request2 = null;
       alert("Ajax not supported by your browser!");
     }
   }
  }

  function getDBInfo() {
    document.getElementById("loading").innerHTML = "<img src='../images/ajax_loader_2.gif' alt='Loading' />";
    document.getElementById("newImage").innerHTML = "<img src='../images/ajax_loader_1.gif' alt='New Cover' />";
    document.getElementById("exRow1").style.display = "table-row";
    document.getElementById("exRow2").style.display = "table-row";
    document.getElementById("exRow3").style.display = "table-row";
    document.getElementById("exRow4").style.display = "table-row";
    var url = "get_isbndb_info.php?isbn=<?php echo $f_books_isbn; ?>";
    request.open("GET", url, true);
    request.onreadystatechange = updatePage;
    request.send(null);
    url = "get_book_cover.php?isbn=<?php echo $f_books_isbn; ?>";
    request2.open("GET", url, true);
    request2.onreadystatechange = updatePage2;
    request2.send(null);
  }

  function updatePage() {
    if (request.readyState == 4) {
      document.getElementById("loading").innerHTML = "";
      bookXML = request.responseXML;
      var nValue = bookXML.getElementsByTagName("title")[0].childNodes[0].nodeValue;
      document.getElementById("lu_title").value = nValue;
      document.getElementById("newTitle").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("author")[0].childNodes[0].nodeValue;
      document.getElementById("lu_author").value = nValue;
      document.getElementById("newAuthor").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("publisher")[0].childNodes[0].nodeValue;
      document.getElementById("lu_publisher").value = nValue;
      document.getElementById("newPublisher").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("copyright")[0].childNodes[0].nodeValue;
      document.getElementById("lu_copyright").value = nValue;
      document.getElementById("newCopyright").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("genre")[0].childNodes[0].nodeValue;
      document.getElementById("lu_genre").value = nValue;
      document.getElementById("newGenre").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("cover")[0].childNodes[0].nodeValue;
      document.getElementById("lu_cover").value = nValue;
      document.getElementById("newCover").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("isbn")[0].childNodes[0].nodeValue;
      document.getElementById("lu_isbn").value = nValue;
      document.getElementById("newISBN").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("pages")[0].childNodes[0].nodeValue;
      document.getElementById("lu_pages").value = nValue;
      document.getElementById("newPages").innerHTML = nValue;
      nValue = bookXML.getElementsByTagName("notes")[0].childNodes[0].nodeValue;
      document.getElementById("lu_notes").value = nValue;
      document.getElementById("newNotes").innerHTML = nValue;
    }
  }

  function updatePage2() {
    if (request2.readyState == 4) {
      document.getElementById("newImage").innerHTML = request2.responseText;
      if (request2.responseText.match("new_cover") == null) {
        document.getElementById("lu_image").value = "not";
      }
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
      $("errorMsg").update("Updating database ... <img src='../images/ajax_loader_2.gif' alt='Updating' />   ");
      $("modForm").request({
        onComplete: function(transport) {
          if (transport.responseText.match(/Error/i)) {
            $("errorMsg").update(transport.responseText + "   ");
          } else {
            window.location = "bookdetails.php?bookid=" + transport.responseText;
          }
        }
      });
    } else {
      $("errorMsg").update("Error: Enter highlighted items.  ");
    }
  }

  function checkNull(input, title) {
    if ($(input).value == "") {
      $(title).className = "highlight";
      return 1;
    } else {
      $(title).className = "";
      return 0;
    }
  }

  function scrapeGoogle() {
    if (!$("selectScrape").visible()) {
      $("selectScrape").show();
      $("allScrapes").update("Loading scrapes ... <img src='../images/ajax_loader_1.gif' alt='Loading' />");
      first = true;
      img_count = 0;
      imageSearch = new google.search.ImageSearch();
      imageSearch.setResultSetSize(google.search.Search.LARGE_RESULTSET);
      imageSearch.setSearchCompleteCallback(this, searchComplete, null);
      imageSearch.execute($('books_book_title').value + ' ' + $('books_author_name').value + ' book cover');
    } else {
      $("selectScrape").hide();
    }
  }

  function startUpload() {
    $('upload_thumb').hide();
    $('uploadResults').update("<p>Loading images ... <img src='../images/ajax_loader_1.gif' alt='Loading' /></p>");
    $('uploadResults').show();
    positionPopup();
  }

  function finishUpload(result) {
    Element.update("uploadResults", "<p>" + result + "<br /><br /><input type=\"button\" value=\"Close Window\" onclick=\"javascript:toggleForm();\" /></p>");
    positionPopup();
  }

  function getImages() {
    $("allImages").update("Loading images ... <img src='../images/ajax_loader_1.gif' alt='Loading' />");
    var now = new Date();
    new Ajax.Request('get_images.php?time=' + now.getTime(),{
      onComplete: function(transport) {
        var n=$('allImages').update();
        var names = transport.responseText.evalJSON();
        for ( var i=0; i < names.length; i++ ) {
          var d=document.createElement('img');
          d.id = names[i];
          d.className = 'CustomDraggable dim library';
          d.src = "thumbs/" + names[i];
          d.alt = "Library Image #" + i;
          d.ondblclick = function() {
            selectOne(this.id);
          }
          n.appendChild(d);
          $j("#" + names[i].replace(/\./g,"\\.")).draggable({revert: 'invalid', helper: 'clone'});
        }
      }
    });
  }

  function toggleImages() {
    if (!$("selectImage").visible()) {
      $("selectImage").show();
      if ($('coverFile').value == "") {
        getImages();
      }
    } else {
      $("selectImage").hide();
      $("popup").hide();
    }
  }

  function positionPopup() {
    $("popup").clonePosition("allImages", {setWidth: false, setHeight: false, offsetLeft: 1, offsetTop: 1});
  }

  function toggleForm() {
    if (!$("popup").visible()) {
      $("uploadResults").hide();
      $("upload_thumb").show();
      $("upload_thumb").reset();
      positionPopup();
      $("popup").show();
    } else {
      $("popup").hide();
      getImages();
    }
  }

  function selectOne(imageName) {
    if ($('coverFile').value == "") {
      $('coverFile').value = imageName;
      $('popup').hide();
      $('currentCover').setOpacity(0.4);
      iHold = $('sTitle').innerHTML;
      $('sTitle').update("New Image<br /><br /><a href='javascript:selectOne(\"\");'>Re-select</a>");
      $('allImages').update("<img src='thumbs/" + imageName +"' alt='" + imageName + "' />");
    } else {
      $('coverFile').value = "";
      $('popup').hide();
      $('currentCover').setOpacity(1.0);
      $('sTitle').update(iHold);
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
        $j("#" + newImg.id).draggable({revert: 'invalid', helper: 'clone'});
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
