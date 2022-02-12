
$(document).ready(function() {
    isPlayerOne();
    refreshData();

});

function refreshData() {
    var pollTime = 5;  //Seconds
    updateLobby();
    pollGameStarted();
    setTimeout(refreshData, pollTime*1000);
}

function getURLParam(param) {
    const queryString = window.location.search;
    //console.log(queryString);
    const urlParams = new URLSearchParams(queryString);
    const result = urlParams.get(param);
    //console.log(product);
    return result;
}

function updateLobby() {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var param = "function=updateLobby&gameID=" + gameID + "&playerID=" + playerID;
    sendRequest(param);
}

function pollGameStarted() {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var param = "function=pollGameStarted&gameID=" + gameID + "&playerID=" + playerID;
    sendRequest(param);
}

function isPlayerOne() {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var param = "function=isPlayerOne&gameID=" + gameID + "&playerID=" + playerID;
    sendRequest(param, "button");
}

function startGame() {
    var gameID = getURLParam("gameID");
    var playerID = getURLParam("playerID");
    var param = "function=startGame&gameID=" + gameID + "&playerID=" + playerID;
    sendRequest(param);
}

function setButton(buttonState) {
    if (buttonState == "READY") {
        $("#startButton").hide();
        $("#infoDiv").text("Awaiting Players... \n Player One will start the game once all players are present");
    } else {
        $("#startButton").show();
        $("#infoDiv").text("Once all players are shown click \"Start Game\"");
    }
}

function sendRequest(param, element) {
    //console.log("param= " + param);
    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';
    request.open("GET", "nommies.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            if (this.readyState == 4) {
                var resp = this.response;
                if (resp == "true") {
                    setButton("START");
                } else if (resp == "false") {
                    setButton("READY");
                } else if (resp == "Game Started") {
                    console.log("Game Started");
                    //window.location.replace();
                    var gameID = getURLParam("gameID");
                    var playerID = getURLParam("playerID");
                    window.location.href = "nommiesGame.php?gameID=" + gameID + "&playerID=" + playerID;
                } else if (resp == "Waiting for players") {
                    console.log("Waiting for players");
                } else {
                    console.log(resp);
                    var obj = JSON.parse(resp);
                    //console.log(obj);
                    var playersString = "Players:" + "</br>";
                    for (var i = 0; i < obj.length; i++) {
                        playersString += obj[i] + "</br>";
                    }
                    $("#playerDiv").html(playersString);
                }
            }
        };
    request.onerror =
        function() {
            //alert("Request failed");
            console.log("Request Failed");
            //document.getElementById("responseDiv").innerHTML = "Request Failed";

        };
}
