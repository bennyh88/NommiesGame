function createGame() {
    var playerName = document.getElementById("nameCGM").value;
    var param = "function=createGame&playerName=" + playerName;
    sendRequest(param);
}

function joinGame() {
    var playerName = document.getElementById("nameJGM").value;
    var gameID = document.getElementById("gameID").value;
    var param = "function=joinGame&gameID=" + gameID + "&playerName=" + playerName;
    sendRequest(param);
}

function sendRequest(param, element) {
    console.log("param= " + param);
    var request = new XMLHttpRequest();
    request.withCredentials = true;
    request.responseType = 'text';
    request.open("GET", "nommies.php?" + param, true);
    request.send();
    request.onreadystatechange =
        function() {
            if (this.readyState == 4){
                var resp = this.response;
                //document.getElementById(element).innerHTML = resp;
                //console.log("response = " + resp);
                console.log(resp);
                var obj = JSON.parse(resp);
                //console.log(obj);
                //console.log("gameID = " + obj.gameID);
                //console.log("playerID = " + obj.playerID);
                window.location.href = "lobby.php?gameID=" + obj.gameID + "&playerID=" + obj.playerID;
            }
            // failure is unhandled!!!
        };
    request.onerror =
        function() {
            //alert("Request failed");
            console.log("Request Failed");
            //document.getElementById("responseDiv").innerHTML = "Request Failed";

        };
}

function launchCreateModal() {
    var createModal = document.getElementById("createGameModal");
    createModal.style.display = "block";
}

function launchJoinModal() {
    var joinModal = document.getElementById("joinGameModal");
    joinModal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
function closeModal() {
    var createModal = document.getElementById("createGameModal");
    createModal.style.display = "none";
    var joinModal = document.getElementById("joinGameModal");
    joinModal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
//window.onclick = function(event) {
//
//  if (event.target == modal) {
//    closeModal();
//  }
//}
