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
            $player = new Player($row[DBCOLUMNID], $row[DBCOLUMNACCOUNTID], $row[DBCOLUMNNAME]);
            $players[] = $player;
        }

        return $players;
    }

    public function getByAccountID($accountID) {
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEPLAYERS . " WHERE " . DBCOLUMNACCOUNTID . "=? LIMIT 1";
        $params = array($accountID);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();

        if(isset($result[0][DBCOLUMNID])) {
            $player = new Player($result[0][DBCOLUMNID], $result[0][DBCOLUMNACCOUNTID], $result[0][DBCOLUMNNAME]);
            return $player;
        }

        return null;
    }
}