<?php
namespace System\Http;
use System\Interfaces\Jsonable;
/**
 * PHP-QuickORM 框架 HTTP 响应类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */
Class Response
{
    private $httpVersion = "HTTP/1.1";

    protected $header;
    protected $location;

    protected $data;
    protected $statusCode;
    protected $errcode;
    protected $errmsg;

    public function __construct(){
        // 如果需要定制默认 header，可以在此操作
        $header[] = "X-Powered-By: PHP-QuickORM";
    }

    public function json($data, $statusCode = 200){
        if($data instanceof Jsonable){
            echo $data;
        } else {
            echo json_encode($data);
        }
    }

    public function redirect($url = null){

    }

    public function back(){

    }

    public function dataEncode($data, $statusCode, $errcode = 0, $errmsg = ''){

    }

    public function setHeader(){

    }

    public function getHeader(){

    }
}