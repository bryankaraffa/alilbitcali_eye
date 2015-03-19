<?php
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
?>
