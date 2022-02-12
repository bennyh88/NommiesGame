var gameOBJ;
var thisPlayer; // put in to neaten up code??
$(document).ready(function() {
    refreshData();
});

function refreshData() {
    pollForUpdates();
    var pollTime = 5;  //Seconds TODO change back to 5 sec
    setTimeout(refreshData, pollTime*1000);
}

function pollForUpdates() { //send request to server
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    //console.log(oldGameHash);
    var payload;
    if (gameOBJ) {
        payload = gameOBJ.oldGameHash;
    } else {
        payload = "HASH";
    }
    var param = "function=pollForUpdates&gameID=" + gameID + "&playerID=" + playerID + "&oldGameHash=" + payload;

    sendRequest(param);
}

function updateGame(resp) { //copy the data from the response into the global gameOBJ.
    if (!gameOBJ) { //if gameOBJ is not yet defined (when page first loads)
        //console.log("gameOBJ is undefined");
        var obj = JSON.parse(resp);
        gameOBJ = new Game(obj, getURLParam("playerID"));
        //gameOBJ.test();
        thisPlayer = gameOBJ.thisPlayer;
    }

    var obj = JSON.parse(resp);
    gameOBJ.update(obj);
    //oldGameHash = obj.gameHash; done in gameOBJ.update();
    updateDisplay();
}

function updateDisplay() {
    updateCallModal();
    updateTurnIndication();
    updatePlayerHand();
    //updateTricksToWin();
    updateCardsPlayed();
}

function updateCallModal() {
    console.log("updateCallModal");
    console.log("player turn = " + thisPlayer.playerTurn);
    if (thisPlayer.playerTurn && gameOBJ.gameStage == "CALLING") { //if its clients turn to call, and its the CALLING stage of game
        const div = document.getElementById("callModalDiv");
        div.textContent = '';
        for (var i=0; i<gameOBJ.hand[gameOBJ.round]+1; i++) {
            var button = document.createElement("BUTTON");
            button.setAttribute("onclick", "submitCall(" + i + ")");
            button.innerHTML = i;
            div.appendChild(button);
        }
        showChooseCallModal();
    }
}

function updateTurnIndication() {
    //set them all to green
    var elements = document.getElementsByClassName("playerName");
    for(var i=0; i<elements.length; i++) {
        elements[i].style.borderColor="#616F39";
    }
    //set the turn indicator to yellow name0
    var element = "name" + gameOBJ.turn;
    element = document.getElementById(element);
    element.style.borderColor="yellow";
}

function updatePlayerHand() { //array("2C.svg", 0, "C"),array("card.svg", value, "suit")
    //console.log(thisPlayer.playerHand);
    thisPlayer.playerHand = sortHand(thisPlayer.playerHand);
    const div = document.getElementById("playerHand");
    div.textContent = '';
    for (var i=0; i<thisPlayer.playerHand.length; i++) {
        var card = document.createElement("IMG");
        card.setAttribute("class", "card");
        card.setAttribute("src", "cards/" + thisPlayer.playerHand[i][0]);
        card.setAttribute("ondragstart", "drag(event)");
        card.setAttribute("id", thisPlayer.playerHand[i][3]);
        div.appendChild(card);
        //console.log("childAppended");
        //<img class='card' src='cards/2S.svg' ondragstart="drag(event)" id='2S'>
    }
}

function sortHand(hand) {
    var switched = 0;
    for (var i=0; i<hand.length-1; i++) {
        if (hand[i][3] > hand[i+1][3]) {
            var temp = hand[i+1];
            hand[i+1] = hand[i];
            hand[i] = temp;
            switched++;
        }
    }
    if (switched) {
        hand = sortHand(hand);
    }
    return hand;

}

function updateCardsPlayed() {
    console.log("updateCardsPlayed()");
    const div = document.getElementById("playedCardDiv");
    div.textContent = '';
    if (thisPlayer.playerCardInPlay && thisPlayer.playerCardInPlay != "ERROR") {
        //console.log("playerCardInPlay = " + thisPlayer.playerCardInPlay);
        var card = document.createElement("IMG");
        card.setAttribute("class", "card");
        card.setAttribute("src", "cards/" + thisPlayer.playerCardInPlay[0]);
        div.appendChild(card);
    } else {
        var card = document.createElement("IMG");
        card.setAttribute("class", "card");
        card.setAttribute("src", "cards/RED_BACK.svg");
        card.setAttribute("style", "visibility:hidden;");
        div.appendChild(card);
    }

    for (var i=0; i<gameOBJ.players.length; i++) {
        if (i != thisPlayer.playerIndex) {
            const div = document.getElementById("opponentCard" + i);
            div.textContent = '';
            if (gameOBJ.players[i].playerCardInPlay != false) {
                var card = document.createElement("IMG");
                card.setAttribute("class", "card");
                card.setAttribute("src", "cards/" + gameOBJ.players[i].playerCardInPlay[0]);
                div.appendChild(card);
            } else {
                var card = document.createElement("IMG");
                card.setAttribute("class", "card");
                card.setAttribute("src", "cards/RED_BACK.svg");
                card.setAttribute("style", "visibility:hidden;");
                div.appendChild(card);
            }
        }
    }

}

function submitMove(move) {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var payload = move;
    var param = "function=submitMove&gameID=" + gameID + "&playerID=" + playerID + "&payload=" + payload;
    sendRequest(param);
}

function submitCall(call) {
    console.log("you chose; " + call);
    thisPlayer.playerTurn = false;
    hideChooseCallModal();
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var payload = JSON.stringify(call);
    var param = "function=submitCall&gameID=" + gameID + "&playerID=" + playerID + "&payload=" + payload;
    //call = call.toString();
    //var param = "function=submitCall&gameID=" + gameID + "&playerID=" + playerID + "&payload=" + call;
    sendRequest(param);

}

function submitTrump() {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var payload = "payload"
    var param = "function=submitTrump&gameID=" + gameID + "&playerID=" + playerID + "&payload=" + payload;
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text"); //data is cards "score"
    console.log(data);
    if (moveAllowed(data)) {
        var card = document.getElementById(data);
        card.style.display = "none";

        var playedCard = document.createElement("IMG");
        playedCard.setAttribute("class", "card");
        playedCard.setAttribute("src", card.getAttribute("src"));
        //playedCard.setAttribute("ondragstart", "drag(event)");
        playedCard.setAttribute("id", card.getAttribute("id"));

        var div = document.getElementById("playedCardDiv");
        div.appendChild(playedCard);

        thisPlayer.playerTurn = false;
        thisPlayer.playerCardInPlay = data;
        submitMove(data);
    }

}

function moveAllowed(data) {
    if (thisPlayer.playerTurn) {
        if (gameOBJ.cardsInPlay.length == 0) {
            return true;
        }
        var firstCardSuit = gameOBJ.cardsInPlay[0][2];
        //get data for the card played
        var cardSuit;

        for (var i=0; i<thisPlayer.playerHand.length; i++) {
            if (thisPlayer.playerHand[i][3] == data) {
                cardSuit = thisPlayer.playerHand[i][2];
                break;
            }
        }

        if (cardSuit == firstCardSuit) {
            return true;
        } else if (handContainsSuit(firstCardSuit)) {
            return false;
        } else {
            return true;
        }

    } else {
        return false;
    }
}

function handContainsSuit(suit) {
    for (var i=0; i<thisPlayer.playerHand.length; i++) {
        if (thisPlayer.playerHand[i][2] == suit){
            return true;
        }
    }
    return false;
}

function sendRequest(param) {
    //console.log("param= " + param);
    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';//TODO is JSON better?
    request.open("GET", "nommies.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            if (this.readyState == 4) {
                var resp = this.response;
                console.log(resp);
                if (resp != "false") {
                    updateGame(resp);
                }
            }
        };
    request.onerror =
        function() {
            console.log("Request Failed");
        };
}

function getURLParam(param) {
    const queryString = window.location.search;
    //console.log(queryString);
    const urlParams = new URLSearchParams(queryString);
    const result = urlParams.get(param);
    //console.log(result);
    return result;
}

function showChooseCallModal() {
    var chooseCallModal = document.getElementById("chooseCallModal");
    chooseCallModal.style.display = "block";
}

function hideChooseCallModal() {
    var chooseCallModal = document.getElementById("chooseCallModal");
    chooseCallModal.style.display = "none";
}
