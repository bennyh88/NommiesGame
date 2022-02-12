<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1-transitional.dtd">
<?php
	include 'commonFunctions.php';
	loadParams();
	unpackGame();
	global $gameID;
	global $playerID;

	//$numberOfOpponents = count($gameOBJ->players) - 1;//TODO do i need this?
	//echo($numberOfOpponents);

?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="styleSheet.css">
		<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
		-->
		<link href="cards.css" rel="stylesheet" type="text/css">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="player.js"></script>
		<script src="game.js"></script>
    	<script src="nommies.js"></script>

		<title>Nommies</title>
	</head>

	<body>
		<div class='area'>
			<div class='leftColumn'>
				<div class='opponentRow'>
					<?php
						//$playerOrderArray = getPlayerOrder($thisPlayer);//returns an array of playerID's
						$playerOrderArray = $thisPlayer->playerOrder;
						foreach ($playerOrderArray as $player) {
							//$player = getPlayerFromID($player, $gameOBJ);
							$player = $gameOBJ->getPlayerFromID($player);
							$toWin = $player->playerCall - $player->playerTricksWon;
							echo <<<EOL
							<div id='opponent$player->playerName' class='opponent'>
								<div id='name$player->playerIndex' class='playerName'>$player->playerName</div>
								<div class='playerTricksToWin'>To Win: $toWin</div>
							</div>
							EOL;
						}
					 ?>

				</div>
				<div class='pitch' ondrop="drop(event)" ondragover="allowDrop(event)">
					<div class='opponentRow'><!--opponentRow -->
						<?php
							//$playerOrderArray = getPlayerOrder($thisPlayer);//returns an array of playerID's
							$playerOrderArray = $thisPlayer->playerOrder;
							foreach ($playerOrderArray as $player) {
								//$player = getPlayerFromID($player, $gameOBJ);
								$player = $gameOBJ->getPlayerFromID($player);
								//HEREDOC
								echo <<<EOL
								<div id='opponentCard$player->playerIndex' class='opponent'>
									<img class='card' src='cards/AH.svg'>
								</div>
								EOL;
							}
						 ?>
					</div>
					<div class='playArea' id='playedCardDiv'>
						<img class='card' src='cards/AS.svg' style='visibility:hidden;'>
					</div>
				</div>

				<div class='playerRow'>
					<div class='playerInfo'>
						<?php
							$toWin = $thisPlayer->playerCall - $thisPlayer->playerTricksWon;
							//HEREDOC
							echo <<<EOL
							<div id='name$thisPlayer->playerIndex' class='playerName'>$thisPlayer->playerName</div>
							<div class='playerTricksToWin'>To Win: $toWin</div>
							EOL;
						 ?>
					</div>
					<div id='playerHand' class="hand hhand-compact active-hand">
						<img class='card' src='cards/2S.svg' ondragstart="drag(event)" id='2S'>
						<img class='card' src='cards/3S.svg' ondragstart="drag(event)" id='3S'>
						<img class='card' src='cards/4S.svg' ondragstart="drag(event)" id='4S'>
						<img class='card' src='cards/10S.svg' ondragstart="drag(event)" id='10S'>
						<img class='card' src='cards/6H.svg' ondragstart="drag(event)" id='6H'>
						<img class='card' src='cards/3H.svg' ondragstart="drag(event)" id='3H'>
						<img class='card' src='cards/QH.svg' ondragstart="drag(event)" id='QH'>
						<img class='card' src='cards/KH.svg' ondragstart="drag(event)" id='KH'>
					</div>
				</div>
			</div>
			<div class='rightColumn'>
				<div class='scoreTableDiv'>
					<table>
						<tr>		<th rowspan="2">T</th><th rowspan="2">H</th>	<th colspan="2">Ben</th>	<th colspan="2">David</th>	<th colspan="2">Sam</th>	<th colspan="2">Nathan</th></tr>
						<tr>		<th>C</th><th>S</th>	<th>C</th><th>S</th><th>C</th><th>S</th><th>C</th><th>S</th></tr>
						<colgroup>	<col class="title" span="2"/><col class="call"/><col class="score" /><col class="call"/><col class="score" /><col class="call"/><col class="score" /><col class="call"/><col class="score" /></colgroup>
						<tr>		<td>♠️</td><td>8</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♠️</td><td>7</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♠️</td><td>6</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♠️</td><td>5</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♠️</td><td>4</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♦️</td><td>3</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♦️</td><td>2</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♥️</td><td>1</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♥️</td><td>2</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♥️</td><td>3</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♥️</td><td>4</td>	<td>1</td><td>1</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td><td>2</td></tr>
						<tr>		<td>♣️</td><td>5</td>	<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
						<tr>		<td>-</td><td>6</td>	<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
						<tr>		<td>-</td><td>7</td>	<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
						<tr>		<td>-</td><td>8</td>	<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
					</table>
				</div>
				<div class='trumpSuit'>Trump Suit:</div>
				<div class='trumpSuitPip'>♣️</div>
			</div>
		</div>
		<div id="chooseCallModal" class="modal">
		  <!-- Modal content -->
		  <div class="modal-content">
			<label for="name">How many hands will you win?</label><br><br>
			<div id="callModalDiv"></div>
		  </div>
		</div>
	</body>
</html>
