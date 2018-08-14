<?php
namespace System;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use System\Interfaces\Jsonable;

/**
 * PHP-QuickORM 框架的数据集合类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-JSORM
 */

class Collection implements ArrayAccess, Countable, IteratorAggregate
{

    protected $collectionItems = [];

    // 为 Collection 分页方法提供支持
    public $collectionPages;

    // 新建 Collection 方法
    public function __construct($collectionItems = []) {
        $this->collectionItems = $collectionItems;
    }

    /**
     * 将 array 转换为 Collection
     * @return Collection
     * @uses 用于将 array 转换为 Collection 的静态方法，与构造方法一致。专为兼顾 Laravel 党的优雅而生！
     */
    public static function make($collectionItems = []){
        return new static($collectionItems);
    }


    // 栈与队列数据结构封装

    /**
     * 元素进栈
     * @param mixed $value
     * @return Collection
     * @uses 将元素插入 Collection 尾部
     */
    public function push($value){
        array_push($this->collectionItems, $value);
        return $this;
    }

    /**
     * 元素出栈
     * @return Collection
     * @uses 将 Collection 最后一个元素弹出
     */
    public function pop(){
        array_pop($this->collectionItems);
        return $this;
    }

    /**
     * 元素出队列
     * @return Collection
     * @uses 将 Collection 首个元素弹出
     */
    public function shift(){
        array_shift($this->collectionItems);
        return $this;
    }

    /**
     * 元素插入至头部
     * @param mixed $value
     * @return Collection
     * @uses 将元素插入 Collection 首个位置
     */
    public function unShift($value){
        array_unshift($this->collectionItems, $value);
        return $this;
    }

    /**
     * 集合合并
     * @param Collection|array $array
     * @return Collection
     * @uses 将一个 Collection 或 Array 合并到当前实例
     */
    public function merge($array){
        if( $array instanceof Collection){
            $this->collectionItems = array_merge($this->collectionItems, $array->collectionItems);
        } else {
            $this->collectionItems = array_merge($this->collectionItems, $array);
        }
        return $this;
    }

    /**
     * 获取集合元素个数
     * @return int
     */
    public function count() {
        return count($this->collectionItems);
    }

    /**
     * 获取集合首个元素
     * @return mixed
     */
    public function first(){
        return $this->collectionItems[0];
    }

    /**
     * 获取集合最后一个元素
     * @return mixed
     */
    public function last(){
        return $this->collectionItems[count($this->collectionItems)-1];
    }

    /**
     * 检测集合是否为空
     * @return boolean
     */
    public function isEmpty(){
        return empty($this->collectionItems);
    }

    /**
     * 检测元素是否在集合中或某集合是否为该集合的子集
     * @param mixed $collection
     * @return boolean
     */
    public function contains($collection){
        if ( $collection instanceof Collection || is_array($collection)){
            // 传入参数为 Collection 或 Array 的情况：逐个判断是否包含
            foreach ($collection as $value){
                if(!in_array($value,$this->collectionItems)){
                    return false;
                }
            }
            return true;
        } else {
            // 传入参数为 其他元素 的情况：直接判断
            return in_array($collection, $this->collectionItems);
        }
    }


    // ORM 聚合方法

    /**
     * 元素基础排序
     * @param string $sortFunction, callable $callback
     * @return Collection
     * @uses Collection 元素排序，排序方法默认为 PHP 自带的 asrot (底层由变种的快排实现，时间复杂度为 o(NLogN))，若调用其余排序方法请传入参数 sortFunction
     */
    public function sort($orderBy = "ASC", $sortFunction = "asort",$callback = null){
        if (is_null($callback)) {
            $sortFunction($this->collectionItems);
        } else {
            $sortFunction($this->collectionItems, $callback);
        }
        if ($orderBy == "DESC"){
            $this->collectionItems = array_reverse($this->collectionItems);
        }
        return $this;
    }

    /**
     * 获取(某个字段)最大的元素
     * @param string $field
     * @return mixed
     * @uses 用于获取(某个字段)最大的元素；若集合中的数据类型为 Model 实例，请传入比较所依据的字段
     */
    public function max($field = null) {
        // 根据数据类型做出判断
        if ( !is_null($field) && $this->collectionItems[0] instanceof Model) {
            // 如果是 Model 则做简单打擂比较(时间复杂度为 o(N))
            // 有人阅读源码看到这里，请注意没有调用 $this->sort() 是为了减少时间复杂度和降低耦合度，下同
            $target = 0;
            $maxData = $this->collectionItems[$target]->$field;
            foreach ($this->collectionItems as $key => $value) {
                if ($value->$field > $maxData){
                    $target = $key;
                    $maxData = $this->collectionItems[$target]->$field;
                }
            }
            return $this->collectionItems[$target];
        } else {
            // 如果是其他类型则调用 PHP 自带函数
            return max($this->collectionItems);
        }
    }

    /**
     * 获取(某个字段)最小的元素
     * @param string $field
     * @return mixed
     * @uses 用于获取(某个字段)最小的元素；若集合中的数据类型为 Model 实例，请传入比较所依据的字段
     */
    public function min($field = null) {
        // 根据数据类型做出判断
        if ( !is_null($field) && $this->collectionItems[0] instanceof Model) {
            // 如果是 Model 则做简单打擂比较(时间复杂度为 o(N))
            $target = 0;
            $maxData = $this->collectionItems[$target]->$field;
            foreach ($this->collectionItems as $key => $value) {
                if ($value->$field < $maxData){
                    $target = $key;
                    $maxData = $this->collectionItems[$target]->$field;
                }
            }
            return $this->collectionItems[$target];
        } else {
            // 如果是其他类型则调用 PHP 自带函数
            return min($this->collectionItems);
        }
    }

