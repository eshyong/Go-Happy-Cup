<?php include("common-func.php"); ?>
<?php
require ('config.php');
# find all registered players who are not in a game
# return the result as a JSON array
if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}
$today_date = strftime("%Y-%m-%d", strtotime("today"));

$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

#
# find all registered players who are not in a game
#
$sql = "SELECT id, name FROM members
            WHERE members.regdate >= \"$today_date\" AND
            NOT id IN (SELECT white FROM results
                       WHERE active = 1 AND date=\"$today_date\"
                       UNION
                       SELECT black FROM results
                       WHERE active = 1 AND date=\"$today_date\")
            ORDER BY name ASC";

//echo $sql;
$available_players = mysql_query($sql);

$i = 0;
echo "{\"all\": [";
echo "{\"value\":\"-1\", \"display\":\"Choose A Name\"}\n";
while ($row = mysql_fetch_array($available_players)) {
	$name = $row['name'];
	$id = $row['id'];
	echo ",";
	echo "{\"value\":\"$id\", \"display\":\"$name\"}\n";
	$i += 1;
}
echo "]}\n";

mysql_close($con);
?>
