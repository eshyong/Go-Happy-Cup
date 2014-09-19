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
?>
