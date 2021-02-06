<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.2021
 * Time: 22:46
 */

namespace frontend\models;


class MoneySource implements IService
{
    protected $class;
    protected $int;
    protected $uid;
    protected $courseConvertation;

    public function __construct($int, $uid, $courseConvertation)
    {
        $this->int = $int;
        $this->uid = $uid;
        $this->courseConvertation = $courseConvertation;
    }

    public function getPresent()
    {
        $res = PresentCash::find()->one();
        $co = $res->count - $this->int;
        if($co >= 0){
            $res->count = $co;
            $res->save();
        }else{
            return false;
        }

        $userInfo = UserConfig::findOne(['uid' => $this->uid]);
        $config = json_decode($userInfo->config, true);
        $config['cash'] = $config['cash'] + $this->int;
        $userInfo->config = json_encode($config);
        $userInfo->save();

        return [
            'message'=>'Подарок деньги '.$this->int.' грн',
            'actions'=>[
                    'Перевести в бонусы ('.$this->int*$this->courseConvertation.' бонусов)>>>'=>'/site/play?action=convert&int='.$this->int,
//                    'Отправить на карту >>>'=>'/site/play?action=send&int='.base64_encode($this->int)
            ]
        ];
    }

    public static function convertToBonus($int, $uid, $courseConvertation)
    {
        $userInfo = UserConfig::findOne(['uid' => $uid]);
        $config = json_decode($userInfo->config, true);
        $config['cash'] = $config['cash'] - $int;
        if($config['cash'] >= 0){
            $config['bonus'] = $config['bonus'] + $int*$courseConvertation;
            $userInfo->config = json_encode($config);
            return $userInfo->save();
        }
    }

}