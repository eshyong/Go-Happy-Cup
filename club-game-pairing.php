<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN" >
<HTML VERSION="-//IETF//DTD HTML 3.2//EN">
  <head>
	 <?php include("simpletree-headerpart.shtml"); ?>
	 <?php include("club-print-active-games.php"); ?>
	 <script type="text/javascript" src="get-http-obj.js"></script>
	 <script language="JavaScript">
	 var num = 0;
function loadAvailablePlayers(my_name_combo_box)
{
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="club-get-avail-players.php";
	url=url+"?sid="+Math.random();
	xmlhttp.onreadystatechange=availablePlayersChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
	num++;
}

function availablePlayersChanged()
{
	if (xmlhttp.readyState==4) {
		var options = JSON.parse(xmlhttp.responseText);
		//var options = JSON.parse("{\"all\": [{\"value\": \"28\", \"display\": \"Alan Huang\"},{\"value\": \"41\", \"display\": \"Victor Wang(parent)\"}]}");
		//myname = document.getElementById("myname");

		/* remove old options */
		for (m = myname.options.length - 1; m >= 0; m--) {
			document.getElementById("myname").options[m] = null;
		}

		for (i = 0; i < options.all.length; i++) {
			document.getElementById("myname").add(new Option(options.all[i].display, options.all[i].value), null);
		}

		/*
		var elOptNew = document.createElement('option');
		elOptNew.text = 'ABC';
		elOptNew.value = 'abc';
		document.getElementById("myname").add(elOptNew, null);
		*/
	}
}

function myNameChanged(my_name_combo_box)
{
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="club-get-pairing-info.php";
	url=url+"?id="+my_name_combo_box.value;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=suggestGameStateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

	// this is the answer we get when my name is selected
function suggestGameStateChanged()
{
	if (xmlhttp.readyState==4) {
		document.getElementById("gameinfo").innerHTML=xmlhttp.responseText;
		//window.location.reload();
		//refreshAvailablePlayers();
	}
}

function startGame(game_candidate_id, white_id, black_id, is_reverse)
{
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	handicaps = document.getElementById("handicaps_"+game_candidate_id).value;
	komi = document.getElementById("komi_"+game_candidate_id).value;
	boardsize = document.getElementById("boardsize_"+game_candidate_id).value;
	var url="club-add-active-game.php";
	if (is_reverse) {
		url=url+"?white_id="+black_id;
		url=url+"&black_id="+white_id;
	} else {
		url=url+"?white_id="+white_id;
		url=url+"&black_id="+black_id;
	}
	url=url+"&handicaps="+handicaps;
	url=url+"&boardsize="+boardsize;
	url=url+"&komi="+komi;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=activeGameStateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function activeGameStateChanged()
{
	if (xmlhttp.readyState==4) {
		document.getElementById("gameinfo").innerHTML = "";
		document.getElementById("activegames").innerHTML=xmlhttp.responseText;
		//refreshAvailablePlayers();
		//window.location.reload();
	}
}

function recordResult(game_id)
{
	white_win = document.getElementById("winner_"+game_id).value;
	if (white_win != 0 && white_win != 1) {
		window.alert("Must choose a winner first!");
		return;
	}
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="club-record-result.php";
	url=url+"?id="+game_id;
	url=url+"&white_win="+white_win;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=activeGameStateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function cancelGame(game_id)
{
	xmlhttp=GetXmlHttpObject();
	if (xmlhttp==null) {
		alert ("Browser does not support HTTP Request");
		return;
	}
	var url="club-cancel-active-game.php";
	url=url+"?id="+game_id;
	url=url+"&sid="+Math.random();
	xmlhttp.onreadystatechange=activeGameStateChanged;
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}

function clearWinner(winner_combo_box) {
	winner_combo_box.options[0].selected=false;
}

function changeBoardSize(boardsize_combo_box, id) {
	handicaps = document.getElementById("handicaps_"+id).value;
	komi = document.getElementById("komi_"+id).value;
	//window.alert("handicaps = " + handicaps + " on id " + id);
	if (boardsize_combo_box.value == 19) {
		if (handicaps == 0) {
			if (komi == 5.5) {
				document.getElementById("komi_"+id).value = 7.5;
			}
		} else {
			document.getElementById("handicaps_"+id).value = (handicaps * 1 + 1) / 2;
		}
	} else {
		if (handicaps == 0) {
			if (komi == 7.5) {
				document.getElementById("komi_"+id).value = 5.5;
			}
		} else {
			document.getElementById("handicaps_"+id).value = handicaps / 2 - 1;
		}
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

mysql_select_db(MYSQL_DB, $con);

echo "<p>This page helps you find opponent for tonight's club meeting.</p>\n";

/*
echo "<p>Please first <input type=\"button\" value=\"Reload Page\" onClick=\"window.location.reload()\">
to get the most up to date information</p>\n";*/

echo "<form name=dummyform>"; // action=\"club-.php\" method=\"post\">";
echo "<p>Suggest a game for 
<select id=myname size=1 OnMouseOver=\"loadAvailablePlayers(this)\"
 onchange=\"myNameChanged(this)\">
	<option value=\"#\"> </option>
</select></p>";

if (phpversion() >= "5.1.0") {
	date_default_timezone_set('America/Los_Angeles');
}
$today_date = strftime("%Y-%m-%d", strtotime("today"));

#
# find all registered players who are not in a game
#
$sql = "SELECT id, name FROM members
            WHERE members.regdate >= \"$today_date\" AND
            NOT id IN (SELECT white FROM results
                       WHERE active = 1 AND date=\"$today_date\"
                       UNION
                       SELECT black FROM results
                       WHERE active = 1 AND date=\"$today_date\")
            ORDER BY name ASC";

//echo $sql;
$available_players = mysql_query($sql);

echo "<script>
function refreshAvailablePlayers() {
    var names_ar = new Array()\n";

$i = 0;
while ($row = mysql_fetch_array($available_players)) {
	$name = $row['name'];
	$id = $row['id'];
	echo "\tnames_ar[$i] = new Option(\"$name\", \"$id\")\n";
	$i += 1;
}

/* fill values in names_ar into the names combo box */
echo "
my_name_combo_box = eval(\"document.dummyform.myname\")
for (m = my_name_combo_box.options.length - 1; m > 0; m--) {
	my_name_combo_box.options[m]=null;
}
for (i = 0; i < names_ar.length; i++) {
	my_name_combo_box.options[i]=new Option(names_ar[i].text, names_ar[i].value);
}
my_name_combo_box.options[0].selected=false;
";

echo "}\n";
echo "refreshAvailablePlayers();\n";
echo "</script>\n";

echo "<div id=\"gameinfo\"></div>\n";

echo "<div id=\"activegames\">\n";
print_active_games($con);
echo "</div>\n";

mysql_close($con);
?>
<?php include("footer.shtml"); ?>
</body>
</html>
