<?php
require_once('config.php');
$members_tb = "members";
$results_tb = "results";

$id=$_GET["id"];
$checkin=$_GET["checkin"];
$regdate=$_GET["regdate"];

//echo "id=$id<br>\ncheckin=$checkin<br>\nregdate=$regdate<br>\n";

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db(MYSQL_DB, $con);

$sql="UPDATE $members_tb SET `regdate` = ";
if ($checkin == 0) {
	$sql = $sql . "NULL";
} else {
	$sql = $sql . '"' . $regdate . '"';
}
$sql = $sql . " WHERE `id` = $id LIMIT 1";

//echo "$sql<br>\n"; // for debugging

if (mysql_query($sql)) {
	//echo "Update succeed<br>\n";
} else {
	echo "Update failed, you've found a bug, please tell the administrator!<br>\n";
	echo $sql;
}

mysql_close($con);
?>
