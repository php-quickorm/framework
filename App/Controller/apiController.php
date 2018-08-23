<?php
namespace Controller;
use System\Collection;
use System\Controller;
use System\Database;
use Model\Demo;

class apiController extends Controller {

    public function testGet($id){
        header("X-Powered-By: PHP-QuickORM");
        return $this->response()->json(Demo::find($id));
    }

    // ORM 查询类方法演示

    public function find($id){
        // 通过 id 查询数据表
        $resultObject = Demo::find($id);
        dd($resultObject);
    }

    public function whereGet(){
        // 条件检索数据表(条件数组)
        $conditionArray = ["author" => "Rytia"];
        $resultObjectArray = Demo::where($conditionArray)->get();
        dd($resultObjectArray);
    }

    public function whereRawGet(){
        // 条件检索数据表(SQL语句)
        $conditionStatement = 'author LIKE "Rytia"';
        $resultObjectArray = Demo::whereRaw($conditionStatement)->get();
        dd($resultObjectArray);
    }

    public function allGet(){
        // 显示全部数据
        $resultObjectArray = Demo::all();
        dd($resultObjectArray);
    }

    public function rawGet(){
        // 执行SQL语句
        $sqlStatement = 'SELECT * FROM {table} WHERE author LIKE "Rytia"';
        $resultObjectArray = Demo::raw($sqlStatement)->get();
        dd($resultObjectArray);
    }

    public function searchGet(){
        // 数据表字段搜索
        $resultObjectArray = Demo::search("title", "%嗯%");
        dd($resultObjectArray);
    }

    public function orWhereGet(){
        // 多重条件检索数据表(条件数组)
        // 支持 where、whereRaw、orWhere、orWhereRaw
        $test = Demo::where(['title' => '还是标题'])->orWhere(['title' => '测试标题'])->get();
        dd($test);
    }

    public function deleteGet($id){
        // 删除条目
        $result = Demo::find($id)->delete();
        dd($result);
    }

    public function paginateGet($pageNum){
        // 条目分页演示

        // 直接调用：相当于 Database 层分页，效率高
        $test = Demo::paginate(3);

        // Collection 层分页：先把全部数据取出再通过 Collection 分页，效率低
        $test = Demo::all()->paginate(3);

        // Database 层分页：在 SQL 语句里添加 LIMIT，效率高
        $test = Demo::where()->paginate(3);
        $test = Database::table('demo')->where()->setModel(Demo::class)->paginate(3);
        $test = Database::model(Demo::class)->where()->paginate(3);

        dd($test);
    }

    public function databaseWhereGet(){
        // 数据库层封装演示1
        $result = Database::table('demo')
            ->setModel(Demo::class)
            ->select('title')
            ->where(["author" => "Rytia"])
            ->orWhereRaw('content LIKE "%测试%"')
            ->paginate(5);

        dd($result);

    }

    public function databaseOnGet(){
        // 数据库层封装演示2
        $result = Database::table('wiki')
            ->select('DISTINCT zhong.name,wiki.coordinate')
            ->join('zhong')
            ->on('wiki.zhong=zhong.id')
            ->orderBy("coordinate", "DESC")
            ->fetchAll();
        dd($result);

    }
}
