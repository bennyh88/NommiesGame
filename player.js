class Player {
    //fixed on startup
    playerID; //unique id for the player, used for players URL
    playerName; //nickname on screen
    playerOrder = []; //an array that holds position of other players at the table relative to the client
    playerIndex;//this players instance array index in gameOBJ

    //variable
    playerTurn;
    playerCall;
    playerTricksWon;
    playerHand; //an array containing the players cards, only clients cards are sent to the client.
    playerCardInPlay;

    constructor(obj) {
        this.playerID = obj.playerID;
        this.playerName = obj.playerName;
        this.playerOrder = obj.playerOrder;
        this.playerIndex = obj.playerIndex;

        this.playerTurn = obj.playerTurn;
        this.playerCall = obj.playerCall;
        this.playerTricksWon = obj.playerTricksWon;
        if (obj.playerHand) { // if defined, assign it.
            this.playerHand = obj.playerHand;
        }
        if (obj.playerCardInPlay) { // if defined, assign it.
            this.playerCardInPlay = obj.playerCardInPlay;
        } else {
            this.playerCardInPlay = 0;
        }

        //console.log("player created!");
    }

    update(obj) {
        this.playerTurn = obj.playerTurn;
        this.playerCall = obj.playerCall;
        this.playerTricksWon = obj.playerTricksWon;
        if (obj.playerHand) { // if defined, assign it.
            this.playerHand = obj.playerHand;
        }
        if (obj.playerCardInPlay) { // if defined, assign it.
            this.playerCardInPlay = obj.playerCardInPlay;
        } else {
            this.playerCardInPlay = 0;
        }
        //console.log(obj.playerCardInPlay);
        //console.log("player" + this.playerIndex + "updated!");
    }

    test() {
        console.log("Success!");//Works!!
    }
}
