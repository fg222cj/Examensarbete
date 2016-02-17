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
    private $Steam64;
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


            $this->Steam64 = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);
            $profile = $this->get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$this->Steam64}");

            $buffer = fopen("../Resources/{$this->Steam64}ID.json", "w+");
            fwrite($buffer, $profile);
            fclose($buffer);

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
        return "<div id=\"login\">
                 <a href=\"?logout\">Logout</a>
                </div>";
    }

    /**
     * @param $accID
     */
    function matchHistory($accID){
        $matchHistory = file_get_contents("https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key={$this->key}&account_id={$accID}");
        $buffer2 = fopen("../Resources/Match_history.json", "w+");
        fwrite($buffer2, $matchHistory);
        fclose($buffer2);
    }

    /**
     * @param $steamID
     */
    function playerInfo($steamID){
        $profile = $this->get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$steamID}");
        $profile .=",";
        $buffer = fopen("../Resources/Players.json", "a+");
        fwrite($buffer, $profile);
        fclose($buffer);
    }
    /**
     * @return string
     */
    function ifLoggedInPresentPlayerInformation()
    {
        $steam_profile = json_decode(file_get_contents("../Resources/{$_SESSION["T2SteamID64"]}ID.json"));
        $match_history = json_decode(file_get_contents("../Resources/Match_history.json"));
        $match_history_players = json_decode(file_get_contents("../Resources/Players.json"));
        $profileUrl = $steam_profile->response->players[0]->profileurl;
        $nickname = $steam_profile->response->players[0]->personaname;
        $avatar = $steam_profile->response->players[0]->avatarfull;
        $avatars = "";
        $accID = $this->calculateAccountID($steam_profile->response->players[0]->steamid);

        $this->matchHistory($accID);


        $latestMatchID ="";
        for($i = 0; $i <= 9; $i++){
            $steamID = $this->calculateSteamID($match_history->result->matches[0]->players[$i]->account_id);
            $this->playerInfo($steamID);

            $latestMatchID .=  nl2br("\n".$match_history_players->response->players[$i]->steamid . " : " . $match_history->result->matches[0]->players[$i]->hero_id);
        }
        // "<img src=\"{$match_history_players->response->players[0]->account_id}\" width=60px height=60px/>"
        return
            "
            <!DOCTYPE html>
            <html>
            <head>
                <link rel=\"stylesheet\"  href=\"../mainCss.css\">
            </head>
            <body>
        <header>
        <h1>Welcome {$nickname} <img src=\"{$avatar}\" width=60px height=60px/></h1>
        </header>

            <article>
                <p>
                    Your profile url : <a target='_blank' href={$profileUrl}>{$profileUrl}</a><br>
                    Your dotabuff url : <a target='_blank' href=http://www.dotabuff.com/players/{$accID}>http://www.dotabuff.com/players/{$accID}</a><br>

                    Latest match : $latestMatchID



                </p>
 </article>
            </body>

        ";

    }
    function calculateAccountID($steam64){
        return $steam64 - 76561197960265728;
    }
    function calculateSteamID($steam32){
        return $steam32 + 76561197960265728;
    }
    /**
     * If user not logged in present login button
     * @return string
     */
    function ifNotLoggedIn()
    {
        return "<div id=\"login\">
                    <a href=\"?login\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\"/></a>
               </div>";
    }
}