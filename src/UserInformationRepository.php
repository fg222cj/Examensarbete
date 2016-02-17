<?php

/**
 * Created by Joakim Nilsson.
 * Date: 17/02/2016
 * Time: 11:24
 */
require_once(dirname(__FILE__) . "/../../src/Repository.php");

class userInformationRepository  extends Repository
{
    public function getAll(){
        $db = $this->connection();
        $sql= "SELECT * FROM " . DBTABLEJOCKEUSERS;

        $query = $db->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();
        $userInformation = array();
        foreach($result as $row) {
            $userInformation = new UserInformation($row[DBCOLUMNSTEAMID], $row[DBCOLUMNPERSONANAME], $row[DBCOLUMNPROFILEURL], $row[DBCOLUMNAVATARFULL]);
            $userInformation[] = $userInformation;
        }
        return $userInformation;
    }

    public function insert(UserInformation $userInfo) {
        $db = $this->connection();
        $sql = "INSERT IGNORE INTO " . DBTABLEJOCKEUSERS . " (" . DBCOLUMNSTEAMID . ", " . DBCOLUMNPERSONANAME . ", " .
            DBCOLUMNPROFILEURL . ", " . DBCOLUMNAVATARFULL . ") VALUES (?, ?, ?, ?)";
        $params = array($userInfo->getSteamID(), $userInfo->getPersonaname(), $userInfo->getProfileurl(), $userInfo->getAvatarfull());
        $query = $db->prepare($sql);
        $query->execute($params);
    }

}

