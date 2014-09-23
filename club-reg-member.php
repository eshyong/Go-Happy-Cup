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

    function toggleTables(box) {
        // Toggle visibility of the all member table or active member table.
        var allMembers = document.getElementById("all member table");
        var activeMembers = document.getElementById("active member table");
        var allCount = document.getElementById("all member count");
        var activeCount = document.getElementById("active member count");

        if (box.checked) {
            allMembers.style.display = "table";
            allCount.style.display = "";
            activeMembers.style.display = "none";
            activeCount.style.display = "none";
        } else {
            allMembers.style.display = "none";
            allCount.style.display = "none";
            activeMembers.style.display = "table";
            activeCount.style.display = "";
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
require('config.php');
$con = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}

mysql_select_db(MYSQL_DB, $con);

// Find a way to hide the HTML this query returns, if a checkbox is clicked.

$active_members = mysql_query("SELECT id, name, rank, regdate 
                                    FROM members
                                    WHERE regdate >= date_sub(curdate(), INTERVAL 1 YEAR)
                                    ORDER BY name ASC");
$all_members = mysql_query("SELECT * FROM members ORDER BY rank DESC, name ASC");
$num_active_members = mysql_num_rows($active_members);
$num_all_members = mysql_num_rows($all_members);

echo "<p>Please mark your name below so that we know you are here today.</p>\n";

// echo "<div id=\"debug\"></div>\n";

echo "Show all members: <input id=\"show all\" type=checkbox onclick=\"toggleTables(this)\"><br>\n"
   . "<p id=\"active member count\" style=\"\">Active members: $num_active_members</p>\n"
   . "<p id=\"all member count\" style=\"display:none\">Total members: $num_all_members</p><br>\n";

// Split tables into three main columns.
$num_col = 3;
$num_active_members_per_col = $num_active_members / $num_col;
$num_all_members_per_col = $num_all_members / $num_col;

// Emit the active member table table.
echo "<table id=\"active member table\" style=\"display:table\">";
for ($col = 0; $col < $num_col; $col++) {
	echo "<td valign=top>\n"
	   . "<table border=1>"
	   . "<tr><td>Name (Rating)</td>"
	   . "<td width=30 align=center>I Am Here</td></tr>";

	for ($row = 0; $row < $num_active_members_per_col; $row++) {
        // Emit tables for the active member table.
        $result = mysql_fetch_array($active_members);
        if ($result) {
            echo "<tr><td>"
               . $result['name']
               . "(" . rank_str($result['rank']) . ")"
               . "<td align=center><input type=checkbox" . " onclick=\"reg_member(this, " . $result['id'] . ")\"></td>"
               . "</tr>\n";
        }
	}
	echo "</table>\n" 
       . "</td>\n";
}

// End of "active member table" table.
echo "</table>\n";

// Emit the "all member table" table.
echo "<table id=\"all member table\" style=\"display:none\"><tr>";
for ($col = 0; $col < $num_col; $col++) {
	echo "<td valign=top>\n"
	   . "<table border=1>"
	   . "<tr><td>Name (Rating)</td>"
	   . "<td width=30 align=center>I Am Here</td></tr>";

	for ($row = 0; $row < $num_all_members_per_col; $row++) {
        // Emit tables for the all member table
        $result = mysql_fetch_array($all_members);
        if ($result) {
            echo "<tr><td>"
               . $result['name']
               . "(" . rank_str($result['rank']) . ")"
               . "<td align=center><input type=checkbox" . " onclick=\"reg_member(this, " . $result['id'] . ")\"></td>"
               . "</tr>\n";
        }
	}
	echo "</table>\n" 
       . "</td>\n";
}

// End "all member table" table.
echo "</tr></table>\n";

// End page format.
echo "</td></tr></table>\n";

mysql_close($con);
?>
	<?php include("footer.shtml"); ?>
  </body>
</html>
