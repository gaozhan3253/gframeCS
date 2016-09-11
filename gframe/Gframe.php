<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 13:44
 */

/**
 * 框架引导
 */


//定义常量
    //定义框架目录
    defined('GFRAME_PATH') or define('GFRAME_PATH',__DIR__.'/');
    //定义框架核心文件目录
    defined('LIB_PATH') or define('LIB_PATH',GFRAME_PATH.'Library/');
    //定义框架系统文件目录
    defined('CORE_PATH') or define('CORE_PATH',LIB_PATH.'Gframe/');
    //定义框架函数目录
    defined('GF_FUNC_PATH') or define('GF_FUNC_PATH',GFRAME_PATH.'Common/');
    //定义框架配置目录
    defined('GF_CONF_PATH') or define('GF_CONF_PATH',GFRAME_PATH.'Conf/');

    //定义应用配置目录
    defined('CONF_PATH') or define('CONF_PATH',APP_PATH.'Conf/');
    //定义应用函数目录
    defined('FUNC_PATH') or define('FUNC_PATH',APP_PATH.'Common/');

    //定义文件后缀
    defined('EXT') or define('EXT','.class.php');
    //引入系统函数库
    include_once (GF_FUNC_PATH.'Function.php');
    //引入用户函数库
    if(is_file(FUNC_PATH.'function.php')){
        include (FUNC_PATH.'function.php');
    }
    //引入框架核心
    include_once (CORE_PATH.'Gframe.class.php');

    //自动加载
    spl_autoload_register('\Gframe\Gframe::load');
    //启动框架
    \Gframe\Gframe::run();