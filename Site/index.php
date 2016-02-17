<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-17
 * Time: 08:12
 */

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/Model/PlayerRepository.php');
require_once(dirname(__FILE__) . '/Model/PlayerRatingRepository.php');

use Dota2Api\Api;

Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));


$playerRepository = new PlayerRepository();
$playerRatingRepository = new PlayerRatingRepository();
$accountID = 53869596;
$player = $playerRepository->getByAccountID($accountID);

if(isset($_POST['players'])) {
    $ratedPlayers = $_POST['players'];
    foreach($ratedPlayers as $ratedPlayer) {
        $ratingInputName = "rating_" . $ratedPlayer;
        $rating = new PlayerRating($_POST['match_id'], $ratedPlayer, $player->getID(), $_POST[$ratingInputName]);
        $playerRatingRepository->update($rating);
    }
}


?>
<html>
    <head>
        <title>Testar ratings</title>
        <script language="javascript" type="text/javascript" src="../jquery-2.1.4.min.js"></script>
        <script language="javascript" type="text/javascript" src="../update.js"></script>
    </head>
    <body>
        <input type='hidden' name='account_id' value='<?php echo $accountID ?>'/>
        <h1>Ratings</h1>
        <div id="ratings"></div>
    </body>
</html>