<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-15
 * Time: 14:52
 * Description: This file defines all databases information (table names, column names) as constants to be used globally.
 */


/*
 * All tables
 */
define("DBCOLUMNID","id");
define("DBCOLUMNNAME","name");

/*
 *  Table: players
 */
define("DBTABLEPLAYERS","players");
define("DBCOLUMNACCOUNTID","account_id");
define("DBCOLUMNSTEAMID64","steam_id_64");
define("DBCOLUMNLOGINKEY","login_key");

/*
 *  Table: player_ratings
 */
define("DBTABLEPLAYERRATINGS", "player_ratings");
define("DBCOLUMNMATCHID", "match_id");
define("DBCOLUMNPLAYERID", "player_id");
define("DBCOLUMNRATEDBYID", "rated_by_id");
define("DBCOLUMNRATING", "rating");

/*
 *  Table: user_information
 */
define("DBTABLEUSERINFORMATION" , "user_information");
define("DBCOLUMNSTEAMID", "steamid");
define("DBCOLUMNPERSONANAME", "personaname");
define("DBCOLUMNPROFILEURL", "profileurl");
define("DBCOLUMNISPERSONANAMEREAL", "is_personaname_real");
define("DBCOLUMNAVATARFULL", "avatarfull");

/*
 *  Table: heroes
 */
define("DBTABLEHEROES" , "heroes");
define("DBCOLUMHEROESNAME", "name");
define("DBCOLUMNHEROESID", "id");
define("DBCOLUMNLOCALIZEDNAME", "localized_name");