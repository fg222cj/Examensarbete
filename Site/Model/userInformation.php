<?php

/**
 * Created by Joakim Nilsson.
 * Date: 17/02/2016
 * Time: 11:17
 */

class userInformation
{
    private $steamID;
    private $personaname;
    private $profileurl;
    private $avatarfull;

    public function __construct($steamID, $personaname, $profileurl, $avatarfull) {
        $this->steamID = $steamID;
        $this->personaname = $personaname;
        $this->profileurl = $profileurl;
        $this->avatarfull = $avatarfull;
    }


    /**
     * @return mixed
     */
    public function getSteamID()
    {
        return $this->steamID;
    }

    /**
     * @param mixed $steamID
     */
    public function setSteamID($steamID)
    {
        $this->steamID = $steamID;
    }

    /**
     * @return mixed
     */
    public function getPersonaname()
    {
        return $this->personaname;
    }

    /**
     * @param mixed $personaname
     */
    public function setPersonaname($personaname)
    {
        $this->personaname = $personaname;
    }

    /**
     * @return mixed
     */
    public function getProfileurl()
    {
        return $this->profileurl;
    }

    /**
     * @param mixed $profileurl
     */
    public function setProfileurl($profileurl)
    {
        $this->profileurl = $profileurl;
    }

    /**
     * @return mixed
     */
    public function getAvatarfull()
    {
        return $this->avatarfull;
    }

    /**
     * @param mixed $avatarfull
     */
    public function setAvatarfull($avatarfull)
    {
        $this->avatarfull = $avatarfull;
    }
}