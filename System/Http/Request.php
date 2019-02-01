<?php
namespace System\Http;
/**
 * PHP-QuickORM 框架 HTTP 请求类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */
Class Request
{
    protected $method;
    protected $url;
    protected $path;
    protected $requestArray;


    /**
     * Request constructor.
     */
    public function __construct(){
        // 获取请求的信息
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url = $_SERVER["REQUEST_URI"];
        $this->requestArray = $_REQUEST;
        if(!in_array($this->method,['post','get'])){
            parse_str(file_get_contents('php://input'), $_OTHER);
            $this->requestArray = array_merge($this->requestArray,$_OTHER);
        }

    }


    /**
     * 返回全部请求数据
     * @return array
     */
    public function all(){
        return $this->requestArray;
    }

    /**
     * 通过字段(或字段数组)获取相应请求数据
     * @param string|array $field
     * @return array|null
     */
    public function get($field){
        if(is_array($field)){
            // 如果传入的是数组，则递归取值
            // 写框架快一个月了，时至今日 Model、Collection、Database 已经完工，第一次想起将递归取值用上，可喜可贺
            // 此方法的缺陷是：如果传入一个二维数组（比如某个字段本身也是个数组），则该会返回 null，只能单独使用 get() 获取
            foreach ($field as $value) {
                $resultArray[] = $this->get($value);
                return $resultArray;
            }
        } else if(array_key_exists($field,$this->requestArray)) {
            return $this->requestArray[$field];
        } else {
            // 键值不存在
            return null;
        }
    }

    /**
     * 获取请求的路径
     * @return string
     */
    public function getPath(){
        return $this->path;
    }

    /**
     * 获取请求的具体链接
     * @return string
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * 获取请求的方法
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

}