<?php
namespace System;

use Countable;
use ArrayAccess;
use System\Interfaces\Jsonable;
use Traversable;
use ArrayIterator;
use IteratorAggregate;

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

    public function push(){

    }

    public function pop(){

    }

    public function shift(){

    }


    public function sort(){

    }

    public function merge(){

    }

    public function count() {

    }


    // ORM 聚合方法

    public function max() {

    }

    public function min() {

    }

    public function average() {

    }

    public function sum() {

    }

    public function first(){

    }

    public function last(){

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
