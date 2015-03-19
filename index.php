<?php
// Get RSS Eye Library (Repo: shttps://github.com/bryankaraffa/rss_eye)
include_once('./includes/rss_eye/rss_eye.php');

// Required Settings (stored in config.php):
//    $mysql_user = MySQL Username
//    $mysql_pass = MySQL User Password
//    $mysql_db   = MySQL DB Name
//    $mysql_host = MySQL Hostname
include_once('./config.php');

$newItems = Array();
/////////////////////////////////////////////
// Functions
/////////////////////////////////////////////
// Used to check if string contains text
function containsText($string, $text) {
  if (strpos($string, $text) !== false) {
    return true;
  }
  else { return false; }
}

// Inserts an RSS record into the DB
function insertItem($item) {
  // Get the mysqli object
  global $link;

  $title = $link->real_escape_string($item->title);
  $rssLink = $link->real_escape_string($item->link);
  $guid = $link->real_escape_string($item->guid);
  $pubDate = $link->real_escape_string($item->pubDate);
  $description = $link->real_escape_string($item->description);

  $query = "INSERT INTO `rss_items` (`title`, `link`, `guid`, `pubDate`, `description`) VALUES ('$title', '$rssLink', '$guid', '$pubDate', '$description');";

  if (mysqli_query($link, $query)) {
    return true;
  }
  else {
    print($mysqli_error($link)."\n\n");
    return false;
  }

}

function getItems() {
  global $link;
  $return = [];
  /* Select queries return a resultset */
  if ($result = $link->query("SELECT * FROM rss_items ORDER BY  `lastUpdated` DESC")) {
      while($row = $result->fetch_array()) {
        array_push($return, $row);
      }

      /* free result set */
      $result->close();
      return $return;
  }
}
function printItems($itemsArray) {
  foreach ($itemsArray as $item) {
    print("
        <h1> $item[title] </h1>
        <h4> <b>pubDate: </b> $item[pubDate] | <b>Last Updated: </b> $item[lastUpdated] </h4>
        <p> $item[description] </p>
        <p style='font-size:50%'> <i> link: $item[link] <br\> guid: $item[guid] </i> </p>
        <hr />
        ");
  }
}
/////////////////////////////////////////////
// Site Scripts
/////////////////////////////////////////////
// reddit.com/r/Jeep
$r_jeep = new rss_eye('http://www.reddit.com/r/Jeep/new.rss', './tmp/');
foreach ($r_jeep->items['new'] as $item) {
  if ( containsText($item->title, 'Pismo') || containsText($item->title,'TJ') || containsText($item->title, 'California') ) { //if $item->title contains 'Pismo'
    array_push($newItems, $item);
  }
}

// 50campfires
$fiftycampfires = new rss_eye('http://50campfires.com/feed/?s=cheap', './tmp/');
foreach ($fiftycampfires->items['new'] as $item) {
  if ( containsText($item->title, 'cheap') ) {
    array_push($newItems, $item);
  }
}

//

if (isset($argv)) {
  foreach ($argv as $arg) {
    switch ($arg) {
      case '--disable-output':
          $disable_output = true;
          break;
   }
  }
}
/* attempt connection */
$link = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


// Insert New Results
if (count($newItems) > 0) {
  // DO inserts
  foreach ($newItems as $item) {
    insertItem($item);
  }
}

// Print Saved Items
if (!isset($disable_output) || $disable_output != true) {
  $items = getItems();
  printItems($items);
}

/* close connection */
mysqli_close($link);

?>
