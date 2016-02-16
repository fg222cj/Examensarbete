<?php
include "openid.php";


/**
 * Class Login
 */
class Login
{

    /**
     * @var
     */
    private $openID;
    /**
     * @var string
     */
    private $key = "DE32860E362CE20EEB23A66C5E39260F";

    /**
     * @param $URL
     * @return mixed
     */
    function get_contents($URL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * @param LightOpenID $openId
     */
    function setOpenId(LightOpenID $openId)
    {
        $this->openID = $openId;
    }


    /**
     * openId magic
     */
    function hasButtonBeenPressed()
    {
        $this->openID->identity = "http://steamcommunity.com/openid";
        header("Location: {$this->openID->authUrl()}");
    }


    /**
     *
     */
    function writeToFile()
    {
        $_SESSION['T2SteamAuth'] = $this->openID->validate() ? $this->openID->identity : null;
        $_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);

        if ($_SESSION['T2SteamAuth'] !== null) {


            $Steam64 = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);
            $profile = $this->get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$Steam64}");

            $items = file_get_contents("http://steamcommunity.com/profiles/{$Steam64}/inventory/json/730/2");
            $buffer = fopen("../Resources/{$Steam64}.json", "w+");
            $buffer2 = fopen("../Resources/{$Steam64}i.json", "w+");
            fwrite($buffer, $profile);
            fwrite($buffer2, $items);
            fclose($buffer);
            fclose($buffer2);

        }

        header("Location: index.php");

    }


    /**
     * If user logs out clear session.
     */
    function ifLogout()
    {
        unset($_SESSION['T2SteamAuth']);
        unset($_SESSION['T2SteamID64']);
        header("Location: index.php");

    }

    /**
     * Logim/out button
     * @return string
     */
    function ifLoggedInPresentLogoutBtn()
    {
        return "<div id=\"login\"><a href=\"?logout\">Logout</a></div>";
    }

    /**
     * @return string
     */
    function ifLoggedInPresentPlayerInformation()
    {
        $steam_profile = json_decode(file_get_contents("../Resources/{$_SESSION["T2SteamID64"]}.json"));
        $accID = $this->calculateAccountID($steam_profile->response->players[0]->steamid);

        return
            "
            <!DOCTYPE html>
            <html>
            <head>
                <link rel=\"stylesheet\"  href=\"../mainCss.css\">
            </head>
            <body>
        <header>
        <h1>Welcome {$steam_profile->response->players[0]->personaname} <img src=\"{$steam_profile->response->players[0]->avatarfull}\" width=60px height=60px/></h1>
        </header>

            <article>


                <p>
                    Your profile url : <a target='_blank' href={$steam_profile->response->players[0]->profileurl}>{$steam_profile->response->players[0]->profileurl}</a><br>
                    Your dotabuff url : <a target='_blank' href=http://www.dotabuff.com/players/{$accID}>http://www.dotabuff.com/players/{$accID}</a><br>

                </p>
 </article>
            </body>

        ";
//</html><a href=\"../myProfile.php\"><img src=\"{$steam_profile->response->players[0]->avatarfull}\" width=60px height=60px/></a>
    }
    function calculateAccountID($steam64){
        return $steam64 - 76561197960265728;
    }
    /**
     * If user not logged in present login button
     * @return string
     */
    function ifNotLoggedIn()
    {
        return "<div id=\"login\"><a href=\"?login\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\"/></a></div>";
    }
}