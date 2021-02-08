<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.02.2021
 * Time: 1:01
 */

namespace frontend\models;


class UserInfo
{

    public static function run($config = [])
    {
        $userInfo = UserConfig::findOne(['uid' => $config['uid']]);
        $items = json_decode($userInfo->config, true)['item'];
        $it = [];
        $string = '';
        foreach ($items as $key => $value){
            if(!isset($it[$value])){
                $it[$value] = 1;
            }else{
                $it[$value]++;
            }
        }
        foreach ($it as $key => $value){
            $string .= $key.'('.$value.'), ';
        }
        return [
            'bonus'=>json_decode($userInfo->config, true)['bonus'],
            'cash'=>json_decode($userInfo->config, true)['cash'],
            'items'=>$string?$string:'нет'
        ];
    }
}