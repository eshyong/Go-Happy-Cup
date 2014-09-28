<?php
require_once('config.php');
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

	/* generate players file */
	/* players file format: id rank is_anchor "name" */
	$sql = "select id, rank, is_anchor, name from $members_tb";
	$results = mysql_query($sql);

	//echo "$players_file_name<br>\n";
	$fh = fopen($players_file_name, 'w');
	while($row = mysql_fetch_array($results)) {
		if (strstr($row['name'], '"', true)) {
			echo $row['name'] . "has bad character!<br>\n";
		}
		fwrite($fh, $row['id'] . ' ' . $row['rank'] . ' ' .
			   $row['is_anchor'] . ' "' . $row['name'] . "\"\n");
		//echo $row['id'] . ' ' . $row['rank'] . ' ' .
		//	$row['is_anchor'] . "<br>\n";
	}
	fclose($fh);
	echo "$players_file_name is created<br>\n";

	/* generate results file.
	   format: YYYY-MM-DD white-id black-id handicaps komi white_win */

	$from_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($up_to_date)) . " -180 day"));
	//$from_date = "2010-03-27";
	$sql = "select date, white, black, handicaps, komi, white_win,
            board_size, id from $results_tb where date >= '" . $from_date .
		"' and date <= '" . $up_to_date . "' order by date";

	echo $sql, "<br>\n";
	$results = mysql_query($sql);
	if (!$results) {
		echo "$sql<br>\n";
	}
	$fh = fopen($results_file_name, 'w');
	$num_results = 0;
	while ($row = mysql_fetch_array($results)) {
		$handicaps = $row['handicaps'];
		$komi = $row['komi'];
		if ($row['board_size'] != 19) {
			/* convert 13x13 handicap/komi to 19x19 */
			switch ($handicaps) {
			case 0:
				$komi = $komi + 2;
				break;
			case 2:
			case 3:
			case 4:
			case 5:
				$handicaps = $handicaps * 2 - 1;
				break;
			case 6:
				$handicaps = 9;
				$komi = -19.5;
			}
		}
		/* if there are too many handicaps, ignore the game */
		if ($handicaps <= 13) {
			++$num_results;
			fwrite($fh, $row['date'] . ' ' .
				   $row['white'] . ' '. $row['black'] . ' '.
				   $handicaps . ' '. $komi . ' '.
				   $row['white_win'] . "\n");
		} else {
			echo "Ignored result " . $row['id'] . "<br>\n";
		}
	}
	fclose($fh);
	echo "$results_file_name is created ($num_results games)<br>\n";

	if (gethostname() == "wwang4.local") {
		$name = MYSQL_HOST;
	} else {
		$name = "www.gohappycup.com";
	}
	$url = "http://" . $name;
	echo "\"" . $url . "\"<br>\n";

	echo "Now running rate.cgi to calculate ratings.
		please wait for about a minute.<br>\n";
	flush();
	mysql_close($con);

	$rate_out = file_get_contents($url . '/cgi-bin/rate.cgi');
	echo $rate_out;
	flush();

	$update_ratings = file_get_contents($url . '/club-ratings-update-ratings.php?date=' . $up_to_date);
	echo $update_ratings;
	flush();
}

$up_to_date=$_GET["date"];

/* show the link for the next week's update */
date_default_timezone_set('America/Los_Angeles');
$next_week = date('Y-m-d', strtotime(date("Y-m-d", strtotime($up_to_date)) . " +7 day"));
echo "<br><a href=\"/club-ratings.php?date=" . $next_week . "\">Next Week (" . $next_week . ")</a>\n";

if ($up_to_date) {
	get_rating($up_to_date);
} else {
	//get_rating('2010-03-27');
	//get_rating('2010-04-03');
	get_rating('2010-04-10');
}

?>
