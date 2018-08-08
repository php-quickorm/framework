<?php
namespace System\DatabaseDriver;

class pdo_mysql {

    public function connectDatabase($host, $database, $user, $password) {

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

}
