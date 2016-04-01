<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-03-30
 * Time: 10:38
 */

require_once(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../../config.php');

require_once(dirname(__FILE__) . '/PlayerRepository.php');
require_once(dirname(__FILE__) . '/PlayerRatingRepository.php');
use Dota2Api\Api;

class Export {
    private $playerRepository;
    private $playerRatingRepository;
    private $matchesMapper;

    public function __construct() {
        $this->playerRepository = new PlayerRepository();
        $this->playerRatingRepository = new PlayerRatingRepository();
        $this->matchesMapper = new \Dota2Api\Mappers\MatchesMapperDb();
        Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));
    }

    public function exportAll() {
        set_time_limit(60);
        $rows = array();
        $matches = $this->matchesMapper->load();
        foreach($matches as $match) {
            set_time_limit(60);
            $matchID = $match->get('match_id');
            $slots = $match->getAllSlots();
            foreach($slots as $slot) {
                $row = array();
                $accountID = $slot->get('account_id');
                $player = $this->playerRepository->getByAccountID($accountID);
                if(empty($player)) {
                    continue;
                }
                $playerID = $player->getID();
                $ratings = null;
                $ratings = $this->playerRatingRepository->getByPlayerIDAndMatchID($playerID, $matchID);
                if(is_array($ratings) && count($ratings) > 0) {
                    if(isset($player)) {
                        $row[] = $player->getName();
                    }
                    else {
                        $row[] = "?";
                    }
                    // There are 4 reserved places for the ratings (one for each other player on the team) so we need to fill all of them with either the rating or an empty string (if unrated)
                    for($i = 0; $i < 5; $i++) {
                        if((isset($ratings[$i]) || array_key_exists($i, $ratings)) && $ratings[$i]->getRating() != 0) {
                            $row[] = $ratings[$i]->getRating();
                        }
                        else {
                            $row[] = "?";
                        }
                    }
                }
                // If there are no ratings for this player then there is no need to create a row
                else {
                    continue;
                }

                $radiantWin = "FALSE";
                if($match->get('radiant_win')) {
                    $radiantWin = "TRUE";
                }

                $row[] = $match->get('match_id');
                $row[] = $radiantWin;
                $row[] = $match->get('duration');
                $row[] = $match->get('first_blood_time');
                $row[] = $slot->get('hero_id');
                $row[] = $slot->get('item_0');
                $row[] = $slot->get('item_1');
                $row[] = $slot->get('item_2');
                $row[] = $slot->get('item_3');
                $row[] = $slot->get('item_4');
                $row[] = $slot->get('item_5');
                $row[] = $slot->get('kills');
                $row[] = $slot->get('deaths');
                $row[] = $slot->get('assists');
                $row[] = $slot->get('gold');
                $row[] = $slot->get('last_hits');
                $row[] = $slot->get('denies');
                $row[] = $slot->get('gold_per_min');
                $row[] = $slot->get('xp_per_min');
                $row[] = $slot->get('gold_spent');
                $row[] = $slot->get('hero_damage');
                $row[] = $slot->get('tower_damage');
                $row[] = $slot->get('hero_healing');
                $row[] = $slot->get('level');

                $rows[] = implode(",", $row);
            }
        }

        $export = "@relation statistics\n\n";
        $export .= "@attribute Name string\n";
        $export .= "@attribute 'Rating 1' numeric\n";
        $export .= "@attribute 'Rating 2' numeric\n";
        $export .= "@attribute 'Rating 3' numeric\n";
        $export .= "@attribute 'Rating 4' numeric\n";
        $export .= "@attribute 'Match ID' numeric\n";
        $export .= "@attribute 'Radiant Win' {TRUE,FALSE}\n";
        $export .= "@attribute Duration numeric\n";
        $export .= "@attribute 'First blood time' numeric\n";
        $export .= "@attribute 'Hero ID' numeric\n";
        $export .= "@attribute 'Item 0' numeric\n";
        $export .= "@attribute 'Item 1' numeric\n";
        $export .= "@attribute 'Item 2' numeric\n";
        $export .= "@attribute 'Item 3' numeric\n";
        $export .= "@attribute 'Item 4' numeric\n";
        $export .= "@attribute 'Item 5' numeric\n";
        $export .= "@attribute Kills numeric\n";
        $export .= "@attribute Deaths numeric\n";
        $export .= "@attribute Assists numeric\n";
        $export .= "@attribute Gold numeric\n";
        $export .= "@attribute 'Last hits' numeric\n";
        $export .= "@attribute Denies numeric\n";
        $export .= "@attribute 'Gold per minute' numeric\n";
        $export .= "@attribute 'XP per minute' numeric\n";
        $export .= "@attribute 'Gold spent' numeric\n";
        $export .= "@attribute 'Hero damage' numeric\n";
        $export .= "@attribute 'Tower damage' numeric\n";
        $export .= "@attribute 'Hero healing' numeric\n";
        $export .= "@attribute Level numeric\n\n";
        $export .= "@data\n\n";

        foreach($rows as $row) {
            $export .= $row . "\n";
        }

        return $export;
    }
}