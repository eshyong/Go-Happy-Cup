<?php
require('config.php');
  // calculate club ratings using a python CGI.  Here is how this works:
  // 1. generate players file and results file
  // 2. call python CGI to generate ratings file
  // 3. read ratings file and update database
function get_rating($up_to_date) {
	echo "<p>get rating for $up_to_date</p>";
	/* configurations */
	$members_tb = "members";
	$results_tb = "results";
	$ratings_tb = "ratings";
	$players_file_name = "/tmp/gohappycup.com-players.txt";
	$results_file_name = "/tmp/gohappycup.com-results.txt";
	$ratings_file_name = "/tmp/gohappycup.com-ratings.txt";

	/* connect to database */
	$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db(MYSQL_DB, $con);

	/* read the ratings result file and update the database */
	echo "<br>$ratings_file_name<br>\n";
	$fh = fopen($ratings_file_name, 'r');
	if ($fh) {
		$n = 0;
		$num_updated = 0;
		while (!feof($fh)) {
			$line = fgets($fh, 128);
			if (!preg_match('/(\S+) (\S+)/', $line, $matches))
				break;
			$id = $matches[1];
			$rank = $matches[2];
			if ($rank < 10 && $rank > -50) {
				// we have valid rating
				echo $line, "<br>\n";

				// update the ratings in members table
				$sql = "UPDATE $members_tb SET `rank` =  '" . $rank . "'" .
					" WHERE  `id` = $id LIMIT 1 ;";
				$results = mysql_query($sql);
				if (! $results) {
					echo "Update failed on sql:<br>\n$sql<br>\n";
				}

				// update the ratings in ratings table
				$sql = "DELETE FROM $ratings_tb WHERE `id` = $id and " .
					" `date` = '" . $up_to_date . "' LIMIT 1";
				$results = mysql_query($sql);
				#echo "$sql<br>\n";

				$sql = "INSERT INTO  $ratings_tb" .
					" (`id`, `date`, `is_anchor`, `rank`)" .
					" VALUES ('". $id ."'," .
					"'". $up_to_date ."'," .
					"'". 0 ."', " .
					"'". $rank ."')";
				$results = mysql_query($sql);
				#echo "$sql<br>\n";

				$num_updated++;
			}
			$n++;
			if ($n > 5000) // avoid deadloop
				break;
		}
		fclose($fh);
	} else {
		echo "Cannot find $ratings_file_name<br>\n";
	}
	echo "Updated ratings for $num_updated players<br>\n";

	mysql_close($con);
}

$up_to_date=$_GET["date"];

if ($up_to_date) {
	get_rating($up_to_date);
} else {
	//get_rating('2010-03-27');
	//get_rating('2010-04-03');
	get_rating('2010-04-10');
}
?>
