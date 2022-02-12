class Game {
    //fixed on startup
    id;
    players = []; //array of player objects
    thisPlayer; //reference to the client's player object.
    round = 8; //number of cards to be dealt 8?

    //variable
    hand; //how many dealt this time
    dealer;
    turn;
    gameStage; //calling,choosing trump or playing
    //deck; //array of cards
    oldGameHash = "HASH";
    cardsInPlay = [];

    constructor(obj, thisPlayerID) {
        this.id = obj.id;

        this.round = obj.round;
        this.hand = obj.hand; //how many dealt this time
        this.dealer = obj.dealer;
        this.turn = obj.turn;
        this.gameStage = obj.gameStage;
        //this.deck = obj.deck; //array of cards
        this.oldGameHash = obj.gameHash; //used to keep client in synch with server
        this.cardsInPlay = obj.cardsInPlay;

        for (var i=0; i<obj.players.length; i++) {
            this.players[i] = new Player(obj.players[i]);
            if (this.players[i].playerID == thisPlayerID) {
                this.thisPlayer = this.players[i];
                //console.log("thisPlayer Defined");
            }
        }



        //console.log("gameOBJ created!");
    }

    update(obj) {
        this.hand = obj.hand; //how many dealt this time
        this.round = obj.round;
        this.dealer = obj.dealer;
        this.turn = obj.turn;
        this.gameStage = obj.gameStage;
        //this.deck = obj.deck; //array of cards
        //gameStarted = 0;
        this.oldGameHash = obj.gameHash; //used to keep client in synch with server
        this.cardsInPlay = obj.cardsInPlay;

        for (var i=0; i<obj.players.length; i++) {
            this.players[i].update(obj.players[i]);
            //console.log("thisPlayer Defined update");
        }

        //console.log("game updated!");
    }

    test() {
        console.log("gameOBJ created!");//Works!!
    }
}
