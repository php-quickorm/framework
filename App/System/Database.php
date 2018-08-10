<?php
namespace System;
use System\DatabaseDriver\pdo_mysql;
class Database{

    public $PDOStatement;
    public $PDOConnect;

    /**
     * PHP-JSORM 框架数据库基础类
     * @author Rytia <rytia@outlook.com>
     * @uses 用于驱动数据库，属于对 PHP PDO 的二次封装以满足快速操作，其中 PDO 对象保存在 $this->PDOConnect 中，PDOStatement 对象保存在 $this->PDOStatement 中
    */

    public function __construct() {
        // 使用 pdo 进行处理
        $this->PDOConnect = (new pdo_mysql())->connect;
    }

    /**
     * SQL 语句预处理并执行
     * @param string $sqlStatement, array $parameters
     * @return boolean
     * @uses 用于预处理并执行语句，请注意本方法结合了 pdo 中 prepare 和 execute 两个方法
     */
    public function prepare($sqlStatement, $parameters = []){
        $this->PDOStatement = $this->PDOConnect->prepare($sqlStatement);
        return $this->PDOStatement->execute($parameters);
    }

    /**
     * 取回第一条结果
     * @return array
     */
    public function fetch() {
        return $this->PDOStatement->fetch(2);
    }

    /**
     * 取回结果集
     * @return array
     */
    public function fetchAll() {
        return $this->PDOStatement->fetchAll(2);
    }

    /**
     * 开始一个新的事务
     * @uses 用于 Model->update(), Model->delete(), Model->save() 等操作的准备工作
     */
    public function transaction() {
        $this->PDOConnect->beginTransaction();
    }

    /**
     * 提交事务
     * @return boolean
     * @uses 用于 Model->update(), Model->delete(), Model->save() 等操作使 SQL 生效
     */
    public function commit() {
        return $this->PDOConnect->commit();
    }
}
