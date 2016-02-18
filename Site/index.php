<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-17
 * Time: 08:12
 */

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . "/Controller/HandleLogin.php");

session_start();

use Dota2Api\Api;

Api::init(STEAMAPIKEY, array(DBHOST, DBUSERNAME, DBPASSWORD, DBDATABASE, ''));

//Autoclear cache
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$openID = new LightOpenID("http://gillenius.dlinkddns.com/");
$login = new Login();
$login->setOpenId($openID);

//If logout button pressed
if (isset($_GET['logout'])) {
    $login->ifLogout();
}

//If player logged in present logout button and the players profile
if ($login->cookieLogin() || (isset($_SESSION['T2SteamAuth']) && !empty($_SESSION['T2SteamAuth']))) {
    echo $login->ifLoggedInPresentLogoutBtn();
    echo $login->ifLoggedInPresentPlayerInformation();
}

//If not successfully authenticated
if (!$openID->mode && !$login->cookieLogin()) {

    //If button pressed
    if (isset($_GET['login'])) {
        $login->hasButtonBeenPressed();
    }

    if (!isset($_SESSION['T2SteamAuth'])) {
        echo $login->ifNotLoggedIn();
    }

}
//If authenticated
elseif ($openID->mode == "cancel") {
    echo "user has cancelled Authentication.";
} else {
    if (!isset($_SESSION['T2SteamAuth']) && !$login->cookieLogin()) {
        $login->storeUserLoginInfo();
    }
}

?>