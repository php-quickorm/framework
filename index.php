<?php
require __DIR__.'/vendor/autoload.php';

$query = explode("/",$_SERVER['QUERY_STRING']);
call_user_func(['Controller\\'.array_shift($query),array_shift($query)],$query);