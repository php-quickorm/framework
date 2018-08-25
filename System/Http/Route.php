<?php
namespace System\Http;
use ReflectionClass;
use ReflectionException;
/**
 * PHP-QuickORM 框架的路由系统
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */
Class Route
{
    /**
     * 路由系统初始化
     * @param $request
     * @param $response
     * @param $autoRewrite
     */
    public static function initialize(Request $request, Response $response, $autoRewrite = false){

        // 划分请求
        $requestArray = self::getRequestArray($request, $autoRewrite);
        $controllerName = array_shift($requestArray)."Controller";
        $controllerMethod = array_shift($requestArray).ucfirst($request->getMethod());
        // 通过反射调用相应控制器
        try {
            $controller = new ReflectionClass('Controller\\'.$controllerName);
            $controller->newInstance($request, $response)->$controllerMethod(...$requestArray);
        } catch (ReflectionException $exception){
            trigger_error("未找到相应页面", E_USER_ERROR);
        }

    }

    /**
     * 把请求 URL 划分为数组
     * @param Request $request
     * @param bool $autoRewrite
     * @return array
     */
    public static function getRequestArray(Request $request, $autoRewrite = false){
        if($autoRewrite){
            // 划分请求链接
            $requestUrl = $request->getPath();
            $requestArray = explode("/",$requestUrl);
            // 去除第一个元素
            array_shift($requestArray);
            return $requestArray;
        } else {
            // 划分请求链接
            $requestUrl = $_SERVER['QUERY_STRING'];
            return explode("/",$requestUrl);
        }
    }
}