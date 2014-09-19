<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
<HTML VERSION="-//IETF//DTD HTML 3.2//EN">
  <head>
	 <?php include("simpletree-headerpart.shtml"); ?>
	 <?php include("club-results-of-id.php"); ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
	<script type="text/javascript" src="get-http-obj.js"></script>
	 <script language="JavaScript">
function getResults()
{
	name_box = document.getElementById("names")
	date_box = document.getElementById("dates")
    //alert("date is \"" + date_box.value + "\"");
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="club-return-results.php";
	url=url+"?id="+name_box.value;
	url=url+"&meetingdate="+date_box.value;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=stateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}
function stateChanged()
{
	if (xmlhttp.readyState==4)
	{
		document.getElementById("results").innerHTML=xmlhttp.responseText;
	}
}
</script>
  </head>
  <body>
    <table width="100%">
      <tr>
	 	<?php include("header.shtml"); ?>
      </tr>
    </table>

    <table border="0" width="100%" cellspacing=1 cellpadding=12>
      <tr>
	 <?php include("left-menu-simpletree.shtml"); ?>
	 <td valign="top" width="99%">
<?php
function add_date($givendate,$day=0,$mth=0,$yr=0) {
	if (phpversion() >= "5.1.0") {
		date_default_timezone_set('America/Los_Angeles');
	}
	$cd = strtotime($givendate);
	$newdate = date('Y-m-d',
					mktime(date('h',$cd), date('i',$cd), date('s',$cd),
						   date('m',$cd)+$mth, date('d',$cd)+$day,
						   date('Y',$cd)+$yr));
	return $newdate;
}

?>
<?php
require('config.php');
$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
$members_tb = "members";
$results_tb = "results";
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

echo "<p>We are using KGS rule to calculate ratings based on these game results.</p>\n";

echo "<form name=dummyform>
Show game results of
<select id=names size=1 onchange=\"getResults()\">
	<option value=\"#\">This is a Place Holder</option></select>
on
<select id=dates size=1 onchange=\"getResults()\">
	<option value=\"#\">This is a Place Holder</option></select>
</form>";

echo "
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

/* fill values in names_ar into the names combo box */
echo "
name_combo_box = eval(\"document.dummyform.names\")
for (m = name_combo_box.options.length - 1; m > 0; m--)
	name_combo_box.options[m]=null
name_combo_box.options[0]=new Option('All Members', '-1')
for (i = 0; i < names_ar.length; i++)
	name_combo_box.options[i + 1]=new Option(names_ar[i].text, names_ar[i].value)
name_combo_box.options[0].selected=true
";

echo "
	var dates_ar = new Array()\n";

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}

/* hardcode the first Club meeting date */
$first_meeting = "2010-03-27";
$last_meeting = strtotime("next Saturday");

/* set meeting time to 7pm */
$last_meeting += 19 * 3600;

/* last meeting cannot be in the future */
if ($last_meeting > time()) {
	$last_meeting -= 7 * 24 * 3600;
	if ($last_meeting > time()) {
		$last_meeting -= 7 * 24 * 3600;
	}
}

$last_meeting_str = strftime("%Y-%m-%d", $last_meeting);

$n = 0;
$i = 0;
for ($meeting_date = $first_meeting; $meeting_date <= $last_meeting_str;
	 $meeting_date = add_date($meeting_date, 7)) {
	//$meeting_date_str = strftime("%Y-%m-%d", $meeting_date);
	echo "\tdates_ar[$i] = new Option(\"$meeting_date\", \"$meeting_date\")\n";
	$n++;
	$i++;
	if ($n > 1000) // avoid deadloop
		break;
}

/* fill values in dates_ar into the names combo box */
echo "
date_combo_box = eval(\"document.dummyform.dates\")
for (m = date_combo_box.options.length - 1; m > 0; m--)
	date_combo_box.options[m]=null
date_combo_box.options[0]=new Option('All Dates', 'all')
for (i = 0 ; i < dates_ar.length; i++) {
	date_combo_box.options[i + 1]=new Option(
		dates_ar[dates_ar.length - i - 1].text,
		dates_ar[dates_ar.length - i - 1].value)
}
date_combo_box.options[1].selected=true
";

echo "
</script>
";

//echo "last_meeting_str=", $last_meeting_str, "<br>";

echo "<div id=results>\n";
print_game_results($con, $members_tb, $results_tb, -1, $last_meeting_str);
echo "</div>\n";

mysql_close($con);

?>
	</td>
      </tr>
    </table>
	<?php include("footer.shtml"); ?>
  </body>
</html>
