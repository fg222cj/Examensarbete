<?php
error_reporting(E_ALL);
require_once "HandleLogin.php";

session_start();

if(!isset($_SESSION['openID'])){
    $_SESSION['openID'] = new LightOpenID("http://gillenius.dlinkddns.com/");
    $_SESSION['index'] = new Login();
    $_SESSION['index']->setOpenId($_SESSION['openID']);
}


if (!$_SESSION["openID"]->mode) {

    //If button pressed
    if (isset($_GET['login'])) {
        $_SESSION["index"]->hasButtonBeenPressed();
    }
    if (!isset($_SESSION['T2SteamAuth'])) {
         echo $_SESSION["index"]->ifNotLoggedIn();
    }
    elseif ($_SESSION["openID"]->mode == "cancel") {
        echo "user has cancelled Authentication.";
    } else {
        if (!isset($_SESSION['T2SteamAuth'])) {
            $_SESSION["index"]->writeToFile();
        }
    }
    //If authenticated with steam present information
    if (isset($_SESSION['T2SteamAuth'])) {
        echo $_SESSION["index"]->ifLoggedInPresentLogoutBtn();
    }

    //If button pressed
    if (isset($_GET['logout'])) {
        $_SESSION["index"]->ifLogout();
    }
    //If player logged in
    if (isset($_SESSION['T2SteamAuth'])) {
        $_SESSION["index"]->ifLoggedInPresentPlayerInformation();
    }

    echo $_SESSION["index"]->presentBtn();
}


