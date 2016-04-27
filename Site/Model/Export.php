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

    /*
     * Takes two arguments, slot and match. Returns an array with slot and match data if ratings exist for the slot,
     * otherwise returns null.
     */
    public function exportSlot($slot, $match, $asNominal = false) {
        $row = array();
        $accountID = $slot->get('account_id');
        $player = $this->playerRepository->getByAccountID($accountID);
        if(empty($player)) {
            return null;
        }

        $playerID = $player->getID();
        $matchID = $match->get('match_id');
        $ratings = null;
        $ratings = $this->playerRatingRepository->getByPlayerIDAndMatchID($playerID, $matchID);
        $averageRating = "?";
        $submittedRatingsCount = 0;
        $ratingTotal = 0;
        if(is_array($ratings) && count($ratings) > 0) {
            if(isset($player)) {
                $row[] = "'" . $player->getName() . "'";
            }
            else {
                $row[] = "?";
            }
            // There are 4 reserved places for the ratings (one for each other player on the team) so we need to fill all of them with either the rating or an empty string (if unrated)
            for($i = 0; $i < 4; $i++) {
                if((isset($ratings[$i]) || array_key_exists($i, $ratings)) && $ratings[$i]->getRating() != 0) {
                    if($asNominal) {
                        $row[] = $this->convertToNominal($ratings[$i]->getRating(), "RATING");
                    }
                    else {
                        $row[] = $ratings[$i]->getRating();
                    }
                    $ratingTotal += $ratings[$i]->getRating();
                    $submittedRatingsCount++;
                }
                else {
                    $row[] = "?";
                }
            }
            if($submittedRatingsCount > 0) {
                $averageRating = $ratingTotal / $submittedRatingsCount;
            }
        }
        // If there are no ratings for this player then there is no need to create a row
        else {
            return null;
        }


        $radiantWin = "FALSE";
        if($match->get('radiant_win')) {
            $radiantWin = "TRUE";
        }

        if($asNominal) {
            $row[] = $this->convertToNominal($ratings[$i]->getRating(), "RATING");
            $row[] = $this->convertToNominal($averageRating, "RATING");
            $row[] = $match->get('match_id');
            $row[] = $radiantWin;
            $row[] = $this->convertToNominal($match->get('duration'), "DURATION");
            $row[] = $this->convertToNominal($match->get('first_blood_time'), "FIRST_BLOOD_TIME");
            $row[] = $this->convertToNominal($slot->get('hero_id'), "HERO_ID");
            $row[] = $this->convertToNominal($slot->get('item_0'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('item_1'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('item_2'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('item_3'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('item_4'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('item_5'), "ITEM_ID");
            $row[] = $this->convertToNominal($slot->get('kills'), "KILLS");
            $row[] = $this->convertToNominal($slot->get('deaths'), "DEATHS");
            $row[] = $this->convertToNominal($slot->get('assists'), "ASSISTS");
            $row[] = $this->convertToNominal($slot->get('gold'), "GOLD");
            $row[] = $this->convertToNominal($slot->get('last_hits'), "LAST_HITS");
            $row[] = $this->convertToNominal($slot->get('denies'), "DENIES");
            $row[] = $this->convertToNominal($slot->get('gold_per_min'), "GOLD_PER_MIN");
            $row[] = $this->convertToNominal($slot->get('xp_per_min'), "XP_PER_MIN");
            $row[] = $this->convertToNominal($slot->get('gold_spent'), "GOLD_SPENT");
            $row[] = $this->convertToNominal($slot->get('hero_damage'), "HERO_DAMAGE");
            $row[] = $this->convertToNominal($slot->get('tower_damage'), "TOWER_DAMAGE");
            $row[] = $this->convertToNominal($slot->get('hero_healing'), "HERO_HEALING");
            $row[] = $this->convertToNominal($slot->get('level'), "LEVEL");
        }
        else {
            $row[] = $averageRating;
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
        }

        return $row;
    }

    public function exportAllFromMatch($match, $asNominal = false) {
        $rows = array();
        set_time_limit(60);
        $slots = $match->getAllSlots();
        foreach($slots as $slot) {
            $row = $this->exportSlot($slot, $match, $asNominal);
            if(isset($row)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function exportAll($asNominal = false) {
        set_time_limit(2400);
        $result = array();
        $matches = $this->matchesMapper->load();
        foreach($matches as $match) {
            $rows = $this->exportAllFromMatch($match, $asNominal);
            foreach($rows as $row) {
                $result[] = implode(",", $row);
            }
        }

        $export = "@relation statistics\n\n";
        $export .= "@attribute Name string\n";
        $export .= "@attribute 'Rating 1' numeric\n";
        $export .= "@attribute 'Rating 2' numeric\n";
        $export .= "@attribute 'Rating 3' numeric\n";
        $export .= "@attribute 'Rating 4' numeric\n";
        $export .= "@attribute 'Average Rating' numeric\n";
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

        foreach($result as $row) {
            $export .= $row . "\n";
        }

        return $export;
    }

    public function convertToNominal($value, $type) {
        $nominalValue = "?";
        switch($type) {
            case "RATING":
                switch($value) {
                    case 1:
                        $nominalValue = "Very Low";
                        break;
                    case 2:
                        $nominalValue = "Low";
                        break;
                    case 3:
                        $nominalValue = "Average";
                        break;
                    case 4:
                        $nominalValue = "High";
                        break;
                    case 5:
                        $nominalValue = "Very High";
                        break;
                }
                break;
            case "DURATION":
                if($value <= 1109) {
                    $nominalValue = "Very Short";
                }
                elseif($value > 1109  && $value <= 2218) {
                    $nominalValue = "Short";
                }
                elseif($value > 2218 && $value <= 3327) {
                    $nominalValue = "Average";
                }
                elseif($value > 3327 && $value <= 4436) {
                    $nominalValue = "Long";
                }
                elseif($value > 4436) {
                    $nominalValue = "Very Long";
                }
                break;
            case "FIRST_BLOOD_TIME":
                if($value <= 180) {
                    $nominalValue = "Early";
                }
                elseif($value > 180 && $value <= 360) {
                    $nominalValue = "Average";
                }
                elseif($value > 360) {
                    $nominalValue = "Late";
                }
                break;
            case "HERO_ID":
                // ToDo: Map each hero ID to a name here. Possible with Kronusme API?
                $nominalValue = "?";
                break;
            case "ITEM_ID":
                // ToDo: Map each item ID to a name here. Possible with Kronusme API?
                break;
            case "KILLS":
                if($value <= 4) {
                    $nominalValue = "Very Low";
                }
                elseif($value > 4  && $value <= 10) {
                    $nominalValue = "Low";
                }
                elseif($value > 10 && $value <= 18) {
                    $nominalValue = "Average";
                }
                elseif($value > 18 && $value <= 24) {
                    $nominalValue = "High";
                }
                elseif($value > 24) {
                    $nominalValue = "Very High";
                }
                break;
            case "DEATHS":
                if($value <= 3) {
                    $nominalValue = "Very Low";
                }
                elseif($value > 3  && $value <= 7) {
                    $nominalValue = "Low";
                }
                elseif($value > 7 && $value <= 13) {
                    $nominalValue = "Average";
                }
                elseif($value > 13 && $value <= 17) {
                    $nominalValue = "High";
                }
                elseif($value > 20) {
                    $nominalValue = "Very High";
                }
                break;
            case "ASSISTS":
                if($value <= 5) {
                    $nominalValue = "Very Low";
                }
                elseif($value > 5  && $value <= 12) {
                    $nominalValue = "Low";
                }
                elseif($value > 12 && $value <= 21) {
                    $nominalValue = "Average";
                }
                elseif($value > 21 && $value <= 28) {
                    $nominalValue = "High";
                }
                elseif($value > 28) {
                    $nominalValue = "Very High";
                }
                break;
            case "GOLD":
                if($value <= 1000) {
                    $nominalValue = "Very Low";
                }
                elseif($value > 1000  && $value <= 2500) {
                    $nominalValue = "Low";
                }
                elseif($value > 2500 && $value <= 5000) {
                    $nominalValue = "Average";
                }
                elseif($value > 5000 && $value <= 6500) {
                    $nominalValue = "High";
                }
                elseif($value > 6500) {
                    $nominalValue = "Very High";
                }
                break;
            case "LAST_HITS":
                if($value <= 44) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 90) {
                    $nominalValue = "Low";
                }
                elseif($value <= 180) {
                    $nominalValue = "Average";
                }
                elseif($value <= 270) {
                    $nominalValue = "High";
                }
                elseif($value > 270) {
                    $nominalValue = "Very High";
                }
                break;
            case "DENIES":
                if($value <= 3) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 7) {
                    $nominalValue = "Low";
                }
                elseif($value <= 12) {
                    $nominalValue = "Average";
                }
                elseif($value <= 22) {
                    $nominalValue = "High";
                }
                elseif($value > 22) {
                    $nominalValue = "Very High";
                }
                break;
            case "GOLD_PER_MIN":
                if($value <= 200) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 250) {
                    $nominalValue = "Low";
                }
                elseif($value <= 450) {
                    $nominalValue = "Average";
                }
                elseif($value <= 550) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "XP_PER_MIN":
                if($value <= 200) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 320) {
                    $nominalValue = "Low";
                }
                elseif($value <= 440) {
                    $nominalValue = "Average";
                }
                elseif($value <= 550) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "GOLD_SPENT":
                if($value <= 4000) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 6500) {
                    $nominalValue = "Low";
                }
                elseif($value <= 12000) {
                    $nominalValue = "Average";
                }
                elseif($value <= 18000) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "HERO_DAMAGE":
                if($value <= 3000) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 5500) {
                    $nominalValue = "Low";
                }
                elseif($value <= 9500) {
                    $nominalValue = "Average";
                }
                elseif($value <= 14000) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "TOWER_DAMAGE":
                if($value <= 600) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 1200) {
                    $nominalValue = "Low";
                }
                elseif($value <= 2500) {
                    $nominalValue = "Average";
                }
                elseif($value <= 3500) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "HERO_HEALING":
                if($value <= 400) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 950) {
                    $nominalValue = "Low";
                }
                elseif($value <= 1600) {
                    $nominalValue = "Average";
                }
                elseif($value <= 2500) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
            case "LEVEL":
                if($value <= 10) {
                    $nominalValue = "Very Low";
                }
                elseif($value <= 15) {
                    $nominalValue = "Low";
                }
                elseif($value <= 19) {
                    $nominalValue = "Average";
                }
                elseif($value <= 23) {
                    $nominalValue = "High";
                }
                else {
                    $nominalValue = "Very High";
                }
                break;
        }
        return $nominalValue;
    }
}