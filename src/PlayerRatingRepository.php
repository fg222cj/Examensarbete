<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:47
 */

require_once(dirname(__FILE__) . '/Repository.php');
require_once(dirname(__FILE__) . '/PlayerRating.php');

class PlayerRatingRepository extends Repository {
    public function insert(PlayerRating $rating) {
        $db = $this->connection();

        $sql = "INSERT IGNORE INTO " . DBTABLEPLAYERRATINGS . " (" . DBCOLUMNMATCHID . ", " . DBCOLUMNPLAYERID . ", " .
                DBCOLUMNRATEDBYID . ", " . DBCOLUMNRATING . ") VALUES (?, ?, ?, ?)";
        $params = array($rating->getMatchID(), $rating->getPlayerID(), $rating->getRatedByID(), $rating->getRating());

        $query = $db->prepare($sql);
        $query->execute($params);
    }

    public function getLatestUnratedByPlayerID($playerID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERRATINGS . " WHERE " . DBCOLUMNRATEDBYID . "=? AND " . DBCOLUMNRATING . "=0
                GROUP BY " . DBCOLUMNMATCHID;
        $params = array($playerID);

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

    public function getByPlayerIDAndMatchID($playerID, $matchID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERS . " WHERE " . DBCOLUMNPLAYERID . "=? AND " . DBCOLUMNMATCHID . "=? LIMIT 5";
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