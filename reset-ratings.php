<?php
require ('config.php');
function update_one($id, $rank) {
	$changed = 0;
	$sql = "select id, rank from ratings where id=$id order by date desc";
	//echo "$sql<br>\n";
	$results = mysql_query($sql);
	if ($row = mysql_fetch_array($results)) {
		echo $id . ' ' . $row['rank'] . "<br>\n";
		if ($rank != $row['rank']) {
			$sql = "UPDATE members SET `rank` =  '" . $row['rank'] . "'" .
				" WHERE  `id` = $id LIMIT 1 ;";
			//echo "$sql<br>\n";
			$results = mysql_query($sql);
			echo "Changed rank from $rank to " . $row['rank'] . "<br>\n";
			$changed = 1;
		} else {
			echo "Skipped $id<br>\n";
		}
	} else {
		echo "Not found $id<br>\n";
	}
	return $changed;
}

// update every rating in the members table to be the latest rating in the ratings table
function main() {
	/* connect to database */
	$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db(MYSQL_DB, $con);

	//*
	$sql = "select id, name, rank from members";
	$results = mysql_query($sql);
	while($row = mysql_fetch_array($results)) {
		echo $row['id'] . ' ' . $row['name'] . ' ' . $row['rank'] . "<br>\n";
		update_one($row['id'], $row['rank']);
	}
	//*/

	mysql_close($con);
}

main();

?>
