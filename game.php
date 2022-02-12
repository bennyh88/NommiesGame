<?php
class Game {
    public $id;
    public $round; //number of cards to be dealt 8?
    public $hand = []; //array number of cards to deal for each round of game
    public $deck;
    public $players = []; //array of player objects
    public $dealer;
    public $turn;
    public $gameStage; // calling "C", choosing trump "T", or playing hand "P".
    public $gameStarted = 0;
    public $gameHash;
    public $cardsInPlay = [];
    public $callsArray = [];
    public $noOfPlayers;
    public $trump;

    function loadJSON($gameJSON) {
        $data = json_decode($gameJSON);

        $this->id = $data->id;
        $this->round = $data->round;
        $this->hand = $data->hand;
        $this->dealer = $data->dealer;
        $this->turn = $data->turn;
        $this->gameStage = $data->gameStage;
        $this->gameStarted = $data->gameStarted;
        $this->gameHash = $data->gameHash;
        $this->cardsInPlay = $data->cardsInPlay;

        $this->callsArray = $data->callsArray;
        $this->noOfPlayers = $data->noOfPlayers;
        $this->trump = $data->trump;

        $this->deck = new Deck();
        $this->deck->loadData($data->deck);

        foreach ($data->players as $playerData) {
            $player = new Player();
            $player->loadData($playerData);
            array_push($this->players, $player);
        }
    }

    public function updateGameHash() {
        $this->gameHash = makeid(10); //update hash whenever $gameOBJ is changed
    }

    public function startGame() {
        $this->gameStarted = 1;
        echo "Game Started";
    }

    public function setupGame() { //only one runce per game, and only triggered from player1's start game
        $this->hand = array(8, 7, 6, 5, 4, 3, 2, 1, 2, 3, 4, 5, 6, 7, 8); // round is index to hand array
        $this->round = 0;
        $this->noOfPlayers = count($this->players);
        $this->setPlayerOrder();
        $this->gameStage = "CALLING";
        $this->dealer = mt_rand(0,$this->noOfPlayers); //TODO should dealer be a playerObject or is an index sufficient?
        //$this->dealer = array_rand($this->players, 1); //TODO should dealer be a playerObject or is an index sufficient?
        //$this->turn = $this->dealer; //paired with advance turn lets advance turn handle overflow logic
        $this->advanceTurn($this->dealer);
        $this->newDeal();

    }

    public function getPlayerFromID($ID) { // returns player object
        $players = $this->players;
        foreach ($players as $player) {
            if ($player->playerID == $ID) {
                return $player;
            }
        }
    }

    function setPlayerOrder() { // also loads up player index
        foreach ($this->players as $thisPlayer) {
            $playerIndex;
            $size = count($this->players);
            $players = $this->players;
            //get index of thisPlayer
            for ($i = 0; $i < $size; $i++) {
                if ($players[$i]->playerID == $thisPlayer->playerID) {
                    $playerIndex = $i;
                }
            }

            $playerOrderArray = [];
            //fill up array, starting with values after index, then before
            for ($i = $playerIndex + 1; $i < $size; $i++) {
                array_push($playerOrderArray, $this->players[$i]->playerID);
            }
            for ($i = 0; $i < $playerIndex; $i++) {
                array_push($playerOrderArray, $this->players[$i]->playerID);
            }

            $thisPlayer->playerOrder = $playerOrderArray;
            $thisPlayer->playerIndex = $playerIndex;
        }
    }

