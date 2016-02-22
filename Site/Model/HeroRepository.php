<?php
require_once(dirname(__FILE__) . '/Repository.php');
/**
 * Created by Joakim Nilsson.
 * Date: 19/02/2016
 * Time: 10:39
 */
class HeroRepository extends Repository
{
    public function getHero($heroID){


        $db = $this->connection();

        $sql = "SELECT * FROM " . DBTABLEHEROES . " WHERE " . DBCOLUMNHEROESID . "=?";
        $params = array($heroID);

        $query = $db->prepare($sql);
        $query->execute($params);
        $result = $query->fetchAll();

        $ratings = array();

        foreach($result as $row) {
            $rating = $row[DBCOLUMHEROESNAME];
            $rating .="_sb.png";
            $ratings[] = $rating;
        }
        $files = scandir('Resources/Images/');
        foreach($files as $file) {
           if($rating == $file){
               return "Resources/Images/".$rating;
           }
        }
    }
}