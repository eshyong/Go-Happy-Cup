<?php
function rank_str($rank) {
	if ($rank > 0) {
		return $rank . 'd';
	} else if ($rank == -99) {
		return '?';
	} else {
		$kyu = -$rank;
		return floatval($kyu) . 'k';
	}
}

function emit_member_table($total_num, $num_cols, $name, $display, $query) {
	// Calculate number of rows to emit.
	$num_rows = $total_num / $num_cols;

	// Emit the outer table.
	echo "<table id=\"$name\" style=\"display:$display\"><tr>";
	for ($col = 0; $col < $num_cols; $col++) {
		echo "<td valign=top>\n"
		   . "<table border=1>"
		   . "<tr><td>Name (Rating)</td>"
		   . "<td width=30 align=center>I Am Here</td></tr>";

		for ($row = 0; $row < $num_rows; $row++) {
			// Emit tables for the all member table
			$result = mysql_fetch_array($query);
			if ($result) {
				echo "<tr><td>"
				   . $result['name']
				   . "(" . rank_str($result['rank']) . ")"
				   . "<td align=center><input type=checkbox" . " onclick=\"reg_member(this, " . $result['id'] . ")\"></td>"
				   . "</tr>\n";
			}
		}
		echo "</table>\n" 
		   . "</td>\n";
	}
	echo "</tr></table>\n";
}
?>
