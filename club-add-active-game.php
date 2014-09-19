<?php include("club-print-active-games.php"); ?>
<?php
require ('config.php');
$white=$_GET["white_id"];
$black=$_GET["black_id"];
$boardsize=$_GET["boardsize"];
$handicaps=$_GET["handicaps"];
$komi=$_GET["komi"];

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}
$today_date = strftime("%Y-%m-%d", strtotime("today"));

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

//echo "white=$white<br>black=$black<br>handicaps=$handicaps<br>komi=$komi<br>\n";

//* add this game into the results table
$sql="INSERT INTO results
 (white, black, board_size, handicaps, komi, active, date)
VALUES
 ('$white', '$black', '$boardsize', '$handicaps', '$komi', '1', '$today_date')";

if (!mysql_query($sql)) {
	echo "Insertion failed: " . mysql_error() . "<br>\n";
	echo $sql;
}
// */

print_active_games($con);

mysql_close($con);
?>
