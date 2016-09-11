<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 13:59
 */
namespace Gframe;

class Gframe{
    static $module;
    static $controller;
    static $action;

    /**
     * 框架启动
     */
    public static function run(){
        //引入路由
        $route = new \Gframe\Route();
        //获取当前模块 控制器 方法
        self::$module = ucfirst(strtolower($route->module));
        self::$controller = ucfirst(strtolower($route->controller));
        self::$action = strtolower($route->action);
        //获取允许访问的控制器
        $contArr = getC('MODULE_ALLOW_LIST')?getC('MODULE_ALLOW_LIST'):'';
        //允许访问列表不为空时
        if(!empty($contArr)){
            foreach($contArr as $k=>$v){
                $contArr[$k] =$v;
            }
            if(!in_array(self::$module,$contArr)){
                    return ; //非允许列表访问
            }
        }

        //默认控制器文件夹
        $defalut_controller_path = getC('CONTROLLER_PATH')?getC('CONTROLLER_PATH'):'Index';
        //默认错误控制器
        $error_controller = getC('DEFAULT_CONTROLLER_ERROR')?getC('DEFAULT_CONTROLLER_ERROR'):'EmptyController';
        //默认错误方法
        $error_action = getC('DEFAULT_ACTION_ERROR')?getC('DEFAULT_ACTION_ERROR'):'_empty';

        //拼接路径 暂写死控制器文件夹
        $contrfile = APP_PATH.self::$module.'/'.$defalut_controller_path.'/'.self::$controller.'Controller'.EXT;
        //如果存在控制器类文件
        if(is_file($contrfile)){
            //拼接类名
            $contrname = '\\'.self::$module.'\\'.$defalut_controller_path.'\\'.self::$controller.'Controller';
            //new 对象
            $contr = new $contrname();
            //如果方法不存在 就找_empty方法
            if(!method_exists($contr,self::$action)){
                self::$action = $error_action;
            }
            //执行方法
            $action = self::$action;
            $contr->$action();
        }else{
            $contrfile = APP_PATH.self::$module.'/'.$defalut_controller_path.'/'.$error_controller.EXT;
            if(is_file($contrfile)){
                //拼接默认错误方法
                $contrname = '\\'.self::$module.'\\Controller\\'.$error_controller;
                self::$action = $error_controller;
                $contr = new $contrname();
                self::$action = $error_action;
                //执行方法
                $contr->$error_action();
            }
        }
    }

    /**
     * 自动加载
     */
    public static function load($class){
        //截取传入的命名空间名
        /*自动加载与命名空间一点关系都没
        这里之所以用按照命名空间来自动加载 是因为规定了
        命名空间的名和文件的路径对应
        这样我知道名称，就可以找到文件夹和文件夹下的类文件了
        */
        //判断传入名称是否带\,是否是命名空间的类
        if(false !==strpos($class,'\\',true)){
            //是命名空间的类名 截取空间名和文件名
            $name = strstr($class,'\\',true);

            //判断是否为核心中的文件夹
            if(is_dir(LIB_PATH.$name)){
                $classpath =LIB_PATH.str_replace('\\','/',$class).EXT;
                //如果是文件
                if(is_file($classpath)){
                    //引入核心类文件
                    include $classpath;
                }
            }else{
             //不是核心中的文件夹 或许是应用文件夹
                $filename = APP_PATH.str_replace('\\','/',$class).EXT;
                //引入应用文件夹下的类文件
                include $filename;

            }
        }
    }

}