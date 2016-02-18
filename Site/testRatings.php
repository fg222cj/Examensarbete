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

use Dota2Api\Api;

Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

$playerRepository = new PlayerRepository();
$playerRatingRepository = new PlayerRatingRepository();

if(isset($_POST['accountID']) && !empty($_POST['accountID'])) {
    $accountID = $_POST['accountID'];
}


$player = $playerRepository->getByAccountID($accountID);
$ratings = $playerRatingRepository->getLatestUnratedByPlayerID($player->getID());

$html = "";

if(!empty($ratings)) {
    $html .= "<h2>Match ID: " . $ratings[0]->getMatchID() . "</h2>
              <form action='index.php' method='post'>
              <input type='hidden' name='match_id' value='" . $ratings[0]->getMatchID() . "' />";

    foreach ($ratings as $rating) {
        $otherPlayer = $playerRepository->getByID($rating->getPlayerID());
        $html .= "<div class='rating-box'>
                      <h3>" . $otherPlayer->getName() . "</h3>
                      <input type='hidden' name='players[]' value='" . $rating->getPlayerID() . "'/>";
        for($i = 10; $i >= 1; $i--) {
            $html .= "<div class='rating-radio'>
                      <label for='rating_" . $rating->getPlayerID() . "_" . $i . "'>" . $i . "</label>
                      <input type='radio' id='rating_" . $rating->getPlayerID() . "_" . $i . "' name='rating_" . $rating->getPlayerID() . "' value='" . $i . "'/>
                      </div>";
        }
        $html .= "</div>";
    }

    $html .= "<input type='submit' /></form>";
}
else {
    $html .= "<p>No recent unrated matches, good job!</p>";
}

echo $html;
return $html;