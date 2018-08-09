<?php
namespace System\DatabaseDriver;

class pdo_mysql {

    public $connect;

    public function initialConnect($host, $database, $user, $password) {
        // 创建 pdo 链接
        $link = 'mysql:dbname='.$database.';host='.$host;
        try {
            $db = new \PDO($link, $user, $password);
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
        $this->connect = self::initialConnect('127.0.0.1', 'orm', 'root', '');
    }

}
