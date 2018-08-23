<?php
namespace System\Http;
Class Request
{
    protected $method;
    protected $postArray;
    protected $getArray;


    /**
     * Request constructor.
     */
    public function __construct(){
        // 获取请求的方式
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->postArray = $_POST;
        $this->getArray = $_GET;
    }


    /**
     * 返回全部请求数据
     * @return array
     */
    public function all(){
        return array_merge($this->postArray, $this->getArray);
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
        } else if(array_key_exists($field,$this->postArray)) {
            // POST 取值
            return $this->postArray[$field];
        } elseif(array_key_exists($field,$this->getArray)) {
            // GET 取值
            return $this->getArray[$field];
        } else {
            // 键值不存在
            return null;
        }
    }

    /**
     * 获取请求的路径
     * @return string
     */
    public function path(){
        return dirname($_SERVER["REQUEST_URI"]);
    }

    /**
     * 获取请求的具体链接
     * @return string
     */
    public function url(){
        return $_SERVER["REQUEST_URI"];
    }

    /**
     * 获取请求的方法
     * @return string
     */
    public function method(){
        return $this->method;
    }

}