<?php
namespace System;
use ReflectionClass;
use ReflectionException;
use System\DatabaseDriver\pdo_mysql;
class Database{

    public $PDOStatement;
    public $PDOConnect;
    public $SQLStatement;
    public $table;
    public $model;

    /**
     * PHP-QuickORM 框架数据库基础类
     * @author Rytia <rytia@outlook.com>
     * @uses 用于驱动数据库，属于对 PHP PDO 的二次封装以满足快速操作，其中 PDO 对象保存在 $this->PDOConnect 中，PDOStatement 对象保存在 $this->PDOStatement 中
    */

    public function __construct($table) {
        // 使用 pdo 进行处理
        $this->PDOConnect = (new pdo_mysql())->connect;
        $this->table = $table;
    }

    /**
     * 选择数据表
     * @param string $table
     * @return Database
     * @uses 用于通过数据表生成 Database 实例
     */
    public static function table($table) {
        $db = new self($table);
        return $db;
    }

    /**
     * 选择模型(静态方法)
     * @param model $modelClass
     * @return Database
     * @uses 用于通过模型生成 Database 实例
     */
    public static function model($modelClass) {
        // 使用反射获取数据表名
        try{
            $reflect = new ReflectionClass($modelClass);
        }
        catch (ReflectionException $e) {
            echo 'Database Class Failed: ' . $e->getMessage();
        }
        // 创建新的 Database 实例
        $db = new self($reflect->getStaticPropertyValue('table'));
        $db->model = $modelClass;
        return $db;
    }

    /**
     * 选择模型(实例方法)
     * @param model $modelClass
     * @return Database
     * @uses 为实例选择 Model
     */
    public function setModel($modelClass) {
        // 使用反射获取数据表名
        try{
            $reflect = new ReflectionClass($modelClass);
        }
        catch (ReflectionException $e) {
            echo 'Database Class Failed: ' . $e->getMessage();
        }
        // 修改当前实例的数据表和 Model
        $this->table = $reflect->getStaticPropertyValue("table");
        $this->model = $modelClass;
        return $this;
    }

    // PDO类封装

    /**
     * SQL 语句预处理并执行
     * @param string $sqlStatement, array $parameters
     * @return boolean
     * @uses 用于预处理并执行语句，请注意本方法结合了 pdo 中 prepare 和 execute 两个方法
     */
    public function prepare($sqlStatement = '', $parameters = []){
        if (!empty($sqlStatement)) {
            $this->SQLStatement = $sqlStatement;
        }
        $this->PDOStatement = $this->PDOConnect->prepare($this->SQLStatement);
        return $this->PDOStatement->execute($parameters);
    }

