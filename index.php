<?php
// Get RSS Eye Library (Repo: shttps://github.com/bryankaraffa/rss_eye)
include_once('./includes/rss_eye/rss_eye.php');

// Required Settings (stored in config.php):
//    $mysql_user = MySQL Username
//    $mysql_pass = MySQL User Password
//    $mysql_db   = MySQL DB Name
//    $mysql_host = MySQL Hostname
include_once('./config.php');

include_once('./functions.php');

$newItems = Array();

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
