<?php
namespace System;
use System\Database;
/**
 * PHP-JSORM 框架的 Model 基本类
 * Author:  Rytia
 * Date:  	2018.07.29
 */

class Model
{
	protected static $table = '';
	protected static $db;

	public static function where($conditionArray){
		// SQL 语句拼接
		foreach ($conditionArray as $key => $value) {
		    if (isset($sql)) {
                $sql .= " AND ".$key.'="'.$value.'"';
            } else {
		        $sql = $key.'="'.$value.'"';
            }
		}
		$sql = 'SELECT '.$sql.' FROM '.static::$table;;
	}
    public static function find($id){
        $db = (new Database())->connect();

        $sql = "SELECT * FROM :table WHERE id=:id";
        $target = $db->query($sql);
     
//        $target->execute();
        print_r($target->fetch(2));
    }
    public static function whereRaw($SQLCondition){
        echo $sql = 'SELECT '.$SQLCondition.' FROM '.static::$table;
    }
    public static function raw($SQL){
        $sql = str_replace("{table}",static::$table,$SQL);
    }
    public static function search($field,$string){
        echo $sql = 'SELECT '.$field.' LIKE "'.$string.'" FROM '.static::$table;
    }

    public function __construct() {
    }
}