    /**
     * 执行 SQL 语句
     * @param string $sqlStatement
     * @return Database
     */
    public function query($sqlStatement, $parameters = []){
        $this->prepare($sqlStatement, $parameters);
        return $this;
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
    public function beginTransaction() {
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

    // ORM 数据库查询方法

    public function select($sqlStatement = '*'){
        // TODO: 完善 select() 方式
        $this->SQLStatement = 'SELECT '.$sqlStatement;
        return $this;
    }

    /**
     * 通过数组条件检索数据表
     * @param array $sqlConditionArray
     * @return Database
     */
    public function where($sqlConditionArray = []){
        // 判断是否第一次执行
        if(empty($this->SQLStatement)) {
            // 判断 $sqlConditionArray 是否传入：加入空条件的判断使开发变得简便
            if(empty($sqlConditionArray)){
                // 未传入条件，显示全部数据
                $this->SQLStatement = 'SELECT * FROM '.$this->table;
            } else {
                // 传入条件，进行 SQL 语句拼接
                foreach ($sqlConditionArray as $key => $value) {
                    if (isset($sql)) {
                        $sql .= " AND ".$key.'="'.$value.'"';
                    } else {
                        $sql = $key.'="'.$value.'"';
                    }
                }
                $this->SQLStatement = 'SELECT * FROM '.$this->table.' WHERE ('.$sql.')';
            }
        } else {
            // 不是第一次执行，判断 $sqlConditionArray 是否传入
            if(empty($sqlConditionArray)){
                // 未传入条件，SQL语句不做任何改动
            } else {
                // 传入条件，进行 SQL 语句拼接
                foreach ($sqlConditionArray as $key => $value) {
                    if (isset($sql)) {
                        $sql .= " AND ".$key.'="'.$value.'"';
                    } else {
                        $sql = $key.'="'.$value.'"';
                    }
                }
                $this->SQLStatement = $this->SQLStatement.' AND ('.$sql.')';
            }
        }

        $this->prepare($this->SQLStatement);
        return $this;
    }

    /**
     * 通过 SQL 语句条件检索数据表
     * @param string $sqlConditionStatement
     * @return Database
     */
    public function whereRaw($sqlConditionStatement = ''){
        // 判断是否第一次执行
        if(empty($this->SQLStatement)) {
            if (empty($sqlConditionStatement)) {
                // 未传入条件，显示全部数据
                $this->objectSQL = 'SELECT * FROM ' . $this->table;
            } else {
                $this->SQLStatement = 'SELECT * FROM ' . $this->table . ' WHERE (' . $sqlConditionStatement . ')';
            }
        } else {
            // 判断 $sqlConditionArray 是否传入：加入空条件的判断使开发变得简便
            if (empty($sqlConditionStatement)) {
                // 未传入条件，SQL语句不做任何改动
            } else {
                // 传入条件，进行 SQL 语句拼接
                $this->SQLStatement = $this->SQLStatement . ' AND (' . $sqlConditionStatement . ')';
            }
        }

        $this->prepare($this->SQLStatement);
        return $this;
    }

    /**
     * 通过数组条件检索数据表
     * @param array $sqlConditionArray
     * @return Database
     */
    public function orWhere($sqlConditionArray = ''){
        // 判断 $sqlConditionArray 是否传入：加入空条件的判断使开发变得简便
        if(empty($sqlConditionArray)){
            // 未传入条件，SQL语句不做任何改动
        } else {
            // 传入条件，进行 SQL 语句拼接
            foreach ($sqlConditionArray as $key => $value) {
                if (isset($sql)) {
                    $sql .= " AND ".$key.'="'.$value.'"';
                } else {
                    $sql = $key.'="'.$value.'"';
                }
            }
            $this->SQLStatement = $this->SQLStatement.' OR ('.$sql.')';
        }

        $this->prepare($this->SQLStatement);
        return $this;
    }

    /**
     * 通过 SQL 语句条件检索数据表
     * @param string $sqlConditionStatement
     * @return Database
     */
    public function orWhereRaw($sqlConditionStatement = ''){
        // 判断 $sqlConditionArray 是否传入：加入空条件的判断使开发变得简便
        if(empty($sqlConditionStatement)){
            // 未传入条件，SQL语句不做任何改动
        } else {
            // 传入条件，进行 SQL 语句拼接
            $this->SQLStatement = $this->SQLStatement.' OR ('.$sqlConditionStatement.')';
        }

        $this->prepare($this->SQLStatement);
        return $this;
    }



    /**
     * 插入条目
     * @param array $data
     * @return boolean
     */
    public function insert($data){
        // 生成 count($data) 个 ?, 作为 SQL 语句 VALUES 占位符
        $sqlPlaceholder = "?";
        for ($i = 1; $i<count($data); $i++) {
            $sqlPlaceholder .= ',?';
        }
        // 执行语句进行插入
        $this->SQLStatement = 'INSERT INTO '.$this->table.' ('.implode(',',array_keys($data)).') VALUES ('.$sqlPlaceholder.')';

        return $this->prepare($this->SQLStatement,array_values($data));
    }

    /**
     * 更新条目
     * @param array $data
     * @return boolean
     */
    public function update($data){
        // 框架设计之初，没有考虑过单独保存每次 where、select、join 等的值；因此每次做 UPDATE 等操作时，必须重新抽离出 where，是经验不足导致设计时的缺陷，这个问题将会在下一个大版本中得到解决。
        // 下面 paginate() 方法使用了 explode 原因相同
        $rawWhereSQL = explode(" ", $this->SQLStatement);
        $where = implode("",array_slice($rawWhereSQL,5,count($rawWhereSQL)-5));

        foreach ($data as $key => $value) {
            if (isset($sql)) {
                $sql .= " , ".$key.'=?';
            } else {
                $sql = $key.'=?';
            }
        }
        $this->SQLStatement = 'UPDATE '.$this->table.' SET '.$sql.' WHERE '.$where;
        return $this->prepare($this->SQLStatement,array_values($data));
    }

    /**
     * 删除条目
     * @return boolean
     * @uses 用于更新当前操作的实例信息到数据库，
     */
    public function delete(){
        // 无奈拼接 where
        $rawWhereSQL = explode(" ", $this->SQLStatement);
        $where = implode("",array_slice($rawWhereSQL,5,count($rawWhereSQL)-5));
        // 执行 SQL 语句删除条目
        $this->SQLStatement = 'DELETE FROM '.$this->table.' WHERE '.$where;
        return $this->prepare($this->SQLStatement);
    }

    /**
     * Database 分页
     * @param int $pageNum, boolean $furtherPageInfo
     * @return Collection
     * @uses 数据库 LIMIT 语句调用
     */
    public function paginate($pageNum, $furtherPageInfo = true){
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $startAt = (($currentPage-1)*$pageNum);

        // 第一遍先获取到总数，这里对 SQL 语句重新整理
        // 实测当数据量达到 1000000 时，正则要比 explode() 函数慢 0.8s 左右，因此采用后者
        // $countSQL = preg_match("/SELECT (.*) WHERE/", $sql, $result);

        $rawSelectSQL = explode(" ", $this->SQLStatement)[1];
        $countSQL = str_replace($rawSelectSQL, 'COUNT('.$rawSelectSQL.')', $this->SQLStatement);
        $total =  $this->PDOConnect->query($countSQL)->fetch()[0];


        // 拼接 SQL 语句：select * from table limit start,pageNum
        $this->SQLStatement = $this->SQLStatement." LIMIT ".$startAt.",".$pageNum;;
        $this->prepare($this->SQLStatement);


        // 返回集合
        return Collection::make($this->fetchAll())->format($this->model)->forPage($pageNum, $currentPage, $total, $furtherPageInfo);

    }

    /**
     * 数据获取
     * @return Collection
     * @uses 取出数据库内容，并以 Collection 集合返回。用于将 Database 层的数据转换至 Collection
     */
    public function get(){
        return Collection::make($this->fetchAll())->format($this->model);
    }
}
