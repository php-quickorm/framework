<?php
namespace System;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use System\Interfaces\Jsonable;

/**
 * PHP-JSORM 框架的数据集合类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-JSORM
 */

class Collection implements ArrayAccess, Countable, IteratorAggregate
{

    public $dataArray = [];

    // 新建 Collection 方法

    public function __construct($dataArray = []) {
        $this->dataArray = $dataArray;
    }

    /**
     * 将 array 转换为 Collection
     * @return Collection
     * @uses 用于将 array 转换为 Collection 的静态方法，与构造方法一致。专为兼顾 Laravel 党的优雅而生！
     */
    public static function make($dataArray = []){
        return new static($dataArray);
    }


    // 栈与队列数据结构封装

    /**
     * 元素进栈
     * @param mixed $value
     * @return Collection
     * @uses 将元素插入 Collection 尾部
     */
    public function push($value){
        array_push($this->dataArray, $value);
        return $this;
    }

    /**
     * 元素出栈
     * @return Collection
     * @uses 将 Collection 最后一个元素弹出
     */
    public function pop(){
        array_pop($this->dataArray);
        return $this;
    }

    /**
     * 元素出队列
     * @return Collection
     * @uses 将 Collection 首个元素弹出
     */
    public function shift(){
        array_shift($this->dataArray);
        return $this;
    }

    /**
     * 元素插入至头部
     * @param mixed $value
     * @return Collection
     * @uses 将元素插入 Collection 首个位置
     */
    public function unShift($value){
        array_unshift($this->dataArray, $value);
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
            $this->dataArray = array_merge($this->dataArray, $array->dataArray);
        } else {
            $this->dataArray = array_merge($this->dataArray, $array);
        }
        return $this;
    }

    /**
     * 获取集合元素个数
     * @return int
     */
    public function count() {
        return count($this->dataArray);
    }

    /**
     * 获取集合首个元素
     * @return mixed
     */
    public function first(){
        return $this->dataArray[0];
    }

    /**
     * 获取集合最后一个元素
     * @return mixed
     */
    public function last(){
        return $this->dataArray[count($this->dataArray)-1];
    }


    // ORM 聚合方法

    /**
     * 元素排序
     * @param string $sortFunction, callable $callback
     * @return Collection
     * @uses Collection 元素排序，默认为 asrot，若调用其余排序函数请传入参数 sortFunction
     */
    public function sort($sortFunction = "asort",$callback = null){
        if (is_null($callback)) {
            $sortFunction($this->dataArray);
        } else {
            $sortFunction($this->dataArray, $callback);
        }
        return $this;
    }

    public function max() {

    }

    public function min() {

    }

    public function average() {

    }

    public function sum() {

    }


    // ORM 实用方法

    public function orderBy(){

    }

    public function paginate(){

    }


    // PHP ArrayAccess 接口支持

    /**
     * 判断一个 key 是否存在
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key) {
        return array_key_exists($key, $this->dataArray);
    }

    /**
     * 通过 key / offset 获取数组的值
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->dataArray[$key];
    }

    /**
     * 定义数组里的一个元素
     * @param  mixed  $key
     * @param  mixed  $value
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->dataArray[] = $value;
        } else {
            $this->dataArray[$key] = $value;
        }
    }

    /**
     * 删除数组里的一个元素
     * @param  string  $key
     */
    public function offsetUnset($key)
    {
        unset($this->dataArray[$key]);
    }

    /**
     * IteratorAggregate 迭代支持
     * @return string
     */
    public function getIterator()
    {
        return new ArrayIterator($this->dataArray);
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
        },$this->dataArray),$option);
    }
}
