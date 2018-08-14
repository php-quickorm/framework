<?php
require __DIR__.'/../vendor/autoload.php';

/**
 * PHP-QuickORM
 *
 * @description: a simple PHP ORM framework to built up api server
 * @author: Rytia
 */

$query = explode("/",$_SERVER['QUERY_STRING']);
call_user_func(['Controller\\'.array_shift($query),array_shift($query)],...$query);
