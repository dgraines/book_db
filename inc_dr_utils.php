<?php

//------------------------------------------------------
// Login utility functions

function write_log_in( $text ) {
  echo "
<p>$text</p>
<form id='loginform' method='post' action=''>
	<p>User ID: <input type='text' name='user_name' id='user_name' />
	Password: <input type='password' name='password' />
	<input type='submit' value='Log In' /></p>
</form>
<script type='text/javascript'>
  /* <![CDATA[ */
  document.getElementById('loginform').user_name.focus();
  /* ]]> */
</script>
";
} // end write_log_in function

function write_log_out( ) {
  $user_name = $_SESSION['valid_user'];
  echo "
    <form method='post' action=''>
    <p>You are currently logged in as user: $user_name.
    <input type='hidden' name='log_out' value='1' />
    <input type='submit' value='Log Out' /></p>
    </form>
  ";
} // end write_log_out function

function verify() {
  // check to see if user is already logged in
  if ( $_SESSION['valid_user'] != "" ) {
    // user who are logged in my wish to log out
    if (filter_input(INPUT_POST, 'log_out', FILTER_VALIDATE_INT) == '1') {
      $_SESSION['valid_user'] = '';
    } else {
      write_log_out();
      return true;
    }
  }
  // check for login form data
  $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  if ( $user_name && $password ) {
    // verify password and log in to database
    $db = mysql_connect("localhost", "draines", "applegsc");
    if ( $db ) {
      mysql_select_db("login",$db);
      $sql = "SELECT * FROM id WHERE username = '$user_name'";
      $result = mysql_query($sql);
      $myrow = mysql_fetch_array($result);
      $check_pwd = $myrow["password"];
      if ( $password == $check_pwd ) {
        // register session variable and exit the verify function
        $_SESSION['valid_user'] = $user_name;
        echo "<p>Login was successful!<p>";
        write_log_out();
        return true;
      } else {
        // bad user and password
        $text = "User Name and Password could not be validated.";
        write_log_in( $text );
      }
    } else {
      // Cannot connect to mysql
      echo "
      <p>Could not connect to mySQL server.<br />
      Please contact the server administrator.<br /><br />
      <a href='http://www.darrelraines.com/'>Back to the home page.</a><p>";
    }
  } else {
    // no credentials entered - user must log in
    $text = "In order to make changes to this entry, you must be logged in.";
    write_log_in( $text );
  }
} // end verify function

//------------------------------------------------------
// ISBN utility functions

// Use ISBNdb.com to look up book (JSON interface)
function read_isbndb_json($query) {
  // Access key for Darrel Raines on ISBNdb.com
  $accessKey = "R6L4CRYX";
  // Assign defaults if we don't have an ISBN to lookup
  $lookup_book_title = ""; 
  $lookup_author_name = ""; 
  $lookup_publisher = ""; 
  $lookup_cover_type = ""; 
  $lookup_pages = ""; 
  $lookup_copyright_year = ""; 
  $lookup_notes = ""; 
  $lookup_genre = ""; 
  if ($query) {
    // Handle input from a barcode scanner (including price code)
    if (preg_match("/([0-9xX]{9,}) [0-9]{1,}/",$query,$results)) {
      $query = $results[1];
    }
    $query = ISBNConvert($query);
    $strip_isbn = preg_replace("/-/", "", $query);
    // API to lookup ISBN value at isbndb.com (JSON version)
    $url_details = "http://isbndb.com/api/v2/json/$accessKey/book/$strip_isbn";
    $this_isbn = file_get_contents($url_details);
    if ($this_isbn) {
      // Parse Data
      $tbook = json_decode($this_isbn);
      if (!$tbook->error) {
        $lookup_isbn = $query;
        $lookup_book_title = $tbook->data[0]->title;
        $lookup_book_title = ucwords(strtolower($lookup_book_title));
        $lookup_author_name = ""; 
        foreach ($tbook->data[0]->author_data as $val) {
          $lookup_author_name .= ($lookup_author_name ? ", " : "") . $val->name ;
        }
        $lookup_author_name = ucwords(strtolower($lookup_author_name));
        $lookup_publisher = $tbook->data[0]->publisher_name;
        if (!$lookup_publisher) {$lookup_publisher = $tbook->data[0]->publisher_text;}
        $lookup_publisher = ucwords(strtolower($lookup_publisher));
       	$pat_cover[0] = "/(?i:hard\s{0,}(cover|back))/";
       	$rep_cover[0] = "Hard Cover";
       	$pat_cover[1] = "/(?i:(trade\s{0,}(edition){0,})|(soft\s{0,}(cover|back)))/";
       	$rep_cover[1] = "Trade Edition";
       	$pat_cover[2] = "/(?i:paper(back){0,})/";
       	$rep_cover[2] = "Paperback";
       	if (preg_match("/(?i:hard cover|trade edition|paperback)/",preg_replace($pat_cover,$rep_cover,$tbook->data[0]->edition_info),$results)) {
          $lookup_cover_type = $results[0];
        } else {
          $lookup_cover_type = "Hard Cover";
        }
        if (preg_match("/(\d{2,})\s{0,}p(age|g|\.)/",$tbook->data[0]->physical_description_text,$results)) {
          $lookup_pages = $results[1];
        } else {
          $lookup_pages = "";
        }
       	if (preg_match("/(\d{4})/",$tbook->data[0]->edition_info . $tbook->data[0]->publisher_text ,$results)) {
       	  $lookup_copyright_year = $results[1];
        } else {
          $lookup_copyright_year = "";
        }
       	$lookup_notes = $tbook->data[0]->summary . $tbook->data[0]->notes ;
       	$pat_genre[0] = "/(?i:science[\s_-]{0,}fiction)/";
       	$rep_genre[0] = "Science Fiction";
       	$pat_genre[1] = "/(?i:(auto){0,}biography)/";
       	$rep_genre[1] = "Biography";
       	$pat_genre[2] = "/(?i:fantasy)/";
       	$rep_genre[2] = "Fantasy";
       	$pat_genre[3] = "/(?i:fiction)/";
       	$rep_genre[3] = "Fiction";
       	$pat_genre[4] = "/(?i:humor|fun)/";
       	$rep_genre[4] = "Humor";
       	$pat_genre[5] = "/(?i:religion|inspiration(al){0,})/";
       	$rep_genre[5] = "Religion";
       	$pat_genre[6] = "/(?i:technical|engineering)/";
       	$rep_genre[6] = "Technical";
       	if (preg_match("/(?i:science fiction|biography|fantasy|fiction|humor|religion|technical)/",preg_replace($pat_genre,$rep_genre,$tbook->data[0]->subject_ids[0]),$results)) {
       	  $lookup_genre = $results[0];
        } else {
          $lookup_genre = "Miscellaneous";
        }
      } else {
        $lookup_isbn = "Error: " . $tbook->error;
      }
    } else {
      $lookup_isbn = "Error: File not loaded from isbndb.com .";
    }
  } else {
    $lookup_isbn = "Error: No legal ISBN provided for lookup.";
  }
  return array($lookup_isbn, $lookup_book_title, $lookup_author_name, $lookup_publisher, $lookup_cover_type, $lookup_pages, $lookup_copyright_year, $lookup_notes, $lookup_genre);
}

// Use ISBNdb.com to look up book
function read_isbndb($query) {
  // Access key for Darrel Raines on ISBNdb.com
  $accessKey = "R6L4CRYX";
  
  // Assign defaults if we don't have an ISBN to lookup
  $lookup_genre = "Fiction";
  $lookup_cover_type = "Hard Cover";
  
  if ($query):
  
    // Handle input from a barcode scanner (including price code)
    if (preg_match("/([0-9xX]{9,}) [0-9]{1,}/",$query,$results)) {
      $query = $results[1];
    }
    $query = ISBNConvert($query);
    $strip_isbn = preg_replace("/-/", "", $query);
  	
  	// Urls
  	$url_details = "http://isbndb.com/api/v2/xml/$accessKey/book/$strip_isbn";
  	
  	// API lookup ISBN value at isbndb.com
  	$xml_details = @simplexml_load_file($url_details) or die ("No book XML file loaded for that ISBN.") ;
  	
  	// Parse Data
  	$lookup_isbn = $query;
   	$lookup_book_title = $xml_details->data[0]->title ;
    $lookup_author_name= "";
    foreach ($xml_details->data[0]->author_data as $auth) {
      $lookup_author_name .= $auth->name ;
    }
//   	$lookup_author_name = $xml_details->data[0]->author_data[0]->name ;
   	$lookup_publisher = $xml_details->data[0]->publisher_name ;
   	$pat_cover[0] = "/(?i:hard\s{0,}(cover|back))/";
   	$rep_cover[0] = "Hard Cover";
   	$pat_cover[1] = "/(?i:(trade\s{0,}(edition){0,})|(soft\s{0,}(cover|back)))/";
   	$rep_cover[1] = "Trade Edition";
   	$pat_cover[2] = "/(?i:paper(back){0,})/";
   	$rep_cover[2] = "Paperback";
   	if (preg_match("/(?i:hard cover|trade edition|paperback)/",preg_replace($pat_cover,$rep_cover,$xml_details->data[0]->edition_info),$results)) {
      $lookup_cover_type = $results[0];
    } else {
      $lookup_cover_type = "Hard Cover";
    }
    if (preg_match("/(\d{2,})\s{0,}p(age|g|\.)/",$xml_details->data[0]->physical_description_text,$results)) {
      $lookup_pages = $results[1];
    } else {
      $lookup_pages = "";
    }
   	if (preg_match("/(\d{4})/",$xml_details->data[0]->edition_info . $xml_details->data[0]->publisher_text ,$results)) {
   	  $lookup_copyright_year = $results[1];
    } else {
      $lookup_copyright_year = "";
    }
   	$lookup_notes = $xml_details->data[0]->summary . $xml_details->data[0]->notes ;
   	$pat_genre[0] = "/(?i:science[\s_-]{0,}fiction)/";
   	$rep_genre[0] = "Science Fiction";
   	$pat_genre[1] = "/(?i:(auto){0,}biography)/";
   	$rep_genre[1] = "Biography";
   	$pat_genre[2] = "/(?i:fantasy)/";
   	$rep_genre[2] = "Fantasy";
   	$pat_genre[3] = "/(?i:fiction)/";
   	$rep_genre[3] = "Fiction";
   	$pat_genre[4] = "/(?i:humor|fun)/";
   	$rep_genre[4] = "Humor";
   	$pat_genre[5] = "/(?i:religion|inspiration(al){0,})/";
   	$rep_genre[5] = "Religion";
   	$pat_genre[6] = "/(?i:technical|engineering)/";
   	$rep_genre[6] = "Technical";
   	if (preg_match("/(?i:science\s{0,}fiction|biography|fantasy|fiction|humor|religion|technical)/",preg_replace($pat_genre,$rep_genre,$xml_details->data[0]->subject_ids[0]),$results)) {
   	  $lookup_genre = $results[0];
    } else {
      $lookup_genre = "Miscellaneous";
    }
  
  endif;
  return array($lookup_isbn, $lookup_book_title, $lookup_author_name, $lookup_publisher, $lookup_cover_type, $lookup_pages, $lookup_copyright_year, $lookup_notes, $lookup_genre);
}

// ISBN Conversion function, 10->13 and proper hyphen positions
function ISBNConvert($isbn) {
  $v = 0;
  $n = 0;
  $Result = "";
  $isbn = preg_replace("/([\d-]+) \d+$/", "$1", $isbn, 1);
  $isbn = preg_replace("/[\s-]+/","",$isbn,-1);
  $len = strlen($isbn);
  if ($len==10) {
    $isbn13 = "978" . substr($isbn, 0, 9);
    for ($i=0; $i<12; $i++) {
      if ($Result=="") {
        $c = substr($isbn13, $i, 1);
        if ($c>="0" && $c<="9") {
          $v = $c - 0;
          if (($i % 2)!=0) {
            $v = 3 * $v;
          }
          $n = $n + $v; 
        } else {
          $Result = "Error-Non_Digit";
        }
      }
    }
    if ($Result=="") {
      $n = $n % 10;
      if ($n!=0) {
        $n = 10 - $n;
      }
      $Result = $isbn13 . $n;
   }
  } else if ($len==13) {
    $Result = $isbn;
  } else {
    $Result = "Error-Wrong_Length";
  }
  // Format the ISBN with hypens
  if (substr($Result, 0, 1)!="E" && substr($Result, 3, 1)<="1") {
    $Group = substr($Result, 3, 1) - 0;
    $Publisher = substr($Result, 4, 2) - 0;
    $Pub_Long = substr($Result, 4, 4) - 0;
    if ($Group == 0) {
      if ($Publisher <= 19) {
        $hyphen_pos = 6;
      } else if ($Publisher <= 69) {
        $hyphen_pos = 7;
      } else if ($Publisher <= 84) {
        $hyphen_pos = 8;
      } else if ($Publisher <= 89) {
        $hyphen_pos = 9;
      } else if ($Publisher <= 94) {
        $hyphen_pos = 10;
      } else {
        $hyphen_pos = 11;
      }
    } else { // Group is 1 
      if ($Publisher <= 09) {
        $hyphen_pos = 6;
      } else if ($Publisher <= 39) {
        $hyphen_pos = 7;
      } else if ($Publisher <= 54) {
        $hyphen_pos = 8;
      } else if ($Pub_Long <= 8697) {
        $hyphen_pos = 9;
      } else if ($Pub_Long <= 9989) {
        $hyphen_pos = 10;
      } else {
        $hyphen_pos = 11;
      }
    }
    $Result = substr($Result, 0, 3) . "-" . substr($Result, 3, 1) . "-" . substr($Result, 4, ($hyphen_pos-4)) . "-" . substr($Result, $hyphen_pos, (12-$hyphen_pos)) . "-" . substr($Result, 12, 1);
  }
  return $Result;
}

