<?php
namespace System\DatabaseDriver;

class pdo_sqlite{

    public $connect;

    public function initialConnect($file) {
        // 创建 pdo 链接
        $link = 'sqlite:'.$file;
        try {
            $db = new \PDO($link);
            $db->exec("SET NAMES utf8");
        }
        catch (\PDOException $e) {
            echo 'Connection Failed: ' . $e->getMessage();
        }
        finally{
            return $db;
        }
    }

    public function __construct(){
        $this->connect = self::initialConnect('orm.sqlite3.db');
    }

}
