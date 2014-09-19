<?php include("common-func.php"); ?>
<?php
require('config.php');
function print_game_results($con, $members_tb, $results_tb, $id, $date,
							$limit = -1, $count_games = 1) {
	if (is_null($con)) {
		$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
		if (!$con) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db(MYSQL_DB, $con);
		$need_close_con = 1;
	} else {
		$need_close_con = 0;
	}

	/* count unique players */
	if ($count_games) {
		$sql = "SELECT black, white 
FROM $results_tb as results
WHERE ($id=-1 or black=$id or white=$id) and
('$date'='all' or results.date='$date')";
		$results = mysql_query($sql);
		$players_ar = array();

		if ($results) {
			while($row = mysql_fetch_array($results)) {
				$players_ar[] = $row['black'];
				$players_ar[] = $row['white'];
			}
			sort($players_ar);
			$final_players = array_unique($players_ar);
			$uniq_players = count($final_players);
		} else {
			$uniq_players = 0;
		}
	}

	if ($limit == -1) {
		$limit = 1000; // now limit
	}

	$sql = "SELECT w_members.name as w_name,
 w_members.rank as w_rank, 
 b_members.name as b_name,
 b_members.rank as b_rank, 
 results.* 
FROM $results_tb as results, $members_tb as w_members,
 $members_tb as b_members
WHERE results.white=w_members.id and results.black=b_members.id and
 results.active=0 and
($id=-1 or w_members.id=$id or b_members.id=$id) and
('$date'='all' or results.date='$date')
ORDER BY results.date DESC, results.id DESC LIMIT $limit";
	//echo $sql;

	$results = mysql_query($sql);

	if ($count_games) {
		$count = mysql_num_rows($results);

		//echo "<p>Debug: $members_tb $results_tb $id  $date</p>\n";
		echo "<p>Total games: $count &nbsp; &nbsp; &nbsp; &nbsp;\n" .
			"Unique players: $uniq_players</p>\n";
	}

	echo "<table border=1>
<tr><td><b>Id</b></td><td><b>White</b></td><td><b>Black</b></td><td><b>Board</b></td>
<td><b>Handicaps</b></td>
<td><b>Komi</b></td><td><b>Winner</b></td><td><b>Date</b></td></tr>\n";

	while($row = mysql_fetch_array($results)) {
		echo "\t<tr>\n";
		echo "\t<td align=center>" . $row['id'] . "</td>\n";
		echo "\t<td>" . $row['w_name'] . "(" . rank_str($row['w_rank']) . ")</td>\n";
		echo "\t<td>" . $row['b_name'] . "(". rank_str($row['b_rank']) . ")</td>\n";
		echo "\t<td>" . $row['board_size'] . "x" . $row['board_size'] . "</td>\n";
		echo "\t<td align=center>" . $row['handicaps'] . "</td>\n";
		echo "\t<td align=center>" . $row['komi'] . "</td>\n";
		if ($row['white_win']) {
			echo "\t<td>" . $row['w_name'] . "</td>\n";
		} else {
			echo "\t<td>" . $row['b_name'] . "</td>\n";
		}
		echo "\t<td align=center>" . $row['date'] . "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

	if ($need_close_con) {
		mysql_close($con);
	}
}
?>
