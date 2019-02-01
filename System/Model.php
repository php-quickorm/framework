<?php
namespace System;
use System\Database;
use System\Interfaces\Jsonable;
/**
 * PHP-QuickORM 框架的 Model 基本类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */

class Model implements Jsonable
{
	protected static $table = '';
	protected $objectData = [];


	// ORM 静态查询部分
    // 实际上是对 Database 的封装，方便程序员开发

    // 数据库层方法：select()、where()、whereRaw()、orWhere()、orWhereRaw() 返回 Database 对象
    // 数据库层方法：paginate() 返回 Collection 对象
    // 模型层方法：find() 返回 Model 对象
    // 模型层方法：search()、all() 返回 Collection 对象


	/**
     * 通过 id 查询数据表
     * @param int $id
     * @return Model
     */
	public static function find($id){
        $db = new Database(static::$table);
        if($result = $db->where([ 'id' => intval($id)])->fetch()){
            return new static($result);
        } else {
            return null;
        }

    }

    /**
     * 显示全部数据
     * @return Collection
     */
    public static function all(){
        $db = new Database(static::$table);
        return Collection::make($db->where()->fetchAll())->format(static::class);
    }

    /**
     * 数据表字段检索
     * @param string $field
     * @param string $string
     * @return Collection
     */
    public static function search($field,$string){
        $db = new Database(static::$table);
        return Collection::make($db->whereRaw($field.' LIKE "'.$string.'"')->fetchAll())->format(static::class);
    }

    /**
     * 通过数组条件检索数据表
     * @param array $sqlConditionArray
     * @return Database
     */
    public static function where($sqlConditionArray = []){
        return Database::model(static::class)->where($sqlConditionArray);
	}

    /**
     * 通过 SQL 语句条件检索数据表
     * @param string $sqlConditionStatement
     * @return Database
     */
    public static function whereRaw($sqlConditionStatement = ''){
        return Database::model(static::class)->whereRaw($sqlConditionStatement);
    }

    /**
     * 执行 SQL 语句
     * @param string $sqlStatement
     * @return Database
     */
    public static function raw($sqlStatement){
        return Database::model(static::class)->query(str_replace("{table}", static::$table,$sqlStatement));
    }

    /**
     * Model 分页
     * @param int $pageNum
     * @param boolean $furtherPageInfo
     * @return Collection
     * @uses Model 集合分页功能
     */
    public static function paginate($pageNum, $furtherPageInfo = true){
        return static::where()->paginate($pageNum, $furtherPageInfo);
    }

    /*
     * 不知道有多少人会查阅我这个框架的源码，其实仔细看看我上面那几个查询的方法，你就会发现其实 find() 方法可以调用 where() 方法实现，where() 方法可以调用 whereRaw() 方法实现，whereRaw() 方法可以调用 raw() 方法实现，想要省代码的话可以省到极致，不过耦合度就要妥协了。具体的实现方法，请翻阅 Database 类的源码
     * Rytia, 2018.08.12 凌晨
     * */


    /**
     * 保存当前操作的实例
     * @param array $objectData
     * @uses 用于更新或保存当前操作的实例信息到数据库
     * @return boolean
     */
    public function save($objectData = []){

        if (empty($objectData)){
            $objectData = $this->objectData;
        }

        // 判断是新增还是更新
        if (is_null(static::find($this->id))) {
            // 插入新的条目到数据表
            return Database::table(static::$table)->insert($objectData);
        } else {
            // 更新已有的条目
            return Database::table(static::$table)->where(['id' => $this->id])->update($objectData);
        }

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

    /**
     * 删除当前操作的实例
     * @return boolean
     * @uses 用于更新当前操作的实例信息到数据库，
     */
    public function delete(){
        return Database::table(static::$table)->where(['id' => $this->id])->delete( );
    }


    /**
     * 通过关联数组创建实例并保存
     * @param array $array, string $sqlStatementCache
     * @return Model
     * @uses 在新建时可通过此类创建实例
     */
    public static function create($array = []) {
        if (empty($array) || !is_array($array)) {
            return null;
        } else {
            $model = new static($array);
            if($model->save()){
                return $model;
            }
        }

    }

     // Model 类构造方法

    /**
     * 通过关联数组创建实例并保存
     * @param array $objectData
     * @uses 在新建或调用一条记录时可通过此类创建实例
     */
    public function __construct($objectData = []) {
        $this->objectData = $objectData;
    }

    /**
     * 通过二维数组创建实例数组
     * @param array $array
     * @return Collection
     * @uses 在新建或调用多条记录时可通过此类创建实例数组
     */
    public static function makeCollection($array = []) {
        return Collection::make($array)->format(static::class);
    }


    // Model 类重载

    public function __set($name, $value) {
        // $demo->property = "text";

        // 通过反射判断是否已经预定义修饰器
        try {
            $modelClass = new \ReflectionClass(static::class);
            if ($modelClass->hasMethod("set".ucfirst($name))){
                $methodName = "set".ucfirst($name);
                $value = $this->$methodName($value);
            }
        } catch (\ReflectionException $e) {
            // do nothing..
        } finally {
            return $this->objectData[$name] = $value;
        }
    }

    public function __get($name) {
        // echo $demo->property

        // 通过反射判断是否已经预定义访问器
        try {
            $modelClass = new \ReflectionClass(static::class);
            if ($modelClass->hasMethod("get".ucfirst($name))){
                $methodName = "get".ucfirst($name);
                return $this->$methodName();
            }
        } catch (\ReflectionException $e) {
            // do nothing..
        } finally {
            if (isset($this->objectData[$name])){
                return $this->objectData[$name];
            } else {
                return null;
            }
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
    
//    public function __call($name, $arguments) {
//        return Database::model(static::class)->$name(...$arguments);
//    }
//
//    public static function __callStatic($name, $arguments) {
//        return Database::model(static::class)->$name(...$arguments);
//    }


    /**
     * 将实例转换为字符串
     * @return string
     */
    public function __toString() {
        return $this->toJson();
    }

    /**
     * 将实例转换为 JSON 字符串
     * @param $option
     * @return string
     */
    public function toJson($option = 0) {
        return json_encode($this->objectData, $option);
    }

}