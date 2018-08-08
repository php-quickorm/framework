<?php
namespace Controller;
use Model\Demo;

class apiController {
    public function test($id){
      Demo::find($id);
//      De mo::where(['id'=>'5', 'level'=>'1']);
//      Demo::raw("select * form {table}");
//      Demo::whereRaw('username LIKE "xiu" AND level="2"');
//      Demo::search('username','demo');

      //echo $_SERVER['REQUEST_METHOD'];
    }
}
