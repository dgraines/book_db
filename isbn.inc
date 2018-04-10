<?php
// ISBN utility functions

// Use ISBNdb.com to look up book if necessary
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
  	
  	// Urls
  	$url_details = "http://isbndb.com/api/books.xml?access_key=$accessKey&results=details&index1=isbn&value1=$query";
  	$url_texts = "http://isbndb.com/api/books.xml?access_key=$accessKey&results=texts&index1=isbn&value1=$query";
  	$url_prices = "http://isbndb.com/api/books.xml?access_key=$accessKey&results=prices&index1=isbn&value1=$query";
  	$url_subjects = "http://isbndb.com/api/books.xml?access_key=$accessKey&results=subjects&index1=isbn&value1=$query";
  	
  	// API lookup ISBN value at isbndb.com
  	$xml_details = @simplexml_load_file($url_details) or die ("No Details XML file loaded.") ;
  	$xml_texts = @simplexml_load_file($url_texts) or die ("No Texts XML file loaded.") ;
  //	$xml_prices = @simplexml_load_file($url_prices) or die ("No Prices XML file loaded.") ;
  	$xml_subjects = @simplexml_load_file($url_subjects) or die ("No Subjects XML file loaded.") ;
  	
  	// Parse Data
  	$lookup_isbn = $query;
   	$lookup_book_title = $xml_details->BookList[0]->BookData[0]->Title ;
   	$lookup_author_name = $xml_details->BookList[0]->BookData[0]->AuthorsText ;
   	$lookup_publisher = $xml_details->BookList[0]->BookData[0]->PublisherText ;
   	$pat_cover[0] = "/(?i:hard\s{0,}(cover|back))/";
   	$rep_cover[0] = "Hard Cover";
   	$pat_cover[1] = "/(?i:(trade\s{0,}(edition){0,})|(soft\s{0,}(cover|back)))/";
   	$rep_cover[1] = "Trade Edition";
   	$pat_cover[2] = "/(?i:paper(back){0,})/";
   	$rep_cover[2] = "Paperback";
   	if (preg_match("/(?i:hard cover|trade edition|paperback)/",preg_replace($pat_cover,$rep_cover,$xml_details->BookList[0]->BookData[0]->Details[0]['edition_info']),$results)) {
      $lookup_cover_type = $results[0];
    } else {
      $lookup_cover_type = "Hard Cover";
    }
    if (preg_match("/(\d{2,})\s{0,}p(age|g|\.)/",$xml_details->BookList[0]->BookData[0]->Details[0]['physical_description_text'],$results)) {
      $lookup_pages = $results[1];
    } else {
      $lookup_pages = "";
    }
   	if (preg_match("/(\d{4})/",$xml_details->BookList[0]->BookData[0]->Details[0]['edition_info'] . $xml_details->BookList[0]->BookData[0]->PublisherText,$results)) {
   	  $lookup_copyright_year = $results[1];
    } else {
      $lookup_copyright_year = "";
    }
   	$lookup_notes = $xml_texts->BookList[0]->BookData[0]->Summary . $xml_texts->BookList[0]->BookData[0]->Notes ;
   	$pat_genre[0] = "/(?i:science\s{0,}fiction)/";
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
   	if (preg_match("/(?i:science\s{0,}fiction|biography|fantasy|fiction|humor|religion|technical)/",preg_replace($pat_genre,$rep_genre,$xml_subjects->BookList[0]->BookData[0]->Subjects[0]->Subject[0]),$results)) {
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

?>
