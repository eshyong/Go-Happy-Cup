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
	var url="club-return-rating-history.php";
	url=url+"?id="+name_box.value;
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
$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
$members_tb = "members";
$results_tb = "results";
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

echo "<p>This page shows the ratings history of each club member.  If some members' ratings are integers (such as Wenguang Wang), it is because their ratings are used as <b>anchors</b>, so that their ratings do not change.</p>\n";

echo "<p>If you find your kids' ratings drop, don't worry, it is very possible that the anchor players' rating (such as Wenguang Wang) improves, so your kids' rating relative to the anchor plays drops.</p>\n";

echo "<form name=dummyform>
Show rating history of
<select id=names size=1 onchange=\"getResults()\">
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
	name_combo_box.options[i+1]=new Option(names_ar[i].text, names_ar[i].value)
name_combo_box.options[0].selected=false
";

echo "
</script>
";

echo "<div id=results>\n";
echo "</div>\n";

mysql_close($con);

?>
	</td>
      </tr>
    </table>
	<?php include("footer.shtml"); ?>
  </body>
</html>
