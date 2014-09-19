var xmlhttp;

function changeHandicap()
{
	var handicaps = document.getElementById('handicaps');
	var komi = document.getElementById('komi');

	if (handicaps.value != 0) {
		komi.value = 0.5;
	}
}

function addResult()
{
	var gameInfo;
	var white = document.getElementById('white');
	var black = document.getElementById('black');
	var boardsize = document.getElementById('boardsize');
	var handicaps = document.getElementById('handicaps');
	var komi = document.getElementById('komi');
	var date = document.getElementById('date');
	var winner;

	var white_name = white.options[white.selectedIndex].text;
	var black_name = black.options[black.selectedIndex].text;

	if (document.getElementById('winner').value == 1) {
		winner = white_name;
	} else {
		winner = black_name;
	}

	gameInfo = "White: " + white_name  + "\n" +
		"Black: " + black_name + "\n" +
		"Board: " + boardsize.value + "x" + boardsize.value + "\n" +
		"Handicaps: " + handicaps.value + "\n" + 
		"Komi: " + komi.value + "\n" + 
		"Winner: " + winner + "\n" + 
		"Date: " + date.value + "\n";
	var answer = confirm (
		"Are you sure to submit the following game result?\n" + gameInfo);
	if (answer) {
		xmlhttp=GetXmlHttpObject();
		if (xmlhttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return;
		}
		var url="club-add-results.php";
		url=url+"?white="+document.getElementById('white').value;
		url=url+"&black="+document.getElementById('black').value;
		url=url+"&boardsize="+document.getElementById('boardsize').value;
		url=url+"&handicaps="+document.getElementById('handicaps').value;
		url=url+"&komi="+document.getElementById('komi').value;
		url=url+"&winner="+document.getElementById('winner').value;
		url=url+"&date="+document.getElementById('date').value;
		url=url+"&sid="+Math.random();
		xmlhttp.onreadystatechange=stateChanged;
		xmlhttp.open("GET",url,true);
		xmlhttp.send(null);

		// reset the names
		white.options[white.selectedIndex].selected = false;
		black.options[black.selectedIndex].selected = false;
		//boardsize.options[0].selected = true;
		//handicaps.value = 0;
		//komi.value = 7.5;
		//winner.options[winner.selectedIndex].selected = false;
	} else {
		alert ("Game result was NOT added")
	}
}

function stateChanged()
{
	if (xmlhttp.readyState==4)
	{
		document.getElementById("recentResults").innerHTML=xmlhttp.responseText;
	}
}
