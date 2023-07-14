<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Data base
 */
class DataBase
{
    protected $server;

    protected $dbh;

    protected $isSelect = false;

    protected $isSet = false;

    protected $sql = '';

    protected $andWhere = [];

    protected $orWhere = [];

    protected $where = '';

    protected $groupBy = '';

    protected $orderBy = '';

    protected $innerJoin;

    protected $leftJoin;

    protected $rightJoin;

    protected $fullJoin;

    protected $onJoin;

    protected $like;

    protected $notLike;

    protected $limit;

    protected $query;

    protected $paramsColumn;

    protected $paramsValues;

    protected $tableName;

    function __construct($dbh, $tableName)
    {
        $this->server = new Server;
        $this->dbh = $dbh;
        $this->tableName = $tableName;
        $this->paramsValues = [];
        $this->paramsColumn = [];
    }

    /**
     * Method. Generate Select Sql;
     * @param string $columnName
     * @return $this
     */
    public function selectSql($columnName = '*') {

        $this->isSelect = true;
        $this->sql .= 'SELECT ' . $columnName . ' FROM ' . $this->tableName;
        return $this;
    }

    /**
     * Method. Generate Other Sql;
     * @param $sql
     * @param bool $true
     * @return $this
     */
    public function otherSql($sql, $true = true) {

        $this->isSelect = $true;
        $this->sql = $sql;
        return $this;
    }

    /**
     * Method. Generate Select Sql;
     * @param $sql
     * @return $this
     */
    public function setSql($sql) {
        $this->isSet = true;
        $this->sql .= $sql;
        return $this;
    }

    /**
     * Method. Generate Update Sql;
     * @param array $paramsColumn
     * @param array $paramsValues
     * @return $this|string
     */
    public function updateSql($paramsColumn = [], $paramsValues = []) {

        if (!$this->checkCountArray($paramsColumn, $paramsValues)) {
            return EnumConst::ERROR_SQL_ARRAY;
        }

        $this->paramsColumn = $paramsColumn;
        $this->paramsValues = $paramsValues;

        $this->sql = 'UPDATE ' . $this->tableName . ' SET ';
        $paramsColumnToSql = array_map(function($value) { return $value . ' = :' . $value . '_val'; }, $paramsColumn);
        $paramsColumnToSql = array_map(function($value) { return $value . ', '; }, $paramsColumnToSql);
        foreach ($paramsColumnToSql as $value) {
            $this->sql .= $value;
        }
        $this->sql = substr($this->sql, 0, -2);
        return $this;
    }

    /**
     * Method. Generate Insert Sql;
     * @param array $paramsColumn
     * @param array $paramsValues
     * @return $this|string
     */
    public function insertSql($paramsColumn = [], $paramsValues = []) {

        if (!$this->checkCountArray($paramsColumn, $paramsValues)) {
            return EnumConst::ERROR_SQL_ARRAY;
        }

        $this->paramsValues = $paramsValues;
        $this->paramsColumn = $paramsColumn;

        $this->sql = 'INSERT INTO ' . $this->tableName . ' (';
        $this->sql .= implode(', ',$paramsColumn);
        $this->sql .= ') VALUES (';
        $paramsColumn = array_map(function($value){ return ' :' . $value.'_val'; }, $paramsColumn);
        $this->sql .= implode(', ',$paramsColumn);
        $this->sql .= ')';

        return $this;
    }

