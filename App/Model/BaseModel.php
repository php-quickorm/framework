<?php
namespace Model;

/**
 * PHP-ORM 框架的 Model 基本类
 * Author:  Rytia
 * Date:  	2018.08.29
 */

class BaseModel
{
	protected static $table = '';

	public static function where($array){
		// SQL 语句拼接
		foreach ($array as $key => $value) {
		    if (isset($sql)) {
                $sql .= " AND ".$key.'="'.$value.'""';
            } else {
		        $sql = $key.'="'.$value."''";
            }
		}
		echo $sql;
	}

    public static function find($id){
	    $table = get_class(IN_DELETE_SELF)::$table;
        echo 'select id="'.$id.'" from'.Self::$table;
    }
}