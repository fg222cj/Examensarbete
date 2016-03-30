<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:47
 */

require_once(dirname(__FILE__) . '/Repository.php');
require_once(dirname(__FILE__) . '/PlayerRating.php');
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

class PlayerRatingRepository extends Repository {
    public function insert(PlayerRating $rating) {
        $db = $this->connection();

        $sql = "INSERT IGNORE INTO " . DBTABLEPLAYERRATINGS . " (" . DBCOLUMNMATCHID . ", " . DBCOLUMNPLAYERID . ", " .
                DBCOLUMNRATEDBYID . ", " . DBCOLUMNRATING . ") VALUES (?, ?, ?, ?)";
        $params = array($rating->getMatchID(), $rating->getPlayerID(), $rating->getRatedByID(), $rating->getRating());

        $query = $db->prepare($sql);
        $query->execute($params);
    }

    public function update(PlayerRating $rating) {
        $db = $this->connection();

        $sql = "UPDATE " . DBTABLEPLAYERRATINGS . " SET " . DBCOLUMNRATING . "=? WHERE " . DBCOLUMNMATCHID . "=? AND " .
                DBCOLUMNPLAYERID . "=? AND " . DBCOLUMNRATEDBYID . "=?";
        $params = array($rating->getRating(), $rating->getMatchID(), $rating->getPlayerID(), $rating->getRatedByID());

        $query = $db->prepare($sql);
        $query->execute($params);
    }

    public function getAll() {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERRATINGS;

        $query = $db->prepare($sql);
        $query->execute();

        $result = $query->fetchAll();

        $ratings = array();

        foreach($result as $row) {
            $ratings[] = new PlayerRating($row[DBCOLUMNMATCHID], $row[DBCOLUMNPLAYERID], $row[DBCOLUMNRATEDBYID], $row[DBCOLUMNRATING]);
        }

        return $ratings;
    }

    /*
     * Fetches the ID of the latest match the player has participated in but not yet rated.
     */
    private function getLatestMatchIDByPlayerID($playerID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERRATINGS . " WHERE " . DBCOLUMNRATEDBYID . "=? GROUP BY " . DBCOLUMNMATCHID;
        $params = array($playerID);

        $query = $db->prepare($sql);
        $query->execute($params);

        $result = $query->fetchAll();
        $matchMapper = new Dota2Api\Mappers\MatchMapperDb();
        $matches = array();
        foreach($result as $row) {
            $match = $matchMapper->load($row[DBCOLUMNMATCHID]);
            $matches[] = $match;
        }

        $matchID = 0;
        $matchSeqNum = 0;
        foreach($matches as $match) {
            if($match->get('match_seq_num') > $matchSeqNum) {
                $matchSeqNum = $match->get('match_seq_num');
                $matchID = $match->get('match_id');
            }
        }
        return $matchID;
    }

    public function getLatestUnratedByPlayerID($playerID) {
        $db = $this->connection();

        $matchID = $this->getLatestMatchIDByPlayerID($playerID);


        $sql = "SELECT * FROM " . DBTABLEPLAYERRATINGS . " WHERE " . DBCOLUMNRATEDBYID . "=? AND " . DBCOLUMNMATCHID . "=?";
        $params = array($playerID, $matchID);

        $query = $db->prepare($sql);
        $query->execute($params);

        $result = $query->fetchAll();

        $ratings = array();

        // If player has finished rating all other players in the match, return null
        $numberOfRated = 0;
        for($i = 0; $i < count($result); $i++) {
            if($result[$i][DBCOLUMNRATING] != 0) {
                $numberOfRated++;
            }
        }

        if($numberOfRated == count($result)) {
            return null;
        }

        // Otherwise, return an array of all ratings
        foreach($result as $row) {
            $rating = new PlayerRating($row[DBCOLUMNMATCHID], $row[DBCOLUMNPLAYERID], $row[DBCOLUMNRATEDBYID], $row[DBCOLUMNRATING]);
            $ratings[] = $rating;
        }

        return $ratings;
    }

    public function getByPlayerIDAndMatchID($playerID, $matchID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERRATINGS . " WHERE " . DBCOLUMNPLAYERID . "=? AND " . DBCOLUMNMATCHID . "=? LIMIT 5";
        $params = array($playerID, $matchID);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();
        $ratings = array();
        foreach($result as $row) {
            $rating = new PlayerRating($row[DBCOLUMNMATCHID], $row[DBCOLUMNPLAYERID], $row[DBCOLUMNRATEDBYID], $row[DBCOLUMNRATING]);
            $ratings[] = $rating;
        }

        return $ratings;
    }
}