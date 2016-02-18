<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:44
 */

class Player {
    private $ID;
    private $accountID;
    private $name;
    private $loginKey;
    private $steamID64;

    public function __construct($ID = 0, $accountID = 0, $name = "", $steamID64="", $loginKey = "") {
        $this->ID = $ID;
        $this->accountID = $accountID;
        $this->name = $name;
        $this->steamID64 = $steamID64;
        $this->loginKey = $loginKey;
    }

    public function getID() {
        return $this->ID;
    }

    public function getAccountID() {
        return $this->accountID;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSteamID64()
    {
        return $this->steamID64;
    }

    /**
     * @param mixed $steamID64
     */
    public function setSteamID64($steamID64)
    {
        $this->steamID64 = $steamID64;
    }

    public function getLoginKey() {
        return $this->loginKey;
    }

    /**
     * @param string $loginKey
     */
    public function setLoginKey($loginKey)
    {
        $this->loginKey = $loginKey;
    }


}