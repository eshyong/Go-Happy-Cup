<?php

function main() {
	$from_date = $_GET["from_date"];
	$to_date = $_GET["to_date"];
	date_default_timezone_set('America/Los_Angeles');
	$today = date("Y-m-d", time());
	while ($from_date < $today && $from_date < $to_date) {
		$from_date = date('Y-m-d', strtotime(date("Y-m-d", strtotime($from_date)) . " +7 day"));
		echo '<h2>Calc ratings for ' . $from_date . '</h2>';
		flush();
		if (gethostname() == "wwang4.local") {
			$name = MYSQL_HOST;
		} else {
			$name = "www.gohappycup.com";
		}
		$url = "http://" . $name;
		$calc_ratings = file_get_contents($url . '/club-ratings.php?date=' . $from_date);
		echo $calc_ratings;
	}
	echo '<h1>Done!</h1>';
}

main();

?>
