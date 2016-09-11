<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 16:44
 */

namespace Gframe;

class Controller{
    //模板值存放用
    public $assign;

    /**
     * 给模板赋值
     * @param $name
     * @param $value
     */
    public function assign($name,$value){
        //给模板赋值
        $this->assign[$name] = $value;
    }

    /**
     * 展示模板
     * @param string $file
     */
    public function display($file = ''){
        $module = \Gframe\Gframe::$module;
        //判断是否为空
        if(!empty($file)){
            //不为空时 看是否存在\符号
            if(false !==strpos($file,'\\',true)) {
                //存在符号 按符号分解成数组 分成控制器 方法名
                $fileArr = explode('\\',$file);
                $controller = $fileArr[0];
                $action = $fileArr[1];
            }else{
                //不存在 就是只传入了模板名 使用当前控制器的做文件夹
                $controller = \Gframe\Gframe::$controller;
                $action = $file;
            }
        }else{
            //没传入时 就直接使用当前页面对应的模板路径
            $controller = \Gframe\Gframe::$controller;
            $action = \Gframe\Gframe::$action;
        }
        //模板路径
        $filename = APP_PATH.$module.'/View/'.$controller.'/'.$action.'.html';
        //判断模板文件是否存在
        if(is_file($filename)){
            //分解存放模板变量的数组
            extract($this->assign);
            //引入模板文件
            include($filename);
        }

    }

    /**
     * 默认的错误方法
     */
    public function _empty(){
        echo '这里是默认的错误方法';
        $this->display();
    }
}