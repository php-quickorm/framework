<?php
namespace System;
use System\DatabaseDriver\pdo_mysql;
class Database{

    /**
     * PHP-JSORM 框架数据库驱动器
     *
     * 传入参数：
     *      $sql: 需要预处理的 SQL 语句
     *      $array: 预处理相应的字段数组
     * 返回值：
     *      SQL 查询结果的关联数组
    */

    public $db;

    public function __construct($sql, $parameters = []) {
        //使用 PDO 进行处理
        $databaseConnect = (new pdo_mysql())->connect;
        $toDo = $databaseConnect->prepare($sql);
        $toDo->execute($parameters);
        $this->db = $toDo;
    }

    public function fetch() {
        return $this->db->fetch(2);
    }

    public function fetchAll() {
        return $this->db->fetchAll(2);
    }
}
