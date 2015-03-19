<?php

include_once('./includes/rss_eye/rss_eye.php');

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




/////////////////////////////////////////////
// Debugging
/////////////////////////////////////////////
if (count($newItems) > 0) {
  print(json_encode($newItems));
}

?>
