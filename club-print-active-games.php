<?php include("club-results-of-id.php"); ?>
<?php
require('config.php');
function print_active_games($con) {
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

	$result = mysql_query("SELECT
 w_members.name as w_name,
 w_members.rank as w_rank,
 b_members.name as b_name,
 b_members.rank as b_rank,
 results.id as id,
 results.board_size as board_size,
 results.handicaps as handicaps,
 results.komi as komi
 FROM results, members as w_members, members as b_members
 WHERE active = 1 AND w_members.id = white AND b_members.id = black");
	$num_games = mysql_num_rows($result);

	if ($num_games == 0) {
		echo "<h3>There is no active game.</h3>\n";
	} else if ($num_games == 1) {
		echo "<h3>There is $num_games active game:</h3>\n";
	} else {
		echo "<h3>There are $num_games active games:</h3>\n";
	}

	if ($num_games > 0) {
		// table ordered by name
		echo "<table border=1>
<tr><td><b>Id</b></td><td><b>White<b></td><td><b>Black</b></td>
<td><b>Board Size</b></td><td><b>Handicap</b></td>
<td><b>Komi</b></td>
<td><b>Winner</b></td><td><b>Action</b></td>
</tr>";

		date_default_timezone_set('America/Los_Angeles');

		while($row = mysql_fetch_array($result)) {
			echo "<tr>\n";

			echo "<td align=center>".$row['id']."</td>\n";
			echo "<td>".$row['w_name']."(".rank_str($row['w_rank']).")</td>\n";
			echo "<td>".$row['b_name']."(".rank_str($row['b_rank']).")</td>\n";
			echo "<td>".$row['board_size']."x".$row['board_size']."</td>\n";
			echo "<td align=center>".$row['handicaps']."</td>\n";
			echo "<td align=center>".$row['komi']."</td>\n";
			echo "<td><select id=winner_".$row['id']." size=1 onload=\"clearWinner(this)\">
		<option value=-1></option>
		<option value=1>White</option>
		<option value=0>Black</option></td>\n";
			echo "<td><button type=button onclick=recordResult(".$row['id'].")>Record Result</button>
<button type=button onclick=cancelGame(".$row['id'].")>Cancel Game</button></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

	echo "<h3>Recent game results:</h3>\n";

	print_game_results($con, "members", "results", -1, 'all', 4/*limit*/,
					   0/*count_names*/);

	if ($need_close_con) {
		mysql_close($con);
	}
}
?>
