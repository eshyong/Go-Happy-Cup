<?php include("club-print-active-games.php"); ?>
<?php
require_once('config.php');
$id=$_GET["id"];
$white_win=$_GET["white_win"];

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

//echo "white=$white<br>black=$black<br>handicaps=$handicaps<br>komi=$komi<br>\n";

//* remove this game from the results table
$sql="UPDATE results SET white_win='$white_win', active=0 WHERE id=$id LIMIT 1";

if (!mysql_query($sql)) {
	echo "Insertion failed: " . mysql_error() . "<br>\n";
	echo $sql;
}
// */

print_active_games($con);

mysql_close($con);
?>
