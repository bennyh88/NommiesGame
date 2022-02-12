<?php
class Deck {

    public $deck = array (
        //array("card.svg", value, "suit", sort,
        array("2C.svg", 0, "C", 1),
        array("3C.svg", 1, "C", 2),
        array("4C.svg", 2, "C", 3),
        array("5C.svg", 3, "C", 4),
        array("6C.svg", 4, "C", 5),
        array("7C.svg", 5, "C", 6),
        array("8C.svg", 6, "C", 7),
        array("9C.svg", 7, "C", 8),
        array("10C.svg", 8, "C", 9),
        array("JC.svg", 9, "C", 10),
        array("QC.svg", 10, "C", 11),
        array("KC.svg", 11, "C", 12),
        array("AC.svg", 12, "C", 13),

        array("2H.svg", 0, "H", 14),
        array("3H.svg", 1, "H", 15),
        array("4H.svg", 2, "H", 16),
        array("5H.svg", 3, "H", 17),
        array("6H.svg", 4, "H", 18),
        array("7H.svg", 5, "H", 19),
        array("8H.svg", 6, "H", 20),
        array("9H.svg", 7, "H", 21),
        array("10H.svg", 8, "H", 22),
        array("JH.svg", 9, "H", 23),
        array("QH.svg", 10, "H", 24),
        array("KH.svg", 11, "H", 25),
        array("AH.svg", 12, "H", 26),

        array("2S.svg", 0, "S", 27),
        array("3S.svg", 1, "S", 28),
        array("4S.svg", 2, "S", 29),
        array("5S.svg", 3, "S", 30),
        array("6S.svg", 4, "S", 31),
        array("7S.svg", 5, "S", 32),
        array("8S.svg", 6, "S", 33),
        array("9S.svg", 7, "S", 34),
        array("10S.svg", 8, "S", 35),
        array("JS.svg", 9, "S", 36),
        array("QS.svg", 10, "S", 37),
        array("KS.svg", 11, "S", 38),
        array("AS.svg", 12, "S", 39),

        array("2D.svg", 0, "D", 40),
        array("3D.svg", 1, "D", 41),
        array("4D.svg", 2, "D", 42),
        array("5D.svg", 3, "D", 43),
        array("6D.svg", 4, "D", 44),
        array("7D.svg", 5, "D", 45),
        array("8D.svg", 6, "D", 46),
        array("9D.svg", 7, "D", 47),
        array("10D.svg", 8, "D", 48),
        array("JD.svg", 9, "D", 49),
        array("QD.svg", 10, "D", 50),
        array("KD.svg", 11, "D", 51),
        array("AD.svg", 12, "D", 52)
    );

    public $tempDeck = [];

    public function loadData($data) {
        $this->tempDeck = $data->tempDeck;
        $this->deck = $data->deck;
        //echo("deck, loadData()");
        //var_dump($data);
    }

    public function dealHand($noOfCards) { // return an array of cards
        $hand = [];
        for ($i=0; $i<$noOfCards; $i++) {
            shuffle($this->deck);
            $temp = array_pop($this->deck);
            array_push($hand, $temp);
            array_push($this->tempDeck, $temp); //store a copy of all cards dealt to help with finding and moving deck objects.
        }
        //$hand = $this->sortHand($hand);
        //var_dump($tempDeck);
        return $hand;
    }

    function sortHand($hand) { //sort in the browser instead.
        //first sort by value.
        $temp = [];
        for ($i=0; $i<count($hand); $i++) {
            if ($hand[$i][3] > $hand[$i+1][3]) {
                //swap
                $temp = $hand[$i+1];
                $hand[$i+1] = $hand[$i];
                $hand[$i] = $temp;
                $i = 0;
            }
        }
        return $hand;
    }

    public function getCardfromId($id) {

        for ($i=0; $i<count($this->tempDeck); $i++) {
            if ($this->tempDeck[$i][3] == $id) {
                return $this->tempDeck[$i];
            }
        }
        return "ERROR";
    }


}
?>
