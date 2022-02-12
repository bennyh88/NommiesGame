<?php
include 'game.php';
include 'player.php';
include 'deck.php';
//request parameters
$function;
$gameID;
$playerID;
$payload;
$oldGameHash;

$gameOBJ;
$thisPlayer;
//player variables
$playerName; //nickname on screen
//game variables
$hand = 8; // $round = (2*$hand)-1 8,7,6,5,4,3,2,1,2,3,4,5,6,7,8 = 15
$param;


//$mysqli = new mysqli("ip","user","pass","database");
$mysqli = new mysqli("localhost","nommies","Nommies123","nommies");
if ($mysqli -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
}


function loadParams() {
    global $function;
    global $gameID;
    global $playerID;
    global $playerName;
    global $payload;
    global $oldGameHash;

    //get the data from the request and make it safe to use before storing it as a variable
    if (isset($_GET["function"])) {
        $function = filter_input(INPUT_GET, 'function', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (isset($_GET["gameID"])) {
        $gameID = filter_input(INPUT_GET, 'gameID', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (isset($_GET["playerID"])) {
        $playerID = filter_input(INPUT_GET, 'playerID', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (isset($_GET["playerName"])) {
        $playerName = filter_input(INPUT_GET, 'playerName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (isset($_GET["oldGameHash"])) {
        $oldGameHash = filter_input(INPUT_GET, 'oldGameHash', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    if (isset($_GET["payload"])) {
        $payload = filter_input(INPUT_GET, 'payload', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    //var_dump($payload);
}

function rejectUser() {
    closeDBConnection();
    exit();
}

function closeDBConnection() {
    global $mysqli;
    $mysqli->close();
    //echo "MySQL connection closed successfully";
}

function createGame() {
    //get player details and make a player object
    global $playerName;
    $player = new Player();
    $player->playerID = makeid(10);
    $player->playerName = $playerName;

    //make a game object and attach the player object
    global $hand;
    global $gameOBJ;
    $gameOBJ = new Game();
    $gameOBJ->id = makeid(10);
    $gameOBJ->round = ($hand*2) - 1; // initialise with 8,7,6,5,4,3,2,1,2,3,4,5,6,7,8
    $gameOBJ->hand = $hand;
    $gameOBJ->gameStage = "CALLING";
    $gameOBJ->gameStarted = 0;
    $gameOBJ->players[] = $player;
    $gameOBJ->gameHash = makeid(10);

    $gameCredentials = new GameCredentials();
    $gameCredentials->gameID = $gameOBJ->id;
    $gameCredentials->playerID = $player->playerID;

    $gameJSON = json_encode($gameCredentials);
    echo $gameJSON;

    //echo '{"gameID":"', $gameOBJ->id, '", "playerID":"', $player->playerID, '"}';
}

function makeid($length) {
   $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
   $result = '';
   for ($i = 0; $i < $length; $i++) {
       $random_key = rand(0, strlen($characters));
       $result .= substr($characters, $random_key, 1); //.= what is this? walrus operator??? Use a for each instead
   }
   return $result;
}

function joinGame() {
    global $gameOBJ;
    global $playerName;

    //if the game is in a state to allow someone to join Game
    if ($gameOBJ->gameStarted == 0) {
        //get player details and make a player object
        $player = new Player();
        $player->playerID = makeid(10);
        $player->playerName = $playerName;
        //add player object to game object
        array_push($gameOBJ->players, $player);

        $gameCredentials = new GameCredentials();
        $gameCredentials->gameID = $gameOBJ->id;
        $gameCredentials->playerID = $player->playerID;

        $gameJSON = json_encode($gameCredentials);
        echo $gameJSON;
    } else {
        echo "game already in progress";
    }

}

//TODO move to lobby.php if not utilised for other pages
function updateLobby() {
    global $gameOBJ;
    $playerNames = [];
    for ($i = 0; $i < count($gameOBJ->players); $i++) {
        $player = $gameOBJ->players[$i];
        $playerNames[$i] = $player->playerName;
    }
    $responseJSON = json_encode($playerNames);
    echo $responseJSON;
}

//TODO move to lobby.php if not utilised for other pages
//TODO work out player one once only and store it in $gameOBJ instead. faster??
function isPlayerOne() { // used in lobby.php to decide who has start button shown
    global $gameOBJ;
    global $playerID;
    $playerOne = $gameOBJ->players[0];
    $playerOne = $playerOne->playerID;
    if ($playerID == $playerOne) {
        echo "true";
    }
    else {
        echo "false";
    }
}

function unpackGame() {
    global $mysqli;
    global $gameID;
    global $playerID;

    global $gameOBJ;
    global $thisPlayer;

    $gameJSON;

    //retrieve game JSON from the Database
    $query = "SELECT gameObject FROM gameTable WHERE gameID = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $gameID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $gameJSON = $row["gameObject"];
        }
    } else {
        echo "incorrect game ID?";
    }

    //convert the JSON to a usable Game object
    $gameOBJ = new Game();
    $gameOBJ->loadJSON($gameJSON);

    //Get object reference of player we are working with
    if (isset($playerID)) {
        foreach ($gameOBJ->players as $player) {
            if ($player->playerID == $playerID) {
                $thisPlayer = $player;
            }
        }
    }

}

function packGame() {
    global $mysqli;
    global $gameOBJ;

    $scoreOBJ = "SCORE";
    $gameID = $gameOBJ->id; //when create game is used the global value is empty
    $gameHash = $gameOBJ->gameHash;

    //convert game object into JSON format
    $gameJSON = json_encode($gameOBJ);
    //insert the game JSON into the database
    $query = "REPLACE INTO gameTable (gameid, gameObject, scoreObject, gameHash) VALUES(?,?,?,?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssss", $gameID, $gameJSON, $scoreOBJ, $gameHash);
    $stmt->execute();

}

function pollGameStarted() {
    global $gameOBJ;
    if ($gameOBJ->gameStarted == 1) {
        echo "Game Started";
    } else {
        echo "Waiting for players";
    }
}

function pollForUpdates() {
    //return the most recent timestamp
    global $mysqli;
    global $gameID;
    global $oldGameHash;
    //retrieve game JSON from the Database
    //$query = "SELECT timeStamp FROM gameTable WHERE gameID = ?";
    $query = "SELECT gameHash FROM gameTable WHERE gameID = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $gameID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $timeStamp = $row["gameHash"];
            if ($row["gameHash"] == $oldGameHash) {
                echo "false";
                closeDBConnection();
                exit();
            } else {
                unpackGame();
                updateGame();
            }
        }
    } else {
        echo "incorrect game ID?";
    }

}

function updateGame() {
    global $gameOBJ;
    $gameJSON = json_encode($gameOBJ);
    echo($gameJSON);
}

function updateScoreboard() {

}

function submitCall() {
    global $gameOBJ;
    global $payload;
    global $thisPlayer;
    //var_dump($payload);
    if ($thisPlayer->playerTurn == TRUE) { //only accept call if its the players turn
        $call = $payload;
        $call = intval($call);
        $thisPlayer->playerCall = $call;
        array_push($gameOBJ->callsArray, $thisPlayer->playerIndex);
        //$gameOBJ->advanceTurn();
    }
}

function submitMove() {
    global $payload;
    global $gameOBJ;
    global $thisPlayer;

    $move = $payload;
    if (moveAllowed()) {
        //remove from $playerHand
        for ($i=0; $i<count($thisPlayer->playerHand); $i++) {
            if ($thisPlayer->playerHand[$i][3] == $move) {
                array_splice($thisPlayer->playerHand, $i, 1);
                break;
            }
        }
        $move = $gameOBJ->deck->getCardFromId($move);
        $thisPlayer->playerCardInPlay = $move;
        array_push($gameOBJ->cardsInPlay, $move);
    }
}

function moveAllowed() {
    //is the players $turn?
    //is it the first card to be played?
    //is the suit in the players hand and is the card being played following Suit
    

    //move is legal
    return TRUE;
}

function submitTrump() {
    global $thisPlayer;
    if ($thisPlayer->playerTurn) {
        $gameOBJ->trump = "N"; //No trumps for testing

    }
    //echo "trump submitted";
}

class GameCredentials {
    public $playerID;
    public $gameID;
}

class Payload {
    //encode my game object to deliver data
}

?>
