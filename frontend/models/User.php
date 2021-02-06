<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.2021
 * Time: 19:46
 */

namespace frontend\models;


class User extends \common\models\User
{
    public function getConfig()
    {
        return $this->hasOne(UserConfig::className(), ['uid' => 'id']);
    }
}