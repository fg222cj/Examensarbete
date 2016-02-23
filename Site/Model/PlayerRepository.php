<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:47
 */

require_once(dirname(__FILE__) . '/Repository.php');
require_once(dirname(__FILE__) . '/Player.php');

class PlayerRepository extends Repository {

    public function getAll() {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERS;

        $query = $db->prepare($sql);
        $query->execute();

        $result = $query->fetchAll();
        $players = array();

        foreach($result as $row) {
            $player = new Player($row[DBCOLUMNID], $row[DBCOLUMNACCOUNTID], $row[DBCOLUMNNAME], $row[DBCOLUMNSTEAMID64], $row[DBCOLUMNLOGINKEY]);
            $players[] = $player;
        }

        return $players;
    }

    public function getByID($ID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERS . " WHERE " . DBCOLUMNID . "=? LIMIT 1";
        $params = array($ID);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();

        if(isset($result[0][DBCOLUMNID])) {
            $player = new Player($result[0][DBCOLUMNID], $result[0][DBCOLUMNACCOUNTID], $result[0][DBCOLUMNNAME], $result[0][DBCOLUMNSTEAMID64], $result[0][DBCOLUMNLOGINKEY]);
            return $player;
        }

        return null;
    }

    public function getByAccountID($accountID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERS . " WHERE " . DBCOLUMNACCOUNTID . "=? LIMIT 1";
        $params = array($accountID);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();

        if(isset($result[0][DBCOLUMNID])) {
            $player = new Player($result[0][DBCOLUMNID], $result[0][DBCOLUMNACCOUNTID], $result[0][DBCOLUMNNAME], $result[0][DBCOLUMNSTEAMID64], $result[0][DBCOLUMNLOGINKEY]);
            return $player;
        }

        return null;
    }

    public function storeLoginKey(Player $player) {
        $db = $this->connection();

        $sql = "UPDATE " . DBTABLEPLAYERS . " SET " . DBCOLUMNLOGINKEY . "=? WHERE " . DBCOLUMNID . "=?";
        $params = array($player->getLoginKey(), $player->getID());

        $query = $db->prepare($sql);
        $query->execute($params);
    }

    public function storeSteamID64(Player $player) {
        $db = $this->connection();

        $sql = "UPDATE " . DBTABLEPLAYERS . " SET " . DBCOLUMNSTEAMID64 . "=? WHERE " . DBCOLUMNID . "=?";
        $params = array($player->getSteamID64(), $player->getID());

        $query = $db->prepare($sql);
        $query->execute($params);
    }
}