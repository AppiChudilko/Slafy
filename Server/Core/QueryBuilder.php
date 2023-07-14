<?php

namespace Server\Core;

use \PDO;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
/**
 * QueryBuilder
 */
class QueryBuilder
{
    public $dbh;

    protected $server;

    function __construct()
    {
        $this->server = new Server;
    }

    /**
     * Mehtod. Connect data base;
     * @param $host
     * @param $db_name
     * @param $user
     * @param $pass
     * @param string $charset
     * @return bool
     */
    public function connectDataBase($host, $db_name, $user, $pass, $charset = 'utf8mb4') {
        $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
        $pass = 'A' . hash('sha256', $pass);

        $opt = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_EMULATE_PREPARES => 1);
        // указатель на соединение
        try
        {
            $this->dbh = new PDO($dsn, $user, $pass, $opt);
        }
        catch(\PDOException $e)
        {
            echo $e->getMessage();
            $this->server->log("[".$this->server->dateNow."] ".$e.";\n", "logs/errors.log");
            return false;
        }
        $char = $this->dbh->prepare('SET NAMES utf8mb4');
        $char->execute();
        $this->server->log("Connect to data base - true; DB Name = ".$db_name.";\n", "logs/sql.log");
        return true;
    }

    /**
     * Mehtod. Create data base connect;
     * @param $tableName
     * @return DataBase
     */
    public function createQueryBuilder($tableName) {
        return new DataBase($this->dbh, $tableName);
    }
}