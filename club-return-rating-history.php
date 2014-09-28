<?php include("common-func.php"); ?>
<?php
require_once('config.php');
function print_rating_history($con, $members_tb, $ratings_tb, $id, $date,
							$limit = -1, $count_games = 1) {
	if (is_null($con)) {
		$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
		if (!$con) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db(MYSQL_DB, $con);
		$need_close_con = 1;
	} else {
		$need_close_con = 0;
	}

	$sql = "SELECT members.name, ratings.date, ratings.rank
 FROM $ratings_tb as ratings, $members_tb as members
 WHERE ($id = -1 or ratings.id = $id) and ratings.id = members.id
 ORDER by members.name ASC, ratings.date ASC";
	$results = mysql_query($sql);

	echo "<table border=1><tr>
<td align=center><b>Name</b></td>
<td align=center><b>Date</b></td>
<td align=center><b>Rating</b></td></tr>\n";

	while($row = mysql_fetch_array($results)) {
		echo "<tr>";
		echo "  <td align=center>", $row['name'], "</td>\n";
		echo "  <td align=center>", $row['date'], "</td>\n";
		echo "  <td align=center>", rank_str($row['rank']), "</td>\n";
		echo "</tr>\n";
	}

	echo "</table>\n";

	if ($need_close_con) {
		mysql_close($con);
	}
}

$id = $_GET["id"];

$members_tb = "members";
$ratings_tb = "ratings";

print_rating_history(null, $members_tb, $ratings_tb, $id, $date);
?>
