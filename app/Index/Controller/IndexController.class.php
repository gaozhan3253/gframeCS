<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/27 0027
 * Time: 下午 13:27
 */
namespace Index\Controller;
use Gframe\Controller;

class IndexController extends Controller{

    public function index(){

        try{
            $db = new \Gframe\Db();
            $arr = $db->query('select * from gz_mrdh');
            dump($arr);
        }catch (\Exception $e){
            $e->getMessage();
        }
        $this->assign('title','Index');
        $this->display();
}
    public function _empty(){
        echo '空方法<br/>';
    }
}