<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 14:42
 */

    require_once 'vendor/autoload.php';

    use Dota2Api\Api;

    Api::init('DE32860E362CE20EEB23A66C5E39260F', array('localhost', 'examensarbete', 'ytWuPuG2tSwFQx3z', 'examensarbete', ''));

    $matchesMapperWeb = new Dota2Api\Mappers\MatchesMapperWeb();
    $matchesMapperWeb->setAccountId(22471377);
    $matchesShortInfo = $matchesMapperWeb->load();
    foreach ($matchesShortInfo as $key => $matchShortInfo) {
        $matchMapper = new Dota2Api\Mappers\MatchMapperWeb($key);
        $match = $matchMapper->load();
        $mm = new Dota2Api\Mappers\MatchMapperDb();
        $mm->save($match);
    }
