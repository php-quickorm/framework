<?php
namespace System;
use System\Database;
/**
 * PHP-JSORM 框架的 Model 基本类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-JSORM
 */

class Model
{
	protected static $table = '';
	protected $objectSQL;
	protected $objectData = [];


	// ORM 查询部分

	/**
     * 通过 id 查询数据表
     * @param int $id
     * @return Model
     */
    public static function find($id){
        $sql = "SELECT * FROM ".static::$table." WHERE id=?";
        $db = new Database($sql,[$id]);
        return self::create($db->fetch(), $sql);
    }

    /**
     * 通过数组条件检索数据表
     * @param array $sqlConditionArray
     * @return array
     */
	public static function where($sqlConditionArray){
		// SQL 语句拼接
		foreach ($sqlConditionArray as $key => $value) {
		    if (isset($sql)) {
                $sql .= " AND ".$key.'="'.$value.'"';
            } else {
		        $sql = $key.'="'.$value.'"';
            }
		}
		$sql = 'SELECT * FROM '.static::$table.' WHERE '.$sql;

        $db = new Database($sql);
        return self::createArray($db->fetchAll(), $sql);
	}

    /**
     * 通过 SQL 语句条件检索数据表
     * @param string $sqlConditionStatement
     * @return array
     */
    public static function whereRaw($sqlConditionStatement){
        echo $sql = 'SELECT * FROM '.static::$table.' WHERE '.$sqlConditionStatement;
        $db = new Database($sql);
        return self::createArray($db->fetchAll(), $sql);
    }

    /**
     * 执行 SQL 语句
     * @param string $sqlStatement
     * @return array
     */
    public static function raw($sqlStatement){
        $sql = str_replace("{table}",static::$table,$sqlStatement);
        $db = new Database($sql);
        return self::createArray($db->fetchAll(), $sql);
    }

    /**
     * 数据表字段检索
     * @param string $field, string $string
     * @return array
     */
    public static function search($field,$string){
        echo $sql = 'SELECT * FROM '.static::$table.' WHERE '.$field.' LIKE "'.$string.'"';
        $db = new Database($sql);
        return self::createArray($db->fetchAll(), $sql);
    }


     // Model 类构造方法

    public function __construct($sqlStatementCache = '') {
        $this->objectSQL = $sqlStatementCache;
    }

    /**
     * 通过关联数组创建对象
     * @param array $array, string $sqlStatementCache
     * @return Model
     * @uses 在新建或调用一条记录时可通过此类创建对象
     */
    public static function create($array = [], $sqlStatementCache = '') {
        $model = new static($sqlStatementCache);
        $model->objectData = $array;
        return $model;
    }

    /**
     * 通过二维数组创建对象数组
     * @param array $array, string $sqlStatementCache
     * @return array
     * @uses 在新建或调用多条记录时可通过此类创建对象数组
     */
    public static function createArray($array = [], $sqlStatementCache = '') {
        $objectArray = [];
        // 构建对象数组
        foreach($array as $key => $value) {
            $model = new static($sqlStatementCache);
            $model->objectData = $array[$key];
            array_push($objectArray,$model);
        }
        return $objectArray;
    }


    // Model 类重载

    public function __set($name, $value) {
        // $demo->property = "text";
        return $this->objectData[$name] = $value;
    }

    public function __get($name) {
        // echo $demo->property
	    if (isset($this->objectData[$name])){
	        return $this->objectData[$name];
        } else {
	        return null;
        }
    }

    public function __isset($name) {
        // isset($demo->property)
	    return isset($this->objectData[$name]);
    }

    public function __unset($name) {
        // unset($demo->property)
        unset($this->objectData[$name]);
    }
}