<?php
namespace System;
use System\Database;
use System\Interfaces\Jsonable;
/**
 * PHP-JSORM 框架的 Model 基本类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-JSORM
 */

class Model implements Jsonable
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
        $db = new Database();
        $db->prepare($sql,[$id]);
        // 根据数据库返回的数据集创建实例, 此时需要判断是否为空并返回 null，否则空实例会导致框架使用起来很不顺手..
        $result = $db->fetch();
        if (empty($result)) {
            return null;
        } else {
            return new static($result, $sql);
        }
    }

    /**
     * 通过数组条件检索数据表
     * @param array $sqlConditionArray
     * @return Collection
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

        $db = new Database();
        $db->prepare($sql);
        return static::makeCollection($db->fetchAll(), $sql);
	}

    /**
     * 通过 SQL 语句条件检索数据表
     * @param string $sqlConditionStatement
     * @return Collection
     */
    public static function whereRaw($sqlConditionStatement){
        echo $sql = 'SELECT * FROM '.static::$table.' WHERE '.$sqlConditionStatement;
        $db = new Database();
        $db->prepare($sql);
        return static::makeCollection($db->fetchAll(), $sql);
    }

    /**
     * 执行 SQL 语句
     * @param string $sqlStatement
     * @return Collection
     */
    public static function raw($sqlStatement){
        $sql = str_replace("{table}",static::$table,$sqlStatement);
        $db = new Database();
        $db->prepare($sql);
        return static::makeCollection($db->fetchAll(), $sql);
    }

    /**
     * 数据表字段检索
     * @param string $field, string $string
     * @return Collection
     */
    public static function search($field,$string){
        $sql = 'SELECT * FROM '.static::$table.' WHERE '.$field.' LIKE "'.$string.'"';
        $db = new Database();
        $db->prepare($sql);
        return static::makeCollection($db->fetchAll(), $sql);
    }


    // ORM 修改更新

    /**
     * 保存当前操作的实例
     * @param array $objectData
     * @uses 用于更新或保存当前操作的实例信息到数据库
     * @return boolean
     */
    public function save($objectData = []){
        // 此方法要防注入
        if (empty($objectData)){
            $objectData = $this->objectData;
        }
        // 拼接 SQL 语句，判断是新增还是更新
        if (is_null(static::find($this->id))) {
            // 生成 count($this->objectData) 个 ?, 作为占位符
            $sqlPlaceholder = "?";
            for ($i = 1; $i<count($this->objectData); $i++) {
                $sqlPlaceholder .= ',?';
            }

            $sql = 'INSERT INTO '.static::$table.' ('.implode(',',array_keys($objectData)).') VALUES ('.$sqlPlaceholder.')';
        } else {
            foreach ($objectData as $key => $value) {
                if (isset($sql)) {
                    $sql .= " , ".$key.'=?';
                } else {
                    $sql = $key.'=?';
                }
            }
            $sql = 'UPDATE '.static::$table.' SET '.$sql.' WHERE id='.$this->id;
        }

        // 执行语句，更新数据库
        $db = new Database();
        return $db->prepare($sql,array_values($objectData));
    }

    /**
     * 更新当前操作的实例
     * @param array $objectData
     * @return boolean
     * @uses 用于更新当前操作的实例信息到数据库，
     */
    public function update($objectData){
        return $this->save($objectData);
    }

    public function delete(){
        $sql = 'DELETE FROM '.static::$table.' WHERE id=?';
        $db = new Database();
        return $db->prepare($sql,[$this->id]);
    }


    /**
     * 通过关联数组创建实例并保存
     * @param array $array, string $sqlStatementCache
     * @return Model
     * @uses 在新建时可通过此类创建实例
     */
    public static function create($array = [], $sqlStatementCache = '') {
        if (empty($array)) {
            return null;
        } else {
            $model = new static($array,$sqlStatementCache);
            if($model->save()){
                return $model;
            }
        }

    }

     // Model 类构造方法

    /**
     * 通过关联数组创建实例并保存
     * @param array $objectData, string $sqlStatementCache
     * @uses 在新建或调用一条记录时可通过此类创建实例
     */
    public function __construct($objectData = [], $sqlStatementCache = '') {
        $this->objectData = $objectData;
        $this->objectSQL = $sqlStatementCache;
    }

    /**
     * 通过二维数组创建实例数组
     * @param array $array, string $sqlStatementCache
     * @return Collection
     * @uses 在新建或调用多条记录时可通过此类创建实例数组
     */
    public static function makeCollection($array = [], $sqlStatementCache = '') {
        $objectArray = [];
        // 构建实例数组
        foreach($array as $key => $value) {
            $model = new static($value,$sqlStatementCache);
            if (!is_null($model)) {
                array_push($objectArray,$model);
            }
        }
        return Collection::make($objectArray);
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


    /**
     * 将实例转换为字符串
     * @return string
     */
    public function __toString() {
        return $this->toJson();
    }

    /**
     * 将实例转换为 JSON 字符串
     * @return string
     */
    public function toJson($option = 0) {
        return json_encode($this->objectData, $option);
    }
}