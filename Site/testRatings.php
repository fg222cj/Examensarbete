<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 14:42
 * Description: This script fetches the latest game played by all players in the database.
 */

/*
 * Debugging
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/Model/PlayerRepository.php');
require_once(dirname(__FILE__) . '/Model/PlayerRatingRepository.php');
require_once(dirname(__FILE__) . '/Model/HeroRepository.php');
require_once(dirname(__FILE__) . '/Controller/HandleLogin.php');


use Dota2Api\Api;

Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

$playerRepository = new PlayerRepository();
$playerRatingRepository = new PlayerRatingRepository();
$handleLogin = new HandleLogin();
$heroRep = new HeroRepository();


if(isset($_POST['accountID']) && !empty($_POST['accountID'])) {
    $accountID = $_POST['accountID'];
}


$player = $playerRepository->getByAccountID($accountID);
$ratings = $playerRatingRepository->getLatestUnratedByPlayerID($player->getID());

//$matchHistory =  $handleLogin->GetMatchHistory($player->getAccountID(),1);
//$playerInfo = $handleLogin->getPlayerInfo($player->getSteamID64());

$html = "";

if(!empty($ratings)) {
    $matchMapper = new Dota2Api\Mappers\MatchMapperDb();
    $match = $matchMapper->load($ratings[0]->getMatchID());
    $slots = $match->getAllSlots();

    $html .= "<h2>Match ID: " . $ratings[0]->getMatchID() . "</h2>
              <form action='index.php' method='post' name='rating-form'>
              <input type='hidden' name='match_id' value='" . $ratings[0]->getMatchID() . "' />";



    foreach ($ratings as $rating) {
        $otherPlayer = $playerRepository->getByID($rating->getPlayerID());
        $otherPlayerSlot = null;
        foreach($slots as $slot) {
            if($slot->get('account_id') == $otherPlayer->getAccountID()) {
                $otherPlayerSlot = $slot;
            }
        }

        $html .= "<div class='rating-box'>
                    <div class='rating-info'>";

        if(!empty($otherPlayerSlot)) {
            $html .= "<img src=\"{$heroRep->getHero($otherPlayerSlot->get('hero_id'))}\"/>";
        }

        $html .= "<h3>" . $otherPlayer->getName()."</h3>";

        $html .= "</div><input type='hidden' name='players[]' value='" . $rating->getPlayerID() . "'/>";

        for ($i = 1; $i <= 5; $i++) {
            $checkedClass = "";
            $checkedAttribute = "";
            if($rating->getRating() == $i) {
                $checkedClass = " checked";
                $checkedAttribute = " checked='checked'";
            }
            $html .= "<div class='rating-radio'>
                  <input class='rate' type='radio' id='rating_" . $rating->getPlayerID() . "_" . $i . "' name='rating_" . $rating->getPlayerID() . "' value='" . $i . "'" . $checkedAttribute . "/>
                  <label class='rate" . $checkedClass . "' for='rating_" . $rating->getPlayerID() . "_" . $i . "'>" . $i . "</label>
                  </div>";
        }

        $html .= "</div>";
    }

    $html .= "<div class=\"clear\"></div>
              </form>";
}
else {
    $html .= "<p>No recent unrated matches, good job!</p>";
}

echo $html;
return $html;
//Avatars
//        $steam64 = $otherPlayer->getSteamID64();
//        $steam64 = substr($steam64, 1);
//        $playerInfo = $handleLogin->getPlayersInfo($steam64);
//        {$playerInfo->getAvatarFull()}