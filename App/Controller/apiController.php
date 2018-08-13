<?php
namespace Controller;
use Model\Demo;
use System\Collection;

class apiController {
    /**
     * @param $id
     */
    public function test($id){
        // echo $_SERVER['REQUEST_METHOD'];
        $test = Demo::where(['title' => '还是标题'])->orWhereRaw('1=1');
        print_r($test);
    }

    // 查询类方法演示

    public function find($id){
        // 通过 id 查询数据表
        $resultObject = Demo::find($id);
        print_r($resultObject);
    }

    public function where(){
        // 条件检索数据表(条件数组)
        $conditionArray = ["id" => "6"];
        $resultObjectArray = Demo::where($conditionArray);
        print_r($resultObjectArray);
    }

    public function whereRaw(){
        // 条件检索数据表(SQL语句)
        $conditionStatement = 'author LIKE "Rytia"';
        $resultObjectArray = Demo::whereRaw($conditionStatement);
        print_r($resultObjectArray);
    }

    public function all(){
        // 显示全部数据
        $resultObjectArray = Demo::all();
        print_r($resultObjectArray);
    }

    public function raw(){
        // 执行SQL语句
        $sqlStatement = 'SELECT * FROM {table} WHERE author LIKE "Rytia"';
        $resultObjectArray = Demo::raw($sqlStatement);
        print_r($resultObjectArray);
    }

    public function search(){
        // 数据表字段搜索
        $resultObjectArray = Demo::search("title", "%测试%");
        print_r($resultObjectArray);
    }

    public function orWhere($id){
        // 多重条件检索数据表(条件数组)
        // 支持 where、whereRaw、orWhere、orWhereRaw
        $test = Demo::where(['title' => '还是标题'])->orWhere(['title' => '测试标题']);
        print_r($test);
    }

}
