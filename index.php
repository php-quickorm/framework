<?php
require __DIR__.'/vendor/autoload.php';

/**
 * PHP-QuickORM
 *
 * @description: a simple PHP ORM framework to built up api server
 * @author: Rytia
 */

// 此为测试环境启动器，无需伪静态规则即可运行
// 如果您已将产品真正投入使用，请将您的工作目录设置为 /Public


require_once __DIR__."/App/System/Utilities/Functions.php";
set_error_handler('\System\Utilities\Exception::errorReport');
set_exception_handler('\System\Utilities\Exception::exceptionReport');

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$query = explode("/",$request);
array_shift($query); // 去除数组第一个空元素

// TODO: 重新设计路由系统，Controller 需要实例化
$controller = new ReflectionClass('Controller\\'.array_shift($query));
$function = array_shift($query);
$controller->newInstance()->$function(...$query);
