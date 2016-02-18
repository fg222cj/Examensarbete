<?php

/**
 * Created by Joakim Nilsson.
 * Date: 18/02/2016
 * Time: 11:31
 */
class MatchHistory
{
        private $accountID;
        private $playerSlot;
        private $heroID;

    /**
     * MatchHistory constructor.
     * @param $accountID
     * @param $playerSlot
     * @param $heroID
     */
    public function __construct($accountID, $playerSlot, $heroID)
    {
        $this->accountID = $accountID;
        $this->playerSlot = $playerSlot;
        $this->heroID = $heroID;
    }

    /**
     * @return mixed
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @param mixed $accountID
     */
    public function setAccountID($accountID)
    {
        $this->accountID = $accountID;
    }

    /**
     * @return mixed
     */
    public function getPlayerSlot()
    {
        return $this->playerSlot;
    }

    /**
     * @param mixed $playerSlot
     */
    public function setPlayerSlot($playerSlot)
    {
        $this->playerSlot = $playerSlot;
    }

    /**
     * @return mixed
     */
    public function getHeroID()
    {
        return $this->heroID;
    }

    /**
     * @param mixed $heroID
     */
    public function setHeroID($heroID)
    {
        $this->heroID = $heroID;
    }
}