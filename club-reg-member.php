<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
<HTML VERSION="-//IETF//DTD HTML 3.2//EN">
  <head>
	 <?php include("simpletree-headerpart.shtml"); ?>
	 <?php include("common-func.php"); ?>
	 <script type="text/javascript" src="get-http-obj.js"></script>
	 <script language="JavaScript">
	function reg_member(box, id)
	{
		xmlhttp=GetXmlHttpObject();
		if (xmlhttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return;
		}
		var url="club-reg-member-req.php";
		url=url+"?id="+id;
		if (box.checked) {
			url=url+"&checkin=1";
		} else {
			url=url+"&checkin=0";
		}

		var d = new Date();
		var currYear = d.getFullYear();
		var currMonth = d.getMonth() + 1;
		var currDate = d.getDate();
		var currHour = d.getHours();
		var currMin = d.getMinutes();
		date_str = currYear + "-" +
		(currMonth < 10 ? "0" : "") + currMonth + "-" +
		(currDate < 10 ? "0" : "") + currDate

		url=url+"&regdate="+date_str;
		url=url+"&sid="+Math.random();
		xmlhttp.onreadystatechange=stateChanged;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);
	}

	function toggleTables(box) 
	{
		// Get table and count elements.
		var allMembers = document.getElementById("all members table");
		var activeMembers = document.getElementById("active members table");
		var allCount = document.getElementById("all member count");
		var activeCount = document.getElementById("active member count");

		// style=display:{string}
		var visible = "";
		var none = "none";
		if (box.checked) {
			// Show "all members" and hide "active members" 
			allMembers.style.display = visible;
			allCount.style.display = visible;

			activeMembers.style.display = none;
			activeCount.style.display = none;
		} else {
			// Show "active members" and hide "all members"
			activeMembers.style.display = visible;
			activeCount.style.display = visible;

			allMembers.style.display = none;
			allCount.style.display = none;
		}
	}

	function stateChanged()
	{
		if (xmlhttp.readyState==4) {
			document.getElementById("debug").innerHTML=xmlhttp.responseText;
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
require_once('config.php');
$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}

mysql_select_db(MYSQL_DB, $con);

// Find a way to hide the HTML this query returns, if a checkbox is clicked.

$select_query = "SELECT id, name, rank, regdate FROM members ";
$date_cond = "WHERE regdate >= date_sub(curdate(), INTERVAL 1 YEAR) ";
$order = "ORDER BY name ASC, rank DESC";

$active_members = mysql_query($select_query . $date_cond . $order);
$all_members = mysql_query($select_query . $order);

$num_active_members = mysql_num_rows($active_members);
$num_all_members = mysql_num_rows($all_members);

// Split tables into three main columns.
$num_cols = 3;

echo "<p>Please mark your name below so that we know you are here today.</p>\n";

// echo "<div id=\"debug\"></div>\n";

echo "Show all members: <input id=\"show all\" type=checkbox onclick=\"toggleTables(this)\"><br>\n"
   . "<p id=\"active member count\" style=\"\">Active members: $num_active_members</p>\n"
   . "<p id=\"all member count\" style=\"display:none\">Total members: $num_all_members</p><br>\n";

// Emit the active members table table.
emit_member_table($num_active_members, $num_cols, "active members table", "", $active_members);
emit_member_table($num_all_members, $num_cols, "all members table", "none", $all_members);

mysql_close($con);
?>
	<?php include("footer.shtml"); ?>
  </body>
</html>
