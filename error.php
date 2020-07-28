<?php
header('Content-Type: application/json');

$hostname = "127.0.0.1";
$username = "swaps_ro";
$password = "xxxx";
$db = "swaps";
$uuid = $_GET['uuid'];

$dbconnect=mysqli_connect($hostname,$username,$password,$db);
if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
}

$query = mysqli_query($dbconnect, "SELECT maker_coin,maker_error_type,maker_error_msg,maker_gui,maker_version,taker_coin,taker_error_type,taker_error_msg,taker_gui,taker_version FROM swaps_failed WHERE uuid='$uuid'") or die (mysqli_error($dbconnect));
$row = mysqli_fetch_assoc($query);
echo json_encode($row);

?>
