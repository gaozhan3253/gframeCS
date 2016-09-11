<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30 0030
 * Time: 下午 14:02
 */

/**
 * 友好显示数据
 * @param $data
 */
function dump($data){
    echo '<pre>';
        var_dump($data);
    echo '</pre>';

}

function getC($name,$value=''){
    if(!empty($name)){
        if(is_string($name)){
            if(!empty($value)){
                \Gframe\Conf::config($name,$value);
            }else{
                return \Gframe\Conf::config($name);
            }
        }elseif(is_array($name)){
            \Gframe\Conf::config($name);
        }
    }
}