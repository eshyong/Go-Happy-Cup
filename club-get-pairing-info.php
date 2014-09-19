<?php include("common-func.php"); ?>
<?php
require('config.php');
$id=$_GET["id"];

#echo "id=$id<br>\n";
if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}
$today_date = strftime("%Y-%m-%d", strtotime("today"));

$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

# find the info of this player
$sql = "SELECT name, rank FROM members WHERE id = $id LIMIT 1";
$myrank_results = mysql_query($sql);
if (!($row = mysql_fetch_array($myrank_results))) {
	die("Unknown member id $id received");
}
$myid = $id;
$myname = $row['name'];
$myrank = $row['rank'];

// find all potential opponents who are here tonight and are not in a game
// order by the absolute value of their raitings difference
// if somebody has played games with $myid, their rank is adjusted
$sql = "SELECT DISTINCT id, name, rank, ABS(rank - ($myrank)) as rank_diff
FROM members
WHERE id <> $myid AND regdate >= \"$today_date\" AND
 NOT id IN (SELECT white FROM results where active = 1 AND date=\"$today_date\"
 UNION  SELECT black FROM results where active = 1 AND date=\"$today_date\")
 ORDER BY rank_diff ASC LIMIT 40";

// echo "$sql<br>\n"; // for debugging

if (!$opponents = mysql_query($sql)) {
	die("This sql failed:<br>\n$sql<br>\n");
}

echo "<table border=1>\n";
echo "<tr><td align=center><b>White</b></td>
<td align=center><b>Black</b></td>
<td align=center><b>Board Size</b></td>
<td align=center><b>Handicaps</b></td>
<td align=center><b>Komi</b></td>";
echo "<td width=120><b>Already played with each other?</b></td>";
echo "<td align=center colspan=2><b>Action</b></td></tr>\n";

/* it is a good choice if the two players didn't play with each other tonight */
$num_good_choices = 0;

while ($row = mysql_fetch_array($opponents)) {
	if ($row['rank'] > $myrank) {
		$white_id = $row['id'];
		$white_name = $row['name'];
		$white_rank = $row['rank'];
		$black_id = $myid;
		$black_name = $myname;
		$black_rank = $myrank;
	} else {
		$black_id = $row['id'];
		$black_name = $row['name'];
		$black_rank = $row['rank'];
		$white_id = $myid;
		$white_name = $myname;
		$white_rank = $myrank;
	}
	$game_candidate_id = $row['id'];
	if ($white_rank * $black_rank < 0) {
		$rank_diff = $row['rank_diff'] - 1;
	} else {
		$rank_diff = $row['rank_diff'];
	}
	$rank_diff = intval($rank_diff + 0.5);
	if ($rank_diff == 0) {
		$komi = "7.5";
	} else {
		$komi = "0.5";
	}
	$sql_games_played = "SELECT COUNT(*) FROM results 
        WHERE white=$white_id AND black=$black_id AND date=\"$today_date\"
       AND active=0";
	$result_games_played = mysql_query($sql_games_played);
	$row_games_played = mysql_fetch_array($result_games_played);

	/*
	if ($row_games_played[0] > 0) {
		// these two players have played tonight, do not pair them
		continue;
	}
	// */
	
	echo "<tr>\n";
	echo "<td>" . $white_name . "(" . rank_str($white_rank) . ")</td>";
	echo "<td>" . $black_name . "(" . rank_str($black_rank) . ")</td>";
	echo "<td><select id=boardsize_" . $game_candidate_id . " size=1 onChange=\"changeBoardSize(this, ". $game_candidate_id . ")\">
        <option value=19>19x19</option>
		<option value=13>13x13</option></select></td>";
	echo "<td align=center>" .
		"<input type=textbox id=handicaps_" . $game_candidate_id . " value=" . $rank_diff . " size=3>" .
		"</td>";
	echo "<td align=center>" .
		"<input type=textbox id=komi_" . $game_candidate_id . " value=" . $komi . " size=5>" .
		"</td>";
	//*
	echo "<td align=center>";
	if ($row_games_played[0] > 0) {
		echo "<font color=red>Yes</font>";
	} else {
		echo "No";
	}
	echo "</td>\n";
	// */
	echo "<td><button type=button id=btn_" . $game_candidate_id . " 
                 onclick=\"startGame(".$game_candidate_id.",".$white_id.",".$black_id.", 0)\">
                 Start Game</button></td>\n";
	echo "<td><button type=button id=btn_" . $game_candidate_id . " 
                 onclick=\"startGame(".$game_candidate_id.",".$white_id.",".$black_id.", 1)\">
                 Switch Color &amp; Start Game</button></td>\n";
	echo "</tr>\n";

	$num_good_choices++;
	if ($num_good_choices >= 10) {
		break;
	}
}
echo "</table>\n";

mysql_close($con);
?>
