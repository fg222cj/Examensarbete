<?php

/**
 * Created by Joakim Nilsson.
 * Date: 17/02/2016
 * Time: 11:24
 */
require_once(dirname(__FILE__) . "/Repository.php");

class userInformationRepository  extends Repository
{
    public function getAll(){
        $db = $this->connection();
        $sql= "SELECT * FROM " . DBTABLEUSERINFORMATION;

        $query = $db->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();
        $userInformations = array();
        foreach($result as $row) {
            $userInformation = new UserInformation($row[DBCOLUMNSTEAMID], $row[DBCOLUMNPERSONANAME], $row[DBCOLUMNPROFILEURL], $row[DBCOLUMNAVATARFULL]);
            $userInformations[] = $userInformation;
        }
        return $userInformations;

    }
    public function getUser($steam64){
        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEUSERINFORMATION . " WHERE " . DBCOLUMNSTEAMID . "=? LIMIT 1";
        $params = array($steam64);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();

        if(isset($result[0][DBCOLUMNSTEAMID])){
            $userInformation = new UserInformation($result[0][DBCOLUMNSTEAMID], $result[0][DBCOLUMNPERSONANAME], $result[0][DBCOLUMNPROFILEURL], $result[0][DBCOLUMNAVATARFULL]);

            return $userInformation;
        }
        return null;
    }

    public function insert(UserInformation $userInfo) {
        $db = $this->connection();
        $sql = "INSERT IGNORE INTO " . DBTABLEUSERINFORMATION . " (" . DBCOLUMNSTEAMID . ", " . DBCOLUMNPERSONANAME . ", " .
            DBCOLUMNPROFILEURL . ", " . DBCOLUMNAVATARFULL . ") VALUES (?, ?, ?, ?)";
        $params = array($userInfo->getSteamID(), $userInfo->getPersonaname(), $userInfo->getProfileurl(), $userInfo->getAvatarfull());
        $query = $db->prepare($sql);
        $query->execute($params);
    }

}

