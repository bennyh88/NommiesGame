<?php
    include 'commonFunctions.php';
    //request parameters
    $function;
    $gameID;
    $playerID;
    $payload;

    $gameOBJ;
    //player variables
    $playerName = "Benny"; //nickname on screen
    //game variables
    $hand = 8; // $round = (2*$hand)-1 8,7,6,5,4,3,2,1,2,3,4,5,6,7,8 = 15
    $param;


    //$mysqli = new mysqli("ip","user","pass","database");
    $mysqli = new mysqli("localhost","nommies","Nommies123","nommies");
    if ($mysqli -> connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
        exit();
    }

    loadParams();
    //var_dump($payload);

    if ($function != "createGame") {
        unpackGame();
    }

    switch ($function) {
        case "createGame":
            //echo "createGame";
            createGame();
            break;
        case "joinGame":
            //unpackGame();
            //echo "joinGame";
            joinGame();
            break;
        case "updateLobby":
            //unpackGame();
            //echo "startGame";
            updateLobby();
            break;
        case "startGame":
            //unpackGame();
            //echo "startGame";
            startGame();
            break;
        case "pollGameStarted":
            //unpackGame();
            pollGameStarted();
            break;#
        case "isPlayerOne":
            //unpackGame();
            isPlayerOne();
            break;
        default:
           echo "not a valid param";
           rejectUser();
    }

    packGame();
    closeDBConnection();
    exit();


    function loadParams() {
        global $function;
        global $gameID;
        global $playerID;
        global $playerName;
        global $payload;

        //get the data from the request and make it safe to use before storing it as a variable
        if (isset($_GET["function"])) {
            $function = filter_input(INPUT_GET, 'function', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        if (isset($_GET["gameID"])) {
            //var_dump($_GET["gameID"]);
            $gameID = filter_input(INPUT_GET, 'gameID', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //var_dump($gameID);
        }
        if (isset($_GET["playerID"])) {
            $playerID = filter_input(INPUT_GET, 'playerID', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        if (isset($_GET["playerName"])) {
            $playerName = filter_input(INPUT_GET, 'playerName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        /*
        if ($_GET["payload"]) {
            $payload = filter_input(INPUT_GET, 'move', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        */
        if (isset($_GET["payload"])) {
            $payload = filter_var_array( json_decode($_GET["payload"], true), [
               'email'   => [ 'filter' => FILTER_VALIDATE_EMAIL,
                              'flags'  => FILTER_NULL_ON_FAILURE ],
               'url'     => [ 'filter' => FILTER_VALIDATE_URL,
                              'flags'  => FILTER_NULL_ON_FAILURE ],
               'playerName'    => FILTER_VALIDATE_NAME,
               'address' => FILTER_SANITIZE_STRING
            ] );
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
        $gameOBJ->gameStarted = 0;
        $gameOBJ->players[] = $player;
        //$deck; //array of cards

        //var_dump($gameOBJ);

        //echo "playerID = ", $player->playerID;
        //echo "gameID = ", $gameOBJ->id;

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
        //var_dump($gameOBJ);
        //if the game is in a state to allow someone to join Game
        if ($gameOBJ->gameStarted == 0) {
            //get player details and make a player object
            $player = new Player();
            $player->playerID = makeid(10);
            $player->playerName = $playerName;
            //add player object to game object
            array_push($gameOBJ->players, $player);
            //echo $player->playerID, " was added successfully!";

            $gameCredentials = new GameCredentials();
            $gameCredentials->gameID = $gameOBJ->id;
            $gameCredentials->playerID = $player->playerID;

            $gameJSON = json_encode($gameCredentials);
            echo $gameJSON;
        } else {
            echo "game already in progress";
        }

    }

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



    function isPlayerOne() {
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
        global $gameOBJ;
        $gameJSON;
        //echo "unpacking game id:", $gameID;
        //retrieve game JSON from the Database
        $query = "SELECT gameObject FROM gameTable WHERE gameID = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $gameID);
        $stmt->execute();
        $result = $stmt->get_result();
        //var_dump($result);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $gameJSON = $row["gameObject"];
            }
        } else {
            echo "incorrect game ID?";
        }

        //convert the JSON to a usable object
        $gameOBJ = json_decode($gameJSON);
        //var_dump($gameOBJ);

    }

    function packGame() {
        global $mysqli;
        global $gameOBJ;
        global $gameID;

        $scoreOBJ = "SCORE";
        $gameID = $gameOBJ->id;

        //convert game object into JSON format
        $gameJSON = json_encode($gameOBJ);
        //insert the game JSON into the database
        $query = "REPLACE INTO gameTable (gameid, gameObject, scoreObject) VALUES(?,?,?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sss", $gameID, $gameJSON, $scoreOBJ);
        $stmt->execute();

    }

    function startGame() {
        global $gameOBJ;
        $gameOBJ->gameStarted = 1;
        echo "Game Started";
    }

    function pollGameStarted() {
        global $gameOBJ;
        if ($gameOBJ->gameStarted == 1) {
            echo "Game Started";
        } else {
            echo "Waiting for game to start";
        }
    }

    function checkForChanges() {
        //return the most recent timestamp
    }

    function updateScoreboard() {

    }

    function submitMove() {

    }

    class GameCredentials {
        public $playerID;
        public $gameID;
    }

    class Game {
        public $id;
        public $rounds; //number of cards to be dealt 8?
        public $hand; //how many dealt this time
        public $deck; //array of cards
        public $players = []; //array of player objects
    }

    class Player {
        public $playerID; //unique id for the player, used for players URL
        public $playerName; //nickname on screen
        public $playerHand; //an array containing the players cards
        public $playerCall;
        public $playerTricksWon;
    }


?>
