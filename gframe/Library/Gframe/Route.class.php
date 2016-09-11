<?php
/**
 * 路由
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 13:59
 */
namespace Gframe;

class Route{
    public $module;
    public $controller;
    public $action;

    public function __construct(){
        //读取配置中默认模块，控制器，方法
        $default_module = ucfirst(strtolower(getC('DEFAULT_MODULE')?getC('DEFAULT_MODULE'):'index'));
        $default_controller = ucfirst(strtolower(getC('DEFAULT_CONTROLLER')?getC('DEFAULT_CONTROLLER'):'index'));
        $default_action = getC('DEFAULT_ACTION')?getC('DEFAULT_ACTION'):'index';

        //判断当前URL
        if(isset($_SERVER['REQUEST_URI']) && '/' != $_SERVER['REQUEST_URI']){
            //当前URL 不带网址的 项目名\index.php\id\1
            $path = $_SERVER['REQUEST_URI'];
            //判断项目名
            $baseUrl = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
            //去除项目名
            $path = str_replace($baseUrl,'',$path);

            //判断去掉项目名后 值还剩什么   && empty($path) 条件这里待定
            if('/'==$path){
                //值为空 传默认模块控制器方法
                $this->module = $default_module;
                $this->controller=$default_controller;
                $this->action=$default_action;
            }else{
                //不为空 转成数组
                $patharr = explode('/',$path);
                //转换后第一个元素有可能为空值 过滤掉
                if(empty($patharr[0])){
                    array_shift($patharr);
                }
                //判断数组第一位是否为入口文件
                if(false !==strpos($patharr[0],'.',true)) {
                    //去掉数组第一位
                    array_shift($patharr);
                }
                //判断数组是否为空 为空就给默认值
                if(!count($patharr)){
                    $this->module = $default_module;
                    $this->controller=$default_controller;
                    $this->action=$default_action;
                }else{
                    switch (count($patharr)){
                        //数组只有1个值时 只传入模块名
                        case 1:
                            $this->module = strtolower($patharr[0]);
                            $this->controller=$default_controller;
                            $this->action=$default_action;
                            break;
                        //数组2个值时 传入模块名和对象名
                        case 2:
                            $this->module = strtolower($patharr[0]);
                            $this->controller=strtolower($patharr[1]);
                            $this->action=$default_action;
                            break;
                        //数组3个值时 传入模块名和对象名和方法名
                        case 3:
                            $this->module = strtolower($patharr[0]);
                            $this->controller=strtolower($patharr[1]);
                            $this->action=strtolower($patharr[2]);
                            break;
                        //传入更多时
                        default:
                            //定义循环变量
                            $i = 3;
                            //循环设置变量
                            while($i<count($patharr)){
                                //参数值都是成对传入的 所以有单独传入名却无值情况下
                                if(isset($patharr[$i+1])){
                                    $_GET[$patharr[$i]] = $patharr[$i+1];
                                }
                                $i = $i+2;
                            }
                            break;
                    }
                }
            }
        }
    }
}