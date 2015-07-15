<?php
// contains utility functions mb_stripos_all() and apply_highlight()
//require_once 'local_utils.php';
 
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - not an AJAX request...';
  trigger_error($user_error, E_USER_ERROR);
}
 
// get what user typed in autocomplete input
$term = trim($_GET['term']);
 
$a_json = array();
$a_json_row = array();
 
$a_json_invalid = array(array("id" => "#", "value" => $term, "label" => "Only letters and digits are permitted..."));
$json_invalid = json_encode($a_json_invalid);
 
// replace multiple spaces with one
$term = preg_replace('/\s+/', ' ', $term);
 
// SECURITY HOLE ***************************************************************
// allow space, any unicode letter and digit, underscore and dash
if(preg_match("/[^\040\pL\pN_-]/u", $term)) {
  print $json_invalid;
  exit;
}
// *****************************************************************************
 
// database connection
$conn = "host=localhost port=5432 dbname=Wikidata user=postgres password=postgres";
$dbconn = pg_connect($conn) or die("Could not connect");
 
$parts = explode(' ', $term);
$p = count($parts);
 
/**
 * Create SQL
 */
$sql = "SELECT id, name FROM nodes WHERE lower(name) LIKE lower('%" . $parts[0] ."%')";
for($i = 1; $i < $p; $i++) {
  $sql .= " AND name LIKE lower('%" . $parts[$i] . "%')";
}

$rs = pg_query($dbconn, $sql);
if($rs === false) {
  $user_error = 'Wrong SQL: ' . $sql . 'Error: ' . $dbconn->errno . ' ' . $dbconn->error;
  trigger_error($user_error, E_USER_ERROR);
}
 
while($row = pg_fetch_assoc($rs)) {
  $a_json_row["id"] = $row['name'];
  $a_json_row["item"] = $row['name'];
  $a_json_row["label"] = $row['name'];
  array_push($a_json, $a_json_row);
}
 
// highlight search results
//$a_json = apply_highlight($a_json, $parts);
 
$json = json_encode($a_json);
print $json;
?>