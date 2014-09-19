<?php include("club-results-of-id.php"); ?>
<?php
$id = $_GET["id"];
$date = $_GET["meetingdate"];

$members_tb = "members";
$results_tb = "results";

print_game_results($con, $members_tb, $results_tb, $id, $date);

?>
