<?php
namespace Model;
use System\Model;

/**
 * 测试模型
 */
class Demo extends Model
{
    // 数据表名称
	public static $table = 'demo';

	// 访问器
    public function getPhpinfo(){
        echo phpinfo();
    }

    // 修饰器
    public function setAbc($value){
        return "Baby ".$value;
    }
}