    /**
     * 获取元素(某个字段)算术平均值
     * @param string $field
     * @return int|float
     * @uses 用于获取元素(某个字段)数值总和；若集合中的数据类型为 Model 实例，请传入比较所依据的字段
     */
    public function average($field = null) {
        // 根据数据类型做出判断
        if ( !is_null($field) && $this->collectionItems[0] instanceof Model) {
            // 如果是 Model 调用 $this->sum / $this->count()
            return $this->sum($field) / $this->count();
        } else {
            // 如果是其他类型则调用 PHP 自带函数
            return array_sum($this->collectionItems)/count($this->collectionItems);
        }
    }

    /**
     * 获取元素(某个字段)数值总和
     * @param string $field
     * @return int|float
     * @uses 用于获取元素(某个字段)数值总和；若集合中的数据类型为 Model 实例，请传入比较所依据的字段
     */
    public function sum($field = null) {
        // 根据数据类型做出判断
        if ( !is_null($field) && $this->collectionItems[0] instanceof Model) {
            // 如果是 Model 则做简单累加(时间复杂度为 o(N))
            $sum = 0;
            foreach ($this->collectionItems as $key => $value) {
                $sum += $value->$field;
            }
            return $sum;
        } else {
            // 如果是其他类型则调用 PHP 自带函数
            return array_sum($this->collectionItems);
        }
    }


    // ORM 实用方法

    /**
     * 将集合中的关联数组转换为实例数组
     * @param model $modelClass, string $sqlStatementCache
     * @return Collection
     * @uses 在新建或调用多条记录时可通过此类创建实例数组
     */
    public function format($modelClass, $sqlStatementCache = '') {
        $objectArray = [];
        // 构建实例数组
        foreach($this->collectionItems as $key => $value) {
            $model = new $modelClass($value,$sqlStatementCache);
            if (!is_null($model)) {
                array_push($objectArray,$model);
            }
        }
        return new Collection($objectArray,$sqlStatementCache);
    }


    /**
     * 依据字段排序元素
     * @param string $field, string $orderBy
     * @return Collection
     * @uses Collection 元素排序，集合中的元素应为 Model 实例，且传入比较所依据的字段
     */
    public function sortBy($field = null, $method = "ASC"){
        // 储存临时变量 $field，否则在 uasort() 函数内部无法访问；这个方法不太好，仍需优化
        $dataTmp = $this->collectionItems;
        $this->collectionItems[$this->count()] = $field;
        if (!is_null($field) && is_object($this->collectionItems[0])) {
            uasort($dataTmp,function($x,$y){
                $field = $this->collectionItems[$this->count()-1];
                if($x->$field > $y->$field){
                    return 1;
                } elseif ($x->$field < $y->$field) {
                    return -1;
                } else {
                    return 0;
                }
            });
        }
        // 根据排序方式重新赋值
        if ($method == "DESC"){
            $this->collectionItems = array_reverse($dataTmp);
        } else {
            $this->collectionItems = $dataTmp;
        }
        return $this;
    }

    /**
     * 集合分页输出
     * @param int $pageNum, boolean $furtherPageInfo
     * @return Collection
     * @uses Collection 分页功能
     */
    public function paginate($pageNum, $furtherPageInfo = true){
        // 保存集合总大小
        $total = $this->count();
        // 获取当前页码
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $startAt = (($currentPage-1)*$pageNum);
        // 使用 PHP 原生切片
        $this->collectionItems = array_slice($this->collectionItems,$startAt,$pageNum);
        // 调用分页构造方法
        return $this->forPage($pageNum, $currentPage, $total, $furtherPageInfo);
    }

    /**
     * 集合分页构造
     * @param int $pageNum, int $currentPage, int $total， boolean $furtherPageInfo
     * @return Collection
     * @uses Collection 分页功能
     */
    public function forPage($pageNum, $currentPage, $total, $furtherPageInfo = true){

        if($furtherPageInfo){
            // 添加分页的属性
            $this->collectionPages = new \stdClass();
            $this->collectionPages->currentPage = $currentPage;
            $this->collectionPages->totalItems = $total;
            $this->collectionPages->totalPages = ceil($total / $pageNum);
            $this->collectionPages->hasNext = ($this->collectionPages->totalPages > $currentPage) ? "true" : "false";
            $this->collectionPages->nextUrl =($this->collectionPages->hasNext == "true") ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'?page='.($currentPage+1) : NULL;
        }

        return $this;
    }


    // PHP ArrayAccess 接口支持

    /**
     * 判断一个 key 是否存在
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key) {
        return array_key_exists($key, $this->collectionItems);
    }

    /**
     * 通过 key / offset 获取数组的值
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->collectionItems[$key];
    }

    /**
     * 定义数组里的一个元素
     * @param  mixed  $key
     * @param  mixed  $value
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->collectionItems[] = $value;
        } else {
            $this->collectionItems[$key] = $value;
        }
    }

    /**
     * 删除数组里的一个元素
     * @param  string  $key
     */
    public function offsetUnset($key)
    {
        unset($this->collectionItems[$key]);
    }

    /**
     * IteratorAggregate 迭代支持
     * @return string
     */
    public function getIterator()
    {
        return new ArrayIterator($this->collectionItems);
    }

    /**
     * 将集合转换为字符串
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
        return json_encode(array_map(function ($value){
            if ( $value instanceof Jsonable) {
                return json_decode($value);
            } else {
                return $value;
            }
        },$this->collectionItems),$option);
    }
}
