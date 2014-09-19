<?php
function recent_results($members_tb, $results_tb) {
	echo "<table border=1>
<tr><td><b>White</b></td><td><b>Black</b></td>
<td><b>Board</b></td>
<td><b>Handicaps</b></td>
<td><b>Komi</b></td>
<td><b>Winner</b></td>
<td><b>Date</b></td>
</tr>\n";

	$results = mysql_query("SELECT w_members.name as w_name,
 b_members.name as b_name, results.* 
FROM $results_tb as results, $members_tb as w_members,
 $members_tb as b_members
WHERE results.white=w_members.id and results.black=b_members.id
ORDER BY results.id DESC");

	$n = 0;
	while($row = mysql_fetch_array($results)) {
		echo "\t<tr>\n";
		echo "\t<td>" . $row['w_name'] . "</td>\n";
		echo "\t<td>" . $row['b_name'] . "</td>\n";
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
		$n++;
		if ($n >= 4) {
			break;
		}
	}
	echo "</table>\n";
}
?>