// ISBN conversion function, 10 or 13 -> 10 with no hyphens
function ISBN10($isbn) {
  $Result = preg_replace("/[\s-]+/","",$isbn,-1);
  if (strlen($Result)==13) {
    $Result = substr($Result, 3, 10);
  }
  if (strlen($Result)==10) {
    $Result = substr($Result, 0, 9);
    $stack = 0;
    for ($i = 0; $i < 9; $i++) {
      $stack += $Result[$i] * ($i + 1);
    }
    $check_digit = $stack % 11;
    if ($check_digit == 10) {$check_digit = "X";}
    $Result = $Result . $check_digit;
  } else {
    $Result = "0000000000";
  }
  return $Result;
}

//------------------------------------------------------
// PHP functions for MySQL database access
// Meant to be used with database forms

// Function to get form selection values.
function read_selections ($db_src, $db_user, $db_password, $db_name, $field, $table, $default) {
  $w_sqlstr = "SELECT ". $field . " FROM " . $table;
	$connect = mysql_connect($db_src, $db_user, $db_password);
	$success = mysql_select_db($db_name, $connect);
	$cursor  = mysql_query($w_sqlstr, $connect);
	$html_out = "<select name=\"~*~";
  $html_out .= "\">\n";
	for ($rows = 0; $row = mysql_fetch_row($cursor); $rows++) {
		$value = $row[0];
		$str   = $row[0];
		$html_out .= "<option ";
		if ($value == $default) {
			$html_out .= "selected ";
		}
		$html_out .= "value=\"" . $value . "\">" . $str . "\n";
	}
	$html_out .= "</select>\n";
	mysql_close($connect);
	return $html_out;
}

// Function to get form radio values.
function read_radio ($db_src, $db_user, $db_password, $db_name, $field, $table, $default) {
  $w_sqlstr = "SELECT ". $field . " FROM " . $table;
	$connect = mysql_connect($db_src, $db_user, $db_password);
	$success = mysql_select_db($db_name, $connect);
	$cursor  = mysql_query($w_sqlstr, $connect);
	$html_out = "";
	for ($rows = 0; $row = mysql_fetch_row($cursor); $rows++) {
		$value = $row[0];
		$str   = $row[0];
		if ($rows == 0) {
    	$html_out .= "<p>";
		} else {
			$html_out .= "<br>";
		}
		$html_out .= "<input type=\"radio\" name=\"~*~\" value=\"" . $value . "\"";
		if ($value == $default) {
			$html_out .= " checked";
		}
		$html_out .= ">" . $str . "\n";
	}
	$html_out .= "</p>";
	mysql_close($connect);
	return $html_out;
}

//------------------------------------------------------
// PHP Graphic Utilities

// Create an "No Image" image to be used on web pages
function create_image () {
  $im = ImageCreate(80,110);
  $color1 = ImageColorAllocate($im,0xFF,0xFF,0xFF);
  $color2 = ImageColorAllocate($im,0xFF,0x00,0x00);
  $color3 = ImageColorAllocate($im,0x00,0x00,0x00);
  ImageFilledRectangle($im,0,0,80,110,$color1);
  ImageEllipse($im,40,70,60,60,$color2);
  ImageLine($im,70,40,10,100,$color2);
  ImageTTFText($im,11,0,3,18,$color3,'veranda.ttf','No Image');
  return Imagejpeg($im);
}

?>
