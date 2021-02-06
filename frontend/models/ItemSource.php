<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.2021
 * Time: 22:46
 */

namespace frontend\models;


class ItemSource implements IService
{
    protected $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function getPresent()
    {
        $res = PresentItems::find()->orderBy("rand()")->one();
        if( --$res->count >= 0){
            $res->count;
            $res->save();
        }else{
            return false;
        }

        $userInfo = UserConfig::findOne(['uid' => $this->uid]);
        $config = json_decode($userInfo->config, true);
        array_push($config['item'], $res->name);
        $userInfo->config = json_encode($config);
        $userInfo->save();

        return [
            'message'=>'Подарок "'.$res->name.'"',
            'actions'=>[
                'Отказаться >>>'=>'/site/play?action=refuse',
            ]
        ];
    }

    public static function refundLast($uid)
    {
//        var_dump('refundLast');die;
        $userInfo = UserConfig::findOne(['uid' => $uid]);
        $config = json_decode($userInfo->config, true);
        $item = array_pop($config['item']);
        $userInfo->config = json_encode($config);

        $userInfo->save();
        $res = PresentItems::find(['name' => $item])->one();
        $res->count++;
        return $res->save();
    }

}