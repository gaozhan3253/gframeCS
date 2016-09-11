<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 14:22
 */
namespace Gframe;

class Conf {
    //收集合并项目中所有配置
    private static $configArr;

    public static function config($name,$value =''){

        //框架配置文件
        $gfconfigArr = array();
        $gf_config_file = GF_CONF_PATH.'config.php';
        if(is_file($gf_config_file)){
            $gfconfigArr = include $gf_config_file;
        }
        //应用配置
        $appconfigArr =array();
        $app_config_file = CONF_PATH.'config.php';
        if(is_file($app_config_file)){
            $appconfigArr = include $app_config_file;
        }


        if(is_array($appconfigArr)){
            //合并配置 用户配置覆盖框架自有配置
            self::$configArr = array_merge($gfconfigArr,$appconfigArr);
        }elseif(is_array($gfconfigArr)){
            self::$configArr =$gfconfigArr;
        }

        //获取所有参数
        $arr = func_get_args();
        //如果传入参数
        if(!empty($arr)){
            //如果第一个参数是字符
            if(is_string($arr[0])){
                //如果第二个参数有值 就是设置配置
                if(isset($arr[1])&& !empty($arr[1])){
                    self::$configArr[$arr[0]] =$arr[1];
                }else{
                    //第二个参数无值,就是查询
                    if(!empty(self::$configArr[$arr[0]])){
                        $conf = self::$configArr[$arr[0]];
                        //因转成首字母大写 有些地方不需要 所以就把他去掉 要用的地方去转换一次
//                       if(is_string($conf)){
//                            $conf = ucfirst(strtolower($conf));
//                       }
//                        if(is_array($conf)){
//                            foreach($conf as $k=>$v){
//                                if(is_string($v)){
//                                    $conf[$k] = ucfirst(strtolower($v));
//                                }
//                            }
//                        }
                        return $conf;
                    }else{
                        return false;
                    }
                }
            //如果第一个参数是数组 批量设置配置
            }elseif(is_array($arr[0])){
                //循环数组
                foreach($arr[0] as $k=>$v){
                    //如果键名是字符 就配置数组
                    if(is_string($k)){
                        self::$configArr[$k] = $v;
                    }
                }
            }
        }
    }
}