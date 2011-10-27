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
 * TODO: Maybe use the read_csv() functions in generateTable()
 * TODO: CSS!!!
 * TODO: Jquery to make the flash notices fade in?
 * TODO: Discus?
 *
 */

// =============================================================================
// Configuration
// =============================================================================
$csvfile = "dates.csv";


// =============================================================================
// CSV Helpers
// =============================================================================
function read_csv($filename) {
  $lines = explode("\n", file_get_contents("$filename"));
  foreach ($lines as $line)
    $csv[] = str_getcsv($line);
  return $csv;
}

function write_csv($filename, $data) {
  foreach ($data as $line) {
    $contents[] = implode(",", $line);
  }
  file_put_contents($filename, implode("\n", $contents));
}


// =============================================================================
// SUBMISSION: Appending a new attendee to a date
// =============================================================================
if (isset($_POST['available'])) {
  $csv = read_csv($csvfile);                // Load the CSV file
  foreach ($_POST as $k => $v) {            // Loop through the post vars
    if ($v == "#avail") {                   // $k will be the the date
      foreach ($csv as $index => $line) {   // Loop through the lines of CSV
        if ($line[0] == $k) {               // Only get the selected date line
          $csv[$index][] = $_POST['name'];  // Append the name to that line
        }
      }
    }
  }
  write_csv($csvfile, $csv);                // Save it!
  $notice = "Saved!";                       // Tell the user
}


// =============================================================================
// SUBMISSION: Deleting an attendee from a date
// =============================================================================
elseif (isset($_POST['delete'])) {
  $csv = read_csv($csvfile);                // Load the CSV file
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
  write_csv($csvfile, $csv);                // Save it!
  $notice = "Deleted!";                     // Tell the user
}


// =============================================================================
// Generate the HTML table from CSV
// =============================================================================
function generateTable($filename) {

  // If a file exists, then the booking system is open!
  if (($handle = fopen($filename, "r")) !== FALSE) {

    // Print the table header
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Date</th>';
    echo '<th>Your<br/>availability</th>';
    echo '<th>Total<br/>attendees</th>';
    echo '<th>Attendees</th>';
    echo '</tr>';

    // Open the CSV file and iterate over the rows
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      // Process row (the event date and time)
      $timestamp_raw = $data[0];
      $timestamp_str = strftime(
        "%A<br />%d-%h %Y<br />%H:%M %p",
        strtotime($data[0])
      );
      echo "<tr id='$timestamp_raw'>";
      echo "<th>$timestamp_str</th>";

      // Insert checkbox to indicate availability
      echo "<td>";
      echo "<input
        type='checkbox'
        name='$timestamp_raw'
        value='#avail'/>";
      echo "</td>";

      // Insert the total attendees column
      $num = count($data);
      echo "<td>" . ($num-1) . "</td>";

      // Insert each attendee with a delete checkbox
      echo "<td>";
      $attendees = array_splice($data, 1);
      foreach ($attendees as $attendee) {
        echo "<input
          type='checkbox'
          name='$timestamp_raw#$attendee'
          value='#delete'/>";
       echo "$attendee<br />";
      }
      echo "</td>";

      echo "</td></tr>";
    }

    fclose($handle);
    echo "</table>";
    echo "<input type='submit' name='available' value='Submit availability'/>";
    echo "<input type='submit' name='delete' value='Delete selected attendees'/>";

  } else {
    echo "<div>The booking system is closed!</div>";
  }
}

?>

<html>

  <head>
    <title>Dinner booking</title>
  </head>

  <body>
    <h1>Dinner booking</h1>
    <hr />
    <div class="notice">
      <?php echo $notice; ?>
    </div>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">

      <label for="name">
        Your name:
        <input name="name"/>
      </label>

      <?php generateTable($csvfile); ?>

    </form>
  </body>
</html>
