<?php
namespace System;
/**
 * PHP-QuickORM 框架的 Controller 基本类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */
class Controller
{
    protected $request;

    public function response($data = null, $statusCode = 200){
        return $data;
    }
}