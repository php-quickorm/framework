<?php
require __DIR__.'/vendor/autoload.php';

/**
 * PHP-Quick-ORM
 *
 * @description: a simple PHP ORM framework to built up api server
 * @author: Rytia
 */

// 此为测试环境启动器，无需伪静态规则即可运行
// 如果您已将产品真正投入使用，请将您的工作目录设置为 /Public

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$query = explode("/",$request);
array_shift($query); // 去除数组第一个空元素

call_user_func(['Controller\\'.array_shift($query),array_shift($query)],...$query);
