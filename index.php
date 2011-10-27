<html>

  <head>
    <title>Dinner booking</title>
  </head>

  <body>
    <h1>Dinner booking</h1>
    <hr />

<?php

// Configuration
$csvfile = "dates.csv";

// If a file exists, then the booking system is open!
if (($handle = fopen($csvfile, "r")) !== FALSE) {

  // Print the table header
  echo '<table border="1">';
  echo '<tr>';
  echo '<th>Date</th>';
  echo '<th>Total<br/>attending</th>';
  echo '<th colspan="99">Attendees</th>';
  echo '</tr>';

  // Open the CSV file and iterate over the rows
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    // Process row (the event date and time)
    $timestamp_raw = $data[0];
    $timestamp_str = strftime("%A<br />%d-%h %Y<br />%H:%M %p", strtotime($data[0]));
    echo "<tr id='$timestamp_raw'><th>$timestamp_str</th>";

    // Process each column (the attendees)
    for ($c=1; $c < count($data); $c++) {
      $attendee = trim($data[$c]);
      if (!empty($attendee)) {
        $code = "$timestamp_raw:$c";
        echo "<td id='$code'>$attendee</td>";
      }
    }

    echo "</tr>";
  }

  echo "</table>";
  fclose($handle);

} else {
  echo "<div>The booking system is closed!</div>";
}
?>
