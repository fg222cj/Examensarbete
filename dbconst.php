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

/*
 *  Table: player_ratings
 */
define("DBTABLEPLAYERRATINGS", "player_ratings");
define("DBCOLUMNMATCHID", "match_id");
define("DBCOLUMNPLAYERID", "player_id");
define("DBCOLUMNRATEDBYID", "rated_by_id");
define("DBCOLUMNRATING", "rating");