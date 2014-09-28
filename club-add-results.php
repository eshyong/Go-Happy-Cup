<?php include('club-results-of-id.php'); ?>
<?php
require_once('config.php');
$members_tb = "members";
$results_tb = "results";

$white=$_GET["white"];
$black=$_GET["black"];
$boardsize=$_GET["boardsize"];
$handicaps=$_GET["handicaps"];
$komi=$_GET["komi"];
$winner=$_GET["winner"];
$date=$_GET["date"];

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db(MYSQL_DB, $con);

/*
echo "$white<br>\n";
echo "$black<br>\n";
echo "$boardsize<br>\n";
echo "$handicaps<br>\n";
echo "$komi<br>\n";
echo "$winner<br>\n";
echo "$date<br>\n";
// */

$sql="INSERT INTO $results_tb
 (white, black, board_size, handicaps, komi, white_win, date)
VALUES
 ('$white', '$black', '$boardsize', '$handicaps', '$komi', '$winner', '$date')";

// echo "$sql<br>\n"; // for debugging

if (mysql_query($sql)) {
	print_game_results($con, $members_tb, $results_tb, -1, 'all', 6/*limit*/,
					   0/*count_names*/);
} else {
	echo "Insertion failed, please try again!<br>\n";
	echo $sql;
}

mysql_close($con);
?>
