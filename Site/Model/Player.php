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

    public function __construct($ID = 0, $accountID = 0, $name = "") {
        $this->ID = $ID;
        $this->accountID = $accountID;
        $this->name = $name;
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
}