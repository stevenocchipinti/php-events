<?php
/*
 *  Dinner booking system
 *
 *  A simple CSV backed webapp to come to a group consensus for a date for
 *  when to catchup for dinner.
 *
 *  Warning, this code isn't the prettiest, so I tried to make up for it with
 *  comments - I think I may be out of touch with PHP :(
 *
 */

// =============================================================================
// Globals
// =============================================================================
$csvfile = "dates.csv";
$notice = "";


// =============================================================================
// CSV Helpers
// =============================================================================
function read_csv() {
  $lines = explode("\n", file_get_contents($GLOBALS['csvfile']));
  foreach ($lines as $line)
    $csv[] = str_getcsv($line);
  return $csv;
}

function write_csv($data) {

  foreach ($data as $line) {
    $contents[] = implode(",", $line);
  }
  file_put_contents($GLOBALS['csvfile'], implode("\n", $contents));
}


// =============================================================================
// SUBMISSION: Appending a new attendee to a date
// =============================================================================
if (isset($_POST['available'])) {
  $csv = read_csv();                        // Load the CSV file
  foreach ($_POST as $k => $v) {            // Loop through the post vars
    if ($v == "#avail") {                   // $k will be the the date
      foreach ($csv as $index => $line) {   // Loop through the lines of CSV
        if ($line[0] == $k) {               // Only get the selected date line
          $csv[$index][] = $_POST['name'];  // Append the name to that line
        }
      }
    }
  }
  write_csv($csv);                          // Save it!
  $notice = "Saved!";                       // Tell the user
}


// =============================================================================
// SUBMISSION: Deleting an attendee from a date
// =============================================================================
elseif (isset($_POST['delete'])) {
  $csv = read_csv();                        // Load the CSV file
  foreach ($_POST as $k => $v) {            // Loop through the post vars
    if ($v == "#delete") {                  // $k will be a composite...
      $items = explode("#", $k);            // Split $k up into:
      $date = $items[0];                    // - The date (CSV line) to find
      $name = $items[1];                    // - The name to find and delete
      $name = str_replace("_", " ", $name); // Post values have _'s
      foreach ($csv as $index => $line) {   // Loop through the lines of CSV
        if ($line[0] == $date) {            // Only get the selected date line
          $key = array_search($name, $line);// Find index of the name to delete
          if ($key) {                       // If found on the correct line
            unset($csv[$index][$key]);      // Delete it!
          }
        }
      }
    }
  }
  write_csv($csv);                          // Save it!
  $notice = "Deleted!";                     // Tell the user
}


// =============================================================================
// Generate the HTML table from CSV
// =============================================================================
function generateBody() {

  // If a file exists, then the booking system is open!
  if (($handle = fopen($GLOBALS['csvfile'], 'r')) !== FALSE) {

    // Print the form
    $out .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
    $out .= '<div id="user-input">';
    $out .= '<label for="name">Your name:</label>';
    $out .= '<input name="name"/>';
    $out .= '</div>';

    // Print the table header
    $out .= '<table>';
    $out .= '<tr>';
    $out .= '<th>Date</th>';
    $out .= '<th>Your<br/>availability</th>';
    $out .= '<th>Total<br/>attendees</th>';
    $out .= '<th>Attendees</th>';
    $out .= '</tr>';

    // Open the CSV file and iterate over the rows
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      // Process row (the event date and time)
      $timestamp_raw = $data[0];
      $timestamp_str = strftime(
        "%A<br />%d-%h %Y<br />%l:%M %p",
        strtotime($data[0])
      );
      $out .= "<tr id='$timestamp_raw'>";
      $out .= "<th>$timestamp_str</th>";

      // Insert checkbox to indicate availability
      $out .= "<td>";
      $out .= "<input
        type='checkbox'
        name='$timestamp_raw'
        value='#avail'/>";
      $out .= "</td>";

      // Insert the total attendees column
      $num = count($data);
      $out .= "<td>" . ($num-1) . "</td>";

      // Insert each attendee with a delete checkbox
      $out .= "<td class='attendee'>";
      $attendees = array_splice($data, 1);
      foreach ($attendees as $attendee) {
        $out .= "<input
          type='checkbox'
          name='$timestamp_raw#$attendee'
          value='#delete'/>";
       $out .= "$attendee<br />";
      }
      $out .= "</td>";

      $out .= "</td></tr>";
    }

    fclose($handle);
    $out .= "</table>";
    $out .= "<div id='submission'>";
    $out .= "<input
      type='submit'
      name='available'
      value='Submit availability'/>";
    $out .= "<input
      type='submit'
      name='delete'
      value='Delete selected attendees'/>";
    $out .= "</div>";
    $out .= "</form>";

    return $out;

  } else {
    $GLOBALS['notice'] = "The booking system is closed!";
  }
}


// =============================================================================
// Flash messages
// =============================================================================
function flash() {
  global $notice;

  if ($notice)
    return "<div class='flash' id='notice'>$notice</div>";
  else
    return "<div id='info'>Please fill in your name and which times are most suitable for you.</div>";

}

// Do this before loading the page
$table = generateBody();

?>

<html>

  <head>
    <title>Dinner booking</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
  </head>

  <body>
    <div id="container">
      <h1>Dinner event booking system</h1>

      <div id="flashbox">
        <?php echo flash(); ?>
      </div>

      <?php echo $table; ?>
    </div>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript" ></script>
    <script src="javascript.js" type="text/javascript" /></script>
  </body>
</html>
