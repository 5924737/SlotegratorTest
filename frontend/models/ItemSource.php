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
    protected $class;
    protected $uid;

    public function __construct($class, $uid)
    {
        $this->class = $class;
        $this->uid = $uid;
    }

    public function getPresent()
    {
        $res = $this->class::find()->orderBy("rand()")->one();
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

    public function refundLast()
    {
//        var_dump('refundLast');die;
        $userInfo = UserConfig::findOne(['uid' => $this->uid]);
        $config = json_decode($userInfo->config, true);
        $item = array_pop($config['item']);
        $userInfo->config = json_encode($config);

        $userInfo->save();
        $res = $this->class::find(['name' => $item])->one();
        $res->count++;
        return $res->save();
    }

}