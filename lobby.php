<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1-transitional.dtd">

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styleSheet.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    	<script src="lobby.js"></script>
		<title>Nommies</title>
	</head>
	<body>
		<h2></h2>
		<div id="gameInfoDiv">
			<?php
				$gameID = $_GET["gameID"];
				echo "game ID = ". $gameID;
		 	?>
	 	</div>
		<div id="playerDiv">
			Players:
		</div>
		<div id="infoDiv"> Placeholder Text</div>
		<button id="startButton" type="button" onclick="startGame()">Start Game</button>
	</body>
</html>
