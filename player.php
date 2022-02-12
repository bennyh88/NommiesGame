<?php
class Player {
    public $playerID; //unique id for the player, used for players URL
    public $playerName; //nickname on screen
    public $playerIndex; //this players index within array of player objects.
    public $playerOrder; //how other players would appear at the table

    public $playerCall;
    public $playerTricksWon;
    public $playerHand; //an array containing the players cards
    public $playerTurn = FALSE;
    public $playerCardInPlay;

    function loadData($playerData) {
        $this->playerID = $playerData->playerID;
        $this->playerName = $playerData->playerName;
        $this->playerHand = $playerData->playerHand;
        $this->playerCall = $playerData->playerCall;
        $this->playerTricksWon = $playerData->playerTricksWon;
        $this->playerOrder = $playerData->playerOrder;
        $this->playerIndex = $playerData->playerIndex;
        $this->playerTurn = $playerData->playerTurn;
        $this->playerCardInPlay = $playerData->playerCardInPlay;

    }

    public function newRound() {
        $this->playerCall = NULL;
        $this->playerTricksWon = 0;
        $this->playerHand = [];
        $this->playerCardInPlay = NULL;

    }
}

?>
