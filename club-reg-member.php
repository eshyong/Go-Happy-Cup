<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
<HTML VERSION="-//IETF//DTD HTML 3.2//EN">
  <head>
	 <?php include("simpletree-headerpart.shtml"); ?>
	 <?php include("common-func.php"); ?>
	 <script type="text/javascript" src="get-http-obj.js"></script>
	 <script language="JavaScript">
	function reg_member(box, id)
	{
		//if (box.checked)
		// 	window.alert(id + " checked");
		//else
		// 	window.alert(id + " unchecked");

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
$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PW);
$member_table = "members";
if (!$con) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(MYSQL_DB, $con);

$result = mysql_query("SELECT * FROM $member_table ORDER BY rank DESC, name ASC");

$result_name_order = mysql_query(
	"SELECT id, name, rank, regdate FROM $member_table ORDER BY name ASC");

echo "<p>Please mark your name below so that we know you are here today.</p>\n";

echo "<div id=\"debug\"></div>\n";

$num_members = mysql_num_rows($result);
echo "<p>Total members: $num_members</p>\n";

$num_col = 3;
$num_members_per_col = $num_members / $num_col;

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}

echo "<table>
<tr>";

$n = 0;
for ($col = 0; $col < $num_col; $col++) {
	echo "<td valign=top>\n";

	/* table ordered by name */
	echo "<table border=1>";
	echo "<tr><td>Name (Rating)</td>";
	echo "<td width=30 align=center>I Am Here</td></tr>";

	for ($row = 0; $row < $num_members_per_col; $row++) {
		if ($n == $num_members) {
			break;
		}
		if ($result = mysql_fetch_array($result_name_order)) {
			echo "<tr>";
			echo "<td>" . $result['name'];
			echo "("; echo rank_str($result['rank']); echo ")";
			echo "<td align=center><INPUT TYPE=CHECKBOX";
			if (strtotime($result['regdate']) >= strtotime("today")) {
				echo " checked";
			}
			echo " onclick=\"reg_member(this, " .
				$result['id'] . ")\"></td>";
			echo "</tr>\n";
		}
		$n++;
	}
	echo "</table>\n";

	echo "</td>\n";
}

echo "</tr>
    </table>\n";

echo "   </td>
      </tr>
    </table>\n";

mysql_close($con);
?>
	<?php include("footer.shtml"); ?>
  </body>
</html>
