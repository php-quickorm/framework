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


    /**
     * Response 构造函数
     */
    public function __construct(){
        // 如果需要定制默认 header，可以在此操作
        $this->header["X-Powered-By"] = "PHP-QuickORM";
    }

    /**
     * 将数据以 JSON 格式输出
     * @param $data
     * @param int $statusCode
     */
    public function json($data, $statusCode = 200){
        // 返回响应头部
        $this->executeHeader($statusCode);
        // 返回 json 数据
        if($data instanceof Jsonable){
            // 如果实现了 Jsonable，则在显示时会调用 toJson() 转换为字符串
            echo $data;
        } else {
            // 其他类型数据直接格式化为 json
            echo json_encode($data);
        }
    }

    /**
     * 重定向页面
     * @param null $url
     */
    public function redirect($url = null){
        // 添加重定向字段到响应头部
        $this->setHeader("location", $url);
        $this->executeHeader(302);
    }

    /**
     * 重定向至上一页
     */
    public function back(){
        // 重定向到 REFERER
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 将数据以标准 JSON 对象返回
     * @param $data
     * @param $statusCode
     * @param int $errcode
     * @param string $errmsg
     */
    public function dataEncode($data, $statusCode = 200, $errcode = 0, $errmsg = ''){
        // 同理，如果实现了 Jsonable 则先反转，这里是的简化版
        $data = ($data instanceof Jsonable) ? json_decode($data) : $data;

        // 返回的对象
        $result = [
            'errcode'   =>  $errcode,
            'errmsg'    =>  $errmsg,
            'data'      =>  $data
        ];
        $this->json($result,$statusCode);
    }

    /**
     * 将 Collection 集合以标准 JSON 对象返回并附带分页信息
     * @param $data
     * @param $statusCode
     * @param int $errcode
     * @param string $errmsg
     */
    public function pageEncode($data, $statusCode = 200, $errcode = 0, $errmsg = ''){

        // 判断是否可分页（Collection对象）
        if(!isset($data->collectionPages) || empty($data->collectionPages)){
            trigger_error("data isn't the instance of Paginate", E_USER_ERROR);
        }

        // 将 Collection 自身的 collectionPages 合并到对象并返回
        $result = [
            'errcode'   =>  $errcode,
            'errmsg'    =>  $errmsg,
            'data'      =>  json_decode($data),
            'page'      =>  $data->collectionPages
        ];

        $this->json($result,$statusCode);
    }


    /**
     * 响应头部设置
     * @param string $key
     * @param string $value
     * @return string
     */
    public function setHeader($key, $value){
        return $this->header[$key] = $value;
    }

    /**
     * 响应头部获取
     * @param $key|null
     * @return string
     */
    public function getHeader($key = null){
        if(is_null($key)) {
            return $this->header;
        } else {
            return (array_key_exists($key,$this->header)) ? $this->header[$key] : null;
        }
    }

    /**
     * 获取 HTTP 响应信息
     * @param $statusCode
     * @return mixed
     */
    private function getStatusMessage($statusCode){
        $httpStatus = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ];
        return (array_key_exists($statusCode,$httpStatus)) ? $httpStatus[$statusCode] : $httpStatus["500"];
    }

    /**
     * 执行响应头部
     * @param $statusCode
     */
    private function executeHeader($statusCode){
        // 响应 HTTP 状态
        header($this->httpVersion." ".$statusCode." ".$this->getStatusMessage($statusCode));
        // 声明 JSON 类型
        header("Content-Type: application/json");
        // 设置额外的 header
        if(!empty($this->header)) {
            foreach ($this->header as $key => $value) {
                header($key.": ".$value);
            }
        }
    }
}