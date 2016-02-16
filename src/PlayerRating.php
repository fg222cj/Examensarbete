<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:44
 * Description: Contains ratings for players. Each instance represents one rating given by one player to another player for one match.
 */

class PlayerRating {
    private $matchID;
    private $playerID;
    private $ratedByID;
    private $rating;

    public function __construct($matchID, $playerID, $ratedByID, $rating) {
        $this->matchID = $matchID;
        $this->playerID = $playerID;
        $this->ratedByID = $ratedByID;
        $this->rating = $rating;
    }

    public function getMatchID() {
        return $this->matchID;
    }

    public function getPlayerID() {
        return $this->playerID;
    }

    public function getRatedByID() {
        return $this->ratedByID;
    }

    public function getRating() {
        return $this->rating;
    }
}