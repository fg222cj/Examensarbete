<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 14:42
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require_once(dirname(__FILE__) . '/vendor/autoload.php');
    require_once(dirname(__FILE__) . '/config.php');
    require_once(dirname(__FILE__) . '/src/PlayerRepository.php');

    use Dota2Api\Api;

    $playerRepository = new PlayerRepository();
    Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

    $players = $playerRepository->getAll();
    foreach($players as $player) {
        echo "Getting match history for " . $player->getName() . "...\n";
        set_time_limit(60);
        $matchesMapperWeb = new Dota2Api\Mappers\MatchesMapperWeb();
        $matchesMapperWeb->setAccountId($player->getAccountID());
        $matchesShortInfo = $matchesMapperWeb->load();
        foreach ($matchesShortInfo as $key => $matchShortInfo) {
            $matchMapper = new Dota2Api\Mappers\MatchMapperWeb($key);
            $match = $matchMapper->load();
            $mm = new Dota2Api\Mappers\MatchMapperDb();
            $mm->save($match);
        }
    }
