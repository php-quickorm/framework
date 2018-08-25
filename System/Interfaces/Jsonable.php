<?php
namespace System\Interfaces;
interface Jsonable
{
    /**
     * 换为字符串(JSON)
     * @param  int  $option
     * @return string
     * @uses 用于标记 __toString() 方法将实例格式化为 JSON 的类
     */
    public function toJson($option = 0);

    public function __toString();
}