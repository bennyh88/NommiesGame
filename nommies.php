<?php
    include 'commonFunctions.php';

    loadParams();
    //var_dump($payload);

    if ($function != "createGame" and $function != "pollForUpdates") {
        unpackGame();
    }

    switch ($function) {
        case "createGame":
            //echo "createGame";
            createGame();
            break;
        case "joinGame":
            //echo "joinGame";
            joinGame();
            $gameOBJ->updateGameHash();
            break;
        case "updateLobby":
            updateLobby();
            break;
        case "startGame":
            $gameOBJ->startGame();
            $gameOBJ->setupGame();
            $gameOBJ->gameManager();
            $gameOBJ->updateGameHash();
            break;
        case "pollGameStarted":
            pollGameStarted();
            break;
        case "isPlayerOne": //TODO where is this called??
            isPlayerOne();
            break;
        case "pollForUpdates":
            pollForUpdates();
            break;
        case "submitCall":
            submitCall();
            $gameOBJ->newDeal();
            $gameOBJ->gameManager();
            $gameOBJ->updateGameHash();
            updateGame();
            break;
        case "submitMove":
            submitMove();
            $gameOBJ->gameManager();
            $gameOBJ->updateGameHash();
            updateGame();
            break;
        case "submitTrump":
            submitTrump();
            $gameOBJ->gameManager();
            $gameOBJ->updateGameHash();
            updateGame();
            break;
        default:
           echo "not a valid param";
           rejectUser();
    }

    packGame();
    closeDBConnection();
    exit();
?>
