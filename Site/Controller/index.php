<?php
error_reporting(E_ALL);
require_once "HandleLogin.php";

session_start();


$openID = new LightOpenID("http://gillenius.dlinkddns.com/");
$index = new Login();
$index->setOpenId($openID);

//If not successfully authenticated
if (!$openID->mode) {

    //If button pressed
    if (isset($_GET['login'])) {
        $index->hasButtonBeenPressed();
    }
    //If button pressed
    if (isset($_GET['logout'])) {
        $index->ifLogout();
    }

    if (!isset($_SESSION['T2SteamAuth'])) {
        echo $index->ifNotLoggedIn();
    }

}
//If authenticated
elseif ($openID->mode == "cancel") {
        echo "user has cancelled Authentication.";
    } else {
        if (!isset($_SESSION['T2SteamAuth'])) {
            $index->writeToFile();
        }
    }
    //If player logged in present logout button and the players profile
    if (isset($_SESSION['T2SteamAuth'])) {
        echo $index->ifLoggedInPresentLogoutBtn();
        echo $index->ifLoggedInPresentPlayerInformation();
}
//If no activity over a week, logout.
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 604800)) {
    session_unset();
    session_destroy();
}
    $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp


