<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_config".
 *
 * @property int $id
 * @property int|null $uid
 * @property string|null $config
 * @property int|null $ctime
 * @property int|null $utime
 */
class UserConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'ctime', 'utime'], 'integer'],
            [['config'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'config' => 'Config',
            'ctime' => 'Ctime',
            'utime' => 'Utime',
        ];
    }
}

