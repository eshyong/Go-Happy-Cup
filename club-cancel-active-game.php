<?php include("club-print-active-games.php"); ?>
<?php
require('config.php');
$id=$_GET["id"];

$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

//echo "white=$white<br>black=$black<br>handicaps=$handicaps<br>komi=$komi<br>\n";

//* remove this game from the results table
$sql="DELETE FROM results WHERE id = $id";

if (!mysql_query($sql)) {
	echo "Insertion failed: " . mysql_error() . "<br>\n";
	echo $sql;
}
// */

print_active_games($con);

mysql_close($con);
?>