    /**
     * Method. Create table Sql;
     * @param array $paramsColumn
     * @param array $paramsType
     * @return $this|bool|string
     */
    public function createTableSql($paramsColumn = [], $paramsType = []) {

        if (!$this->checkCountArray($paramsColumn, $paramsType))
            return EnumConst::ERROR_SQL_ARRAY;

        $this->sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (`id` int(11) NOT NULL AUTO_INCREMENT, ';
        for ($i=0; $i < count($paramsColumn); $i++) {
            $this->sql .= '`'.$paramsColumn[$i] . '` ' . $paramsType[$i] . ' NOT NULL, ';
        }
        $this->sql .= 'PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
        $this->query = $this->dbh->prepare($this->sql);
        $this->query->execute();

        if ($this->query)
            return true;
        return false;
    }

    /**
     * Method. Generate Delete Sql;
     */
    public function deleteSql() {

        $this->sql = 'DELETE FROM ' . $this->tableName;
        return $this;
    }

    /**
     * Method. Where Sql;
     * @param $param
     * @return $this
     */
    public function where($param) {

        $this->where = ' WHERE ' . $param;
        return $this;
    }

    /**
     * Method. And Where Sql;
     * @param $param
     * @return $this
     */
    public function andWhere($param) {

        $this->andWhere[] = ' AND ' . $param;
        return $this;
    }

    /**
     * Method. or Where Sql;
     * @param $param
     * @return $this
     */
    public function orWhere($param) {

        $this->orWhere[] = ' OR ' . $param;
        return $this;
    }

    /**
     * Method. order by Sql;
     * @param $param
     * @return $this
     */
    public function orderBy($param) {

        $this->orderBy = ' ORDER BY ' . $param;
        return $this;
    }

    /**
     * Method. group by Sql;
     * @param $param
     * @return $this
     */
    public function groupBy($param) {

        $this->groupBy = ' GROUP BY ' . $param;
        return $this;
    }

    /**
     * Method. LIMIT Sql;
     * @param $param
     * @return $this
     */
    public function limit($param) {

        $this->limit = ' LIMIT ' . $param;
        return $this;
    }

    /**
     * Method. LIKE Sql;
     * @param $param
     * @return $this
     */
    public function like($param) {

        $this->like = ' LIKE ' . $param;
        return $this;
    }

    /**
     * Method. NOT LIKE Sql;
     * @param $param
     * @return $this
     */
    public function notLike($param) {

        $this->notLike = ' NOT LIKE ' . $param;
        return $this;
    }

    /**
     * Method. Innser Join Sql;
     * @param $param
     * @return $this
     */
    public function innerJoin($param) {

        $this->innerJoin = ' INNER JOIN ' . $param;
        return $this;
    }

    /**
     * Method. Left Join Sql;
     * @param $param
     * @return $this
     */
    public function leftJoin($param) {

        $this->leftJoin = ' LEFT JOIN ' . $param;
        return $this;
    }

    /**
     * Method. Right Join Sql;
     * @param $param
     * @return $this
     */
    public function rightJoin($param) {

        $this->rightJoin = ' RIGHT JOIN ' . $param;
        return $this;
    }

    /**
     * Method. Full Join Sql;
     * @param $param
     * @return $this
     */
    public function fullJoin($param) {

        $this->fullJoin = ' FULL JOIN ' . $param;
        return $this;
    }

    /**
     * Method. On Join Sql;
     * @param $param
     * @return $this
     */
    public function onJoin($param) {

        $this->onJoin = ' ON ' . $param;
        return $this;
    }

    /**
     * Method. Execute query;
     */
    public function executeQuery($isJson = false) {

        if (!$this->checkCountArray($this->paramsColumn, $this->paramsValues)) {
            return EnumConst::ERROR_SQL_ARRAY;
        }

        $andWhereResult = implode(' ', $this->andWhere);
        $orWhereResult = implode(' ', $this->orWhere);

        $this->sql .= $this->innerJoin . $this->leftJoin . $this->rightJoin . $this->fullJoin . $this->onJoin;
        $this->sql .= $this->where . $andWhereResult . $orWhereResult . $this->groupBy . $this->orderBy . $this->limit . $this->like . $this->notLike;

        $this->query = $this->dbh->prepare($this->sql);

        if (!empty($this->paramsColumn) && !empty($this->paramsValues)) {
            for ($i=0; $i < count($this->paramsColumn); $i++) {
                if ($isJson)
                    $this->query->bindvalue($this->paramsColumn[$i] . '_val', $this->paramsValues[$i]);
                else
                    $this->query->bindvalue($this->paramsColumn[$i] . '_val', $this->charsHtmlScript($this->paramsValues[$i]));
            }
        }

        if($this->isSet)
            $this->query->nextRowset();

        $this->query->execute();
        //$this->server->log($this->sql, "logs/sql.log");
        return $this;
    }

    /**
     * Method. Get Result;
     */
    public function getResult() {

        if ($this->query) {
            if ($this->isSelect)
                return $this->query->fetchAll();
            return true; //TODO lastInsertId
        }
        return false;
    }

    /**
     * Method. Get Single Result;
     */
    public function getSingleResult() {

        if ($this->query) {
            if ($this->isSelect)
                return $this->query->fetch();
            return true;
        }
        return false;
    }

    /**
     * Method. Get Query;
     */
    public function getQuery() {
        return $this->sql;
    }

    /**
     * @param array $paramsColumn
     * @param array $paramsValues
     * @return bool
     */
    protected function checkCountArray($paramsColumn = [], $paramsValues = []) {
        if (count($paramsColumn) != count($paramsValues))
            return false;
        return true;
    }

    /**
     * @param $value
     * @return string
     */
    protected function charsHtmlScript($value) {
        return addcslashes(htmlspecialchars(stripslashes($value)), '\'"\\');
    }
}