<?php
/**
 * PHP-QuickORM 框架的公用函数
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */

// 调试封装
if(!function_exists('dump')){
    /**
     * @param $toDo
     * @uses 打印变量
     */
    function dump($toDo){
        echo "<pre>";
        if (is_array($toDo)) {
            print_r($toDo);
        } elseif (is_object($toDo)) {
            print_r($toDo);
        } else {
            var_dump($toDo);
        }
    }
}

if(!function_exists('dd')){
    /**
     * @param $toDo
     * @uses 打印变量并停止运行
     */
    function dd($toDo){
        echo "<pre>";
        if (is_array($toDo)) {
            print_r($toDo);
        } elseif (is_object($toDo)) {

            print_r($toDo);
        } else {
            var_dump($toDo);
        }
        die();
    }
}