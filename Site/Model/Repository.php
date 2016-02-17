<?php
/**
 * Author: Fabian Nachenius
 * Date: 2016-02-10
 * Time: 15:44
 */
require_once(dirname(__FILE__) . "/../../config.php");
require_once(dirname(__FILE__) . "/../../dbconst.php");

abstract class Repository {

    protected $dbConnection;
    protected $dbTable;

    protected function connection() {
        if ($this->dbConnection == NULL) {
            $this->dbConnection = new \PDO(DBCONNECTION, DBUSERNAME, DBPASSWORD);
        }
        $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this->dbConnection;
    }
}