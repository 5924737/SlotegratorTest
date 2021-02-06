<?php

use yii\db\Migration;

/**
 * Class m210205_175319_user_config
 */
class m210205_175319_user_config extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_config', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer(),
            'config' => $this->text(),
            'ctime' => $this->integer(),
            'utime' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_config');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210205_175319_user_config cannot be reverted.\n";

        return false;
    }
    */
}
