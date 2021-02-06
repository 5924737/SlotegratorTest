<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.02.2021
 * Time: 1:01
 */

namespace frontend\models;


class BonusInfo
{

    public static function run()
    {
        return [
            'bonus'=>"UNLIM!!!",
            'cash'=>PresentCash::find()->select('count')->scalar(),
            'items'=>PresentItems::find()->select('sum(count)')->scalar()
        ];
    }
}