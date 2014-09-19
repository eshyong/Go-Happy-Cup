<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
<HTML VERSION="-//IETF//DTD HTML 3.2//EN">
  <head>
	 <?php include("simpletree-headerpart.shtml"); ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
<script type="text/javascript" src="get-http-obj.js"></script>
<script type="text/javascript" src="club-add-results.js"></script>
<script language="JavaScript">
function setStartTimeNow() {
	var d = new Date();
	var currYear = d.getFullYear();
	var currMonth = d.getMonth() + 1;
	var currDate = d.getDate();
	var currHour = d.getHours();
	var currMin = d.getMinutes();
	time = currYear + "-" +
	(currMonth < 10 ? "0" : "") + currMonth + "-" +
	(currDate < 10 ? "0" : "") + currDate

	theInput = club_results.date;
	theInput.value = time;
}
</script>
  </head>
  <body onload="setStartTimeNow()">
    <table width="100%">
      <tr>
	 	<?php include("header.shtml"); ?>
      </tr>
    </table>

    <table border="0" width="100%" cellspacing=1 cellpadding=12>
      <tr>
	 <?php include("left-menu-simpletree.shtml"); ?>
	 <td valign="top" width="99%">
	 <?php include('club-results-of-id.php'); ?>
	 <?php
require ('config.php');
	 $con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
$members_tb = "members";
$results_tb = "results";
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

echo "<h3>Add Game Result</h3>
<form name=\"club_results\" action=\"2010-register.php\" method=\"post\">
<table>
  <tr>
	<td align=right>
	  White Player:
	</td>
	<td><select id=white size=1>
		<option value=\"#\">This is a Place Holder</option></select>
	</td>
  </tr>
  <tr>
	<td align=right>
	  Black Player:
	</td>
	<td>
	  <select id=black size=\"1\">
		<option value=\"#\">This is a Place Holder</option>
	  </select>
  </tr>
  <tr>
	<td align=right>
	  Board size:</td>
	<td> <select id=boardsize size=1>
		<option value=19>19x19</option>
		<option value=13>13x13</option>
	  </select>
	</td>
  </tr>
  <tr>
	<td align=right>
	  Handicaps:</td>
	<td><input type=textbox id=handicaps value=0 size=3 onChange=\"changeHandicap()\"></td>
  </tr>
  <tr>
	<td align=right>
	  Komi:</td>
	<td><input type=textbox id=komi value=7.5 size=5></td>
  </tr>
  <tr>
	<td align=right>Winner:</td>
	<td><select id=winner size=1>
		<option value=1>White</option>
		<option value=0>Black</option>
	</select></td>
  </tr>
  <tr>
	<td align=right>Date:</td>
	<td><input type=text id=date></td>
  </tr>
  <tr><td colspan=2 align=center>
	  <input type=\"button\" name=\"submit\" value=\"Submit\"
			 onClick=\"addResult()\"></td></tr>
</table>
</form>

<script>
	var names_ar = new Array()\n";

$names = mysql_query("SELECT id, name FROM $members_tb ORDER BY name ASC");

$i = 0;
while($row = mysql_fetch_array($names)) {
	$name = $row['name'];
	$id = $row['id'];
	echo "\tnames_ar[$i] = new Option(\"$name\", \"$id\")\n";
	$i += 1;
}

echo "
	function populate(combo, name_ar) {
		combo_box = eval(combo)
		for (m = combo_box.options.length - 1; m > 0; m--)
			combo_box.options[m]=null
		name_array = eval(name_ar)
		for (i = 0; i < name_array.length; i++)
			combo_box.options[i]=new Option(name_array[i].text, name_array[i].value)
		combo_box.options[0].selected=false
	}

	populate(document.club_results.white, names_ar)
	populate(document.club_results.black, names_ar)

	//-->
</script>
";

echo "<p>Recent game results:</p>\n";

echo "<div id=recentResults>\n";

print_game_results($con, $members_tb, $results_tb, -1, 'all', 6/*limit*/,
				   0/*count_names*/);
#recent_results($members_tb, $results_tb);

echo "</div>\n";

mysql_close($con);

?>
	</td>
      </tr>
    </table>
	<?php include("footer.shtml"); ?>
  </body>
</html>
