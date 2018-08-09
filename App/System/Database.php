<?php
namespace System;
use System\DatabaseDriver\pdo_mysql;
class Database{

    public $db;

    /**
     * PHP-JSORM 框架数据库类
     * @author Rytia <rytia@outlook.com>
     * @param string $sql, string $parameters
     * @uses 用于驱动数据库，其中 pdo 对象保存在 $this->db
    */

    public function __construct($sql, $parameters = []) {
        // $sql: 需要预处理的 SQL 语句
        // $array: 预处理相应的字段数组

        // 使用 pdo_mysql 进行处理
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
