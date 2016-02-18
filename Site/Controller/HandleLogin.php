<?php
require_once(dirname(__FILE__) . "/../Model/UserInformation.php");
require_once(dirname(__FILE__) . "/../Model/UserInformationRepository.php");
require_once(dirname(__FILE__) . "/../Model/MatchHistory.php");
require_once(dirname(__FILE__) . '/../Model/PlayerRepository.php');
require_once(dirname(__FILE__) . '/../Model/PlayerRatingRepository.php');
include(dirname(__FILE__) . "/openid.php");


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
     * @var
     */
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
    function storeUserLoginInfo()
    {
        $_SESSION['T2SteamAuth'] = $this->openID->validate() ? $this->openID->identity : null;
        $_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);

        if ($_SESSION['T2SteamAuth'] !== null) {
            $this->Steam64 = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);
        }

        $playerinfo = $this->playerInfo();
        $accID = $this->calculateAccountID($playerinfo->getSteamID());

        $playerRepository = new PlayerRepository();
        $player = $playerRepository->getByAccountID($accID);
        $key = $this->calculateLoginKey();
        $player->setLoginKey($key);
        $player->setSteamID64($_SESSION['T2SteamID64']);
        $playerRepository->storeLoginKey($player);
        $playerRepository->storeSteamID64($player);

        setcookie("LoginKey", $key, time()+604800);

        header("Location: index.php");

    }

    function cookieLogin() {
        if(isset($_COOKIE['LoginKey'])) {
            $playerRepository = new PlayerRepository();
            $players = $playerRepository->getAll();
            foreach ($players as $player) {
                if ($player->getLoginKey() == $_COOKIE['LoginKey']) {
                    $_SESSION['T2SteamID64'] = $player->getSteamID64();
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * If user logs out clear session.
     */
    function ifLogout()
    {
        // unset cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
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
    function getMatchHistory($accID, $i){

        $getMatchHistoryFromUrl = file_get_contents("https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?matches_requested=1&key={$this->key}&account_id={$accID}");

        $match_history = json_decode($getMatchHistoryFromUrl);
        $accountID = $match_history->result->matches[0]->players[$i]->account_id;
        $playerSlot = $match_history->result->matches[0]->players[$i]->player_slot;
        $heroID = $match_history->result->matches[0]->players[$i]->hero_id;
        //$matchHistoryRepo = new MatchHistoryRepository();
        $matchHistory = new MatchHistory($accountID, $playerSlot, $heroID);

        return $matchHistory;
    }


    /**
     * @param $steamID
     * @return mixed
     */
    function playerInfo(){

        $profile = $this->get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$_SESSION['T2SteamID64']}");

        $steam_profile = json_decode($profile);
        $steamid = $steam_profile->response->players[0]->steamid;
        $profileUrl = $steam_profile->response->players[0]->profileurl;
        $nickname = $steam_profile->response->players[0]->personaname;
        $avatar = $steam_profile->response->players[0]->avatarfull;
        $userRepo = new UserInformationRepository();
        $userinfo = new UserInformation($steamid, $nickname, $profileUrl, $avatar);
        //$userRepo->insert($userinfo);
        return  $userinfo;
    }
    function getPlayersInfo($playersSteamID){

        $profile = $this->get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$playersSteamID}");

        $steam_profile = json_decode($profile);

            $avatar = $steam_profile->response->players[0]->avatarfull;

        if(is_null($avatar)){
            $userinfo = new UserInformation("Anonymous", "Anonymous", "Anonymous", "Anonymous");
            return $userinfo;
        }
            $steamid = $steam_profile->response->players[0]->steamid;
            $profileUrl = $steam_profile->response->players[0]->profileurl;
            $nickname = $steam_profile->response->players[0]->personaname;

        $userRepo = new UserInformationRepository();
        $userinfo = new UserInformation($steamid, $nickname, $profileUrl, $avatar);
        //$userRepo->insert($userinfo);

        return  $userinfo;
    }

    /**
     * @return string
     */
    function ifLoggedInPresentPlayerInformation()
    {
        $playerRepository = new PlayerRepository();
        $playerRatingRepository = new PlayerRatingRepository();

        $playerinfo = $this->playerInfo();
        $accID = $this->calculateAccountID($playerinfo->getSteamID());



        $player = $playerRepository->getByAccountID($accID);
        if(isset($_POST['players'])) {
            $ratedPlayers = $_POST['players'];
            foreach($ratedPlayers as $ratedPlayer) {
                $ratingInputName = "rating_" . $ratedPlayer;
                $rating = new PlayerRating($_POST['match_id'], $ratedPlayer, $player->getID(), $_POST[$ratingInputName]);
                $playerRatingRepository->update($rating);
            }
        }

        $latestMatchID ="";
        for($i = 0; $i <= 9; $i++){

            $matchHistory =  $this->GetMatchHistory($accID,$i);
            $playersSteamID = $this->calculateSteamID($matchHistory->getAccountID());

            $playersFromLastGame = $this->getPlayersInfo($playersSteamID);

            if($playersFromLastGame->getAvatarFull() == "Anonymous") {
                $latestMatchID .= nl2br("\n" . $playersFromLastGame->getPersonaname() . " : " . $matchHistory->getHeroID());
            }else{
                $latestMatchID .= nl2br("\n" . $playersFromLastGame->getPersonaname() . " : <img src=\"{$playersFromLastGame->getAvatarFull()}\" width=60px height=60px/>" . ": " . $matchHistory->getHeroID());
            }
        }

//$match_history->result->matches[0]->players[$i]->hero_id
        return
            "
            <!DOCTYPE html>
            <html>
            <head>
                <link rel=\"stylesheet\"  href=\"mainCss.css\">
                <script language=\"javascript\" type=\"text/javascript\" src=\"jquery-2.1.4.min.js\"></script>
                <script language=\"javascript\" type=\"text/javascript\" src=\"update.js\"></script>
            </head>
            <body>
        <header>
        <h1>Welcome {$playerinfo->getPersonaname()} <img src=\"{$playerinfo->getAvatarFull()}\" width=60px height=60px/></h1>
        </header>

            <article>
                <p>
                    <input type='hidden' name='account_id' value='" .$accID . "'/>
                    <h1>Ratings</h1>
                    <div id=\"ratings\"></div>
                    <div class=\"clear\"></div>
                    Your profile url : <a target='_blank' href={$playerinfo->getProfileUrl()}>{$playerinfo->getProfileUrl()}</a><br>
                    Your dotabuff url : <a target='_blank' href=http://www.dotabuff.com/players/{$accID}>http://www.dotabuff.com/players/{$accID}</a><br>

                    Latest match : $latestMatchID



                </p>
 </article>
            </body>

        ";

    }

    /**
     * @param $steam64
     * @return mixed
     */
    function calculateAccountID(){
        $steam32 = substr($_SESSION['T2SteamID64'], 1);
        return $steam32 - 76561197960265728;
    }
    function calculateSteamID($steamID32){
        return $steamID32 + 76561197960265728;
    }

    function calculateLoginKey() {
        $salt = "JockeOchFabianLoversForLife";
        $rand = rand(1, 999999999999999999);
        $key = md5($_SESSION['T2SteamID64'] . $salt . $rand);
        return $key;
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