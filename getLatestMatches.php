<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 14:42
 * Description: This script fetches the latest game played by all players in the database.
 */

/*
 * Debugging
 *
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 */

require_once(dirname(__FILE__) . '/Site/vendor/autoload.php');
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/Site/Model/PlayerRepository.php');
require_once(dirname(__FILE__) . '/Site/Model/PlayerRatingRepository.php');

use Dota2Api\Api;

$playerRepository = new PlayerRepository();
$playerRatingRepository = new PlayerRatingRepository();
Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

$players = $playerRepository->getAll();
foreach($players as $player) {
    echo "Getting match history for " . $player->getName() . "...<br/>";
    set_time_limit(60);
    $matchesMapperWeb = new Dota2Api\Mappers\MatchesMapperWeb();
    $matchesMapperWeb->setAccountId($player->getAccountID());
    $matchesShortInfo = $matchesMapperWeb->load();
    $matchHighestSeqNum = 0;
    $matchInfo = null;

    if(empty($matchesShortInfo)) {
        syslog(LOG_DEBUG, "Examensarbete: Empty Matches short info returned for " . $player->getName());
        continue;
    }

    foreach ($matchesShortInfo as $key => $matchShortInfo) {
        if($matchShortInfo->get('match_seq_num') > $matchHighestSeqNum) {
            $matchHighestSeqNum = $matchShortInfo->get('match_seq_num');
            $matchInfo = $matchShortInfo;
        }
    }

    if(empty($matchInfo)) {
        syslog(LOG_DEBUG, "Examensarbete: Empty Matchinfo returned for " . $player->getName());
        continue;
    }

    $matchMapper = new Dota2Api\Mappers\MatchMapperWeb($matchInfo->get('match_id'));
    $match = $matchMapper->load();

    if(empty($match)) {
        syslog(LOG_DEBUG, "Examensarbete: Empty Match returned for " . $player->getName());
        continue;
    }

    $mm = new Dota2Api\Mappers\MatchMapperDb();
    $mm->save($match);

    // Fetch all players in the match, then add rows to player_ratings for each player in the db, except when playerID == ratedByID
    $slots = $match->getAllSlots();
    $playersInMatch = array();
    foreach($slots as $slot) {
        $playerInMatch = $playerRepository->getByAccountID($slot->get('account_id'));
        if(!empty($playerInMatch)){
            $playersInMatch[] = $playerInMatch;
        }
    }

    foreach($playersInMatch as $rateBy) {
        foreach($playersInMatch as $rate) {
            if($rateBy->getID() != $rate->getID()) {
                $playerRating = new PlayerRating($match->get('match_id'), $rate->getID(), $rateBy->getID(), 0);
                $playerRatingRepository->insert($playerRating);
            }
        }
    }
}