    public function gameManager() { // used to manage stages of game, turn call, trump, etc.
        switch ($this->gameStage) {
            case "CALLING":
                //collect calls from all players, starting from player after dealer
                //once all calls are collected advance game stage to choose trump
                if (count($this->callsArray) < $this->noOfPlayers) { //not everyone has called
                    if ($this->players[$this->turn]->playerCall) { //if the player whose turn it is has called then advance the turn.
                        $this->advanceTurn($this->turn);
                    }
                } else { //everyone has called
                    $this->advanceGameStage();
                }
                break;
            case "FIND_HIGHEST_CALL":
                $x = [];
                foreach ($this->players as $player) {
                    if ($player->playerCall > $x[0]) {
                        $x = [];
                        array_push($x, $player->playerCall);
                    } else if ($player->playerCall == $x[0]) {
                        array_push($x, $player->playerCall);
                    } else {
                        //NOP
                    }
                }
                if (count($x) > 1) { //if 2 people call the same TODO make a more visual solution
                    $rand = mt_rand(1, count($x));
                    $rand -= 1;
                    foreach ($this->players as $player) {
                        if ($player->playerCall == $x[0]) { // if the playerCall == highest call
                            if ($rand == 0) {
                                $this->setTurn($player->playerIndex);
                                $this->advanceGameStage();
                            } else {
                                $rand -= 1;
                            }
                        }
                    }
                } else { //only one high call
                    foreach ($this->players as $player) {
                        if ($player->playerCall == $x[0]) {
                            $this->setTurn($player->playerIndex);
                            $this->advanceGameStage();
                        }
                    }
                }
                break;
            case "WAITING_TRUMP_CHOICE":
                if ($this->trump != "NULL") {
                    $this->advanceTurn($this->dealer);
                    $this->advanceGameStage();
                }
                break;
            case "PLAYING":
                if (count($this->cardsInPlay) < $this->noOfPlayers) { //not everyone has laid
                    if ($this->players[$this->turn]->playerCardInPlay) { //if the player whose turn it is has laid.
                        $this->advanceTurn($this->turn);
                        break;
                    }
                } else { //everyone has laid
                    $bestCard = $this->cardsInPlay[0];
                    foreach ($this->cardsInPlay as $card) { //find the bestCard laid
                        if ($bestCard[2] == $this->trump) { //if bestcard is a trump card, only higher trumps will beat it
                            if ($card[2] == $this->trump && $card[1] > $bestCard[1]) {
                                $bestCard = $card;
                            }
                        } else if ($card[2] == $bestCard[2] && $card[1] > $bestCard[1]) { // if the card is following suit, and is higher value
                            $bestCard = $card;
                        } else {
                            //throwing away card
                        }
                    }
                    foreach ($this->players as $player) { //find who laid the bestCard
                        if ($player->playerCardInPlay == $bestCard) {
                            $player->playerTricksWon += 1; // add one to the players score

                            if (count($player->playerHand) != 0) { //if there are still cards to lay
                                foreach ($this->players as $player) {
                                    $player->playerCardInPlay = NULL;
                                }
                                $this->cardsInPlay = []; // zero out the array for next round of hands
                                $this->setTurn($player->playerIndex); // winning player lays first (using setTurn as i have an explicit player index
                            } else {
                                //$this->updateScoreboard();
                                $this->nextRound();
                            }
                            break;
                        }
                    }
                }
                //find the winner.
                //give them the first lay next round
                //once all cards played, take scores
                //decrement cars to deal
                //advance to calling //clear playerCalls
                break;
            default:
                echo "ERROR in gameManager, game.php";
        }
    }

    public function nextRound() { //clears out all the calls, deals everyone a new hand
        foreach ($this->players as $player) {
            $player->newRound();
        }
        $this->round++; //TODO will overflow when reaching the end of last round needs its own func
        $this->advanceDealer();
        $this->advanceTurn($this->dealer);
        $tis->newDeal();
        $this->advanceGameStage();
        $this->updateGameHash();
    }

    public function advanceTurn($turn) {
        //$turn = $this->turn;
        $turn++;
        if ($turn > $this->noOfPlayers-1) {
            $turn = 0;
        }
        $this->setTurn($turn);
    }

    function setTurn($turn) {
        foreach ($this->players as $player) {
            $player->playerTurn = 0;
        }
        $this->turn = $turn;
        $this->players[$turn]->playerTurn = 1;
        $this->updateGameHash();
    }

    public function advanceDealer() {
        $dealer = $this->dealer;
        $dealer++;
        if ($dealer > $this->noOfPlayers-1) {
            $dealer = 0;
        }
        $this->dealer = $dealer;
        $this->updateGameHash();
    }

    public function advanceGameStage() {
        switch ($this->gameStage) {
            case "CALLING":
                $this->gameStage = "FIND_HIGHEST_CALL";
                break;
            case "FIND_HIGHEST_CALL":
                $this->gameStage = "WAITING_TRUMP_CHOICE";
                break;
            case "WAITING_TRUMP_CHOICE":
                $this->gameStage = "PLAYING";
                break;
            case "PLAYING":
                $this->trump = "NULL";
                $this->gameStage = "CALLING";
                break;
            default:
                echo "ERROR in advanceGameStage, game.php";
        }
        $this->updateGameHash();
    }

    function updateScoreboard() {

    }

    public function newDeal() {
        $this->deck = new Deck();
        foreach ($this->players as $player) {
            $player->playerHand = $this->deck->dealHand($this->hand[$this->round]);
        }
        $this->updateGameHash();
    }

}
?>
