<?php
/**
 * PHP-QuickORM 框架的公用函数
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */

// 调试封装
if(!function_exists('dump')){
    /**
     * @param $param
     * @uses 打印变量
     */
    function dump($param){
        echo "<pre>";
        if (is_array($param)) {
            print_r($param);
        } elseif (is_object($param)) {
            print_r($param);
        } else {
            var_dump($param);
        }
    }
}

if(!function_exists('dd')){
    /**
     * @param $param
     * @uses 打印变量并停止运行
     */
    function dd($param){
        echo "<pre>";
        dump($param);
        die();
    }
}