<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.2021
 * Time: 22:46
 */

namespace frontend\models;


class BonusSource implements IService
{
    protected $int;
    protected $uid;

    public function __construct($int, $uid)
    {
        $this->int = $int;
        $this->uid = $uid;
    }

    public function getPresent()
    {
        $userInfo = UserConfig::findOne(['uid' => $this->uid]);
        $config = json_decode($userInfo->config, true);
        $config['bonus'] = $config['bonus'] + $this->int;
        $userInfo->config = json_encode($config);
        $userInfo->save();
//        var_dump($items = json_decode($userInfo->config, true)['bonus']);die;
        return[
            'message'=>'Ваш баланс пополнен бонусы '.$this->int.' ед.',
            'actions'=>[]
        ];
    }

}