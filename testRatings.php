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
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/src/PlayerRepository.php');
require_once(dirname(__FILE__) . '/src/PlayerRatingRepository.php');

use Dota2Api\Api;

Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

$playerRepository = new PlayerRepository();
$playerRatingRepository = new PlayerRatingRepository();
$player = $playerRepository->getByAccountID(53869596);

if(isset($_POST['players'])) {
    $ratedPlayers = $_POST['players'];
    foreach($ratedPlayers as $ratedPlayer) {
        $ratingInputName = "rating_" . $ratedPlayer;
        $rating = new PlayerRating($_POST['match_id'], $ratedPlayer, $player->getID(), $_POST[$ratingInputName]);
        $playerRatingRepository->update($rating);
    }
}

$ratings = $playerRatingRepository->getLatestUnratedByPlayerID($player->getID());

$html = "<html>
<head>
<title>Testar ratings</title>
</head>
<body>
<h1>Ratings</h1>";

if(!empty($ratings)) {
    $html .= "<h2>Match ID: " . $ratings[0]->getMatchID() . "</h2>
              <form action='testRatings.php' method='post'>
              <input type='hidden' name='match_id' value='" . $ratings[0]->getMatchID() . "' />";
    foreach ($ratings as $rating) {
        $otherPlayer = $playerRepository->getByID($rating->getPlayerID());
        $html .= "<div>
              <h3>" . $otherPlayer->getName() . "</h3>
              <input type='hidden' name='players[]' value='" . $rating->getPlayerID() . "'/>
              1
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='1'/>
              2
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='2'/>
              3
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='3'/>
              4
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='4'/>
              5
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='5'/>
              6
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='6'/>
              7
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='7'/>
              8
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='8'/>
              9
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='9'/>
              10
              <input type='radio' name='rating_" . $rating->getPlayerID() . "' value='10'/>
              </div>";
    }

    $html .= "<input type='submit' /></form>";
}
else {
    $html .= "<p>No recent unrated matches, good job!</p>";
}
$html .= "</body></html>";
echo $html;