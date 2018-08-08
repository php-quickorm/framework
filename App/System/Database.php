<?php
namespace System;
use System\DatabaseDriver\pdo_mysql;
class Database extends pdo_mysql{
    public function connect() {
        return parent::connectDatabase('127.0.0.1', 'orm', 'root', '');
    }
}
