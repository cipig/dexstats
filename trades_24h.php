<?php
header('Content-Type: application/json');

$hostname = "127.0.0.1";
$username = "swaps_ro";
$password = "xxxx";
$db = "swaps";

$dbconnect=mysqli_connect($hostname,$username,$password,$db);
if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
}

if(isset($_GET['market'])) {
  $symbols = explode("-", $_GET['market']);
  $query = mysqli_query($dbconnect, "(SELECT uuid,started_at AS time,taker_amount AS qty,maker_amount AS quoteQty,(maker_amount / taker_amount) AS price FROM swaps WHERE taker_coin = '$symbols[0]' AND maker_coin = '$symbols[1]' AND started_at >= now() - INTERVAL 1 DAY) UNION (SELECT uuid,started_at AS time,maker_amount AS qty,taker_amount AS quoteQty,(taker_amount / maker_amount) AS price FROM swaps WHERE taker_coin = '$symbols[1]' AND maker_coin = '$symbols[0]' AND started_at >= now() - INTERVAL 1 DAY) ORDER BY time")
   or die (mysqli_error($dbconnect));
  $swaps = array();
  while ($row = mysqli_fetch_assoc($query)) {
    $swaps[] = $row;
  }
}

echo json_encode($swaps);
?>
