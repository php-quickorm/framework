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
    protected $response;

    public function response($data = null, $statusCode = 200, $errcode = 0, $errmsg = ''){
        if(is_null($data)){
            return $this->response;
        } else {
            return $this->response->dataEncode($data, $statusCode, $errcode, $errmsg);
        }
    }

    public function request($field){
        if(is_null($field)){
            return $this->request;
        } else {
            return $this->request->get($field);
        }
    }

    public function __construct($request, $response){
        $this->request = $request;
        $this->response = $response;
    }
}