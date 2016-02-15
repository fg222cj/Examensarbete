
    <?php
        include "openid.php";


class Login
{

    private $openID;
    private $key = "DE32860E362CE20EEB23A66C5E39260F";

    function setOpenId(LightOpenID $openId)
    {
            $this->openID = $openId;
    }


    function hasButtonBeenPressed()
    {
        $this->openID->identity = "http://steamcommunity.com/openid";
        header("Location: {$this->openID->authUrl()}");
    }

    function ifNotLoggedIn()
    {
        return  "<div id=\"login\"><a href=\"?login\"><img src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png\"/></a></div>";
        echo $_SESSION['T2SteamAuth'];
    }


    function writeToFile()
    {
        $_SESSION['T2SteamAuth'] = $this->openID->validate() ? $this->openID->identity : null;
        $_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);

        if ($_SESSION['T2SteamAuth'] !== null) {

            $Steam64 = str_replace("http://steamcommunity.com/openid/id", "", $_SESSION['T2SteamAuth']);
            $profile = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->key}&steamids={$Steam64}");
            $items = file_get_contents("http://steamcommunity.com/profiles/{$Steam64}/inventory/json/730/2");
            $buffer = fopen("cache/{$Steam64}.json", "w+");
            $buffer2 = fopen("cache/{$Steam64}i.json", "w+");
            fwrite($buffer, $profile);
            fwrite($buffer2, $items);
            fclose($buffer);
            fclose($buffer2);
        }

        header("Location: Index.php");

    }



    function ifLoggedInPresentLogoutBtn()
    {
        return  "<div id=\"login\"><a href=\"?logout\">Logout</a></div>";
    }

    function ifLogout()
    {
        unset($_SESSION['T2SteamAuth']);
        unset($_SESSION['T2SteamID64']);
        header("Location: Index.php");

    }

    function presentBtn()
    {
        return  "</div>
        <div id='menu'><?php
        echo 'd';
        ?>
        </div>";
    }

    function ifLoggedInPresentPlayerInformation()
    {
        $steam_profile = json_decode(file_get_contents("cache/{$_SESSION["T2SteamID64"]}.json"));
       return " <html>
        <head>
            <title>Rank your teammates</title>
            <link rel='stylesheet' href='mainCss.css' type='text/css'/>
        </head>
        <body>
        <div id='top'>
            <div id='index'>Index</div>
            <div id='menu'>
            </div>
            <div id='miniprofile'>


        <a href=\'myProfile.php\'><img src=\'{$steam_profile->response->players[0]->avatarfull}\' width=60px height=60px/></a>;
        ";

    }
}