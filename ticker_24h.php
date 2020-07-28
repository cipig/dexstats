<?php
header('Content-Type: application/json');

$hostname = "127.0.0.1";
$username = "swaps_ro";
$password = "xxxx";
$db = "swaps";
$markets = array();
$tickers = array();

$dbconnect=mysqli_connect($hostname,$username,$password,$db);
if ($dbconnect->connect_error) {
  die("Database connection failed: " . $dbconnect->connect_error);
}

if(!isset($_GET['market'])) {
  $query = mysqli_query($dbconnect, "SELECT DISTINCT taker_coin,maker_coin FROM swaps WHERE started_at >= now() - INTERVAL 1 DAY") or die (mysqli_error($dbconnect));
  while ($row = mysqli_fetch_assoc($query)) {
    if ($row['maker_coin'] == 'BTC') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'BTC') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    elseif ($row['maker_coin'] == 'KMD') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'KMD') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    elseif ($row['maker_coin'] == 'BCH') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'BCH') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    elseif ($row['maker_coin'] == 'LTC') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'LTC') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    elseif ($row['maker_coin'] == 'ETH') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'ETH') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    elseif ($row['maker_coin'] == 'MORTY') { if (!in_array($row['taker_coin'].'-'.$row['maker_coin'],$markets)) {$markets[] = $row['taker_coin'].'-'.$row['maker_coin'];} }
    elseif ($row['taker_coin'] == 'MORTY') { if (!in_array($row['maker_coin'].'-'.$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
    else { if (!in_array($row['maker_coin'].$row['taker_coin'],$markets)) {$markets[] = $row['maker_coin'].'-'.$row['taker_coin'];} }
  }
} else {
  $markets[] = $_GET['market'];
}

foreach($markets as $market) {
  $symbols = explode("-", $market);
  $query = mysqli_query($dbconnect, "(SELECT started_at,maker_amount AS quoteVolume,taker_amount AS volume,(maker_amount / taker_amount) AS price FROM swaps WHERE taker_coin = '$symbols[0]' AND maker_coin = '$symbols[1]' AND started_at >= now() - INTERVAL 1 DAY) UNION (SELECT started_at,taker_amount AS quoteVolume,maker_amount AS volume,(taker_amount / maker_amount) AS price FROM swaps WHERE taker_coin = '$symbols[1]' AND maker_coin = '$symbols[0]' AND started_at >= now() - INTERVAL 1 DAY) ORDER BY started_at") or die (mysqli_error($dbconnect));

  $count=0; $volume=0; $quoteVolume=0; $weightedPrice=0;
  while ($row = mysqli_fetch_assoc($query)) {
    $count += 1;
    $quoteVolume += $row['quoteVolume'];
    $volume += $row['volume'];
    $lastPrice = sprintf("%.8f",$row['price']);
    $weightedPrice += $lastPrice*$row['volume'];
    $lastTime = $row['started_at'];
    $lastQty = $row['volume'];
  }
  if ($count>0) {$tickers[] = array('market' => $market, 'lastPrice' => $lastPrice, 'lastTime' => $lastTime, 'lastQty' => $lastQty, 'weightedAvgPrice' => sprintf("%.8f",$weightedPrice/$volume), 'volume' => sprintf("%.8f",$volume), 'quoteVolume' => sprintf("%.8f",$quoteVolume), 'count' => $count);}
}

echo json_encode($tickers);

?>
