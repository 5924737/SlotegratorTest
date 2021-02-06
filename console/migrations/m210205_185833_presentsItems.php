<?php

use yii\db\Migration;

/**
 * Class m210205_185833_presentsItems
 */
class m210205_185833_presentsItems extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('present_items', [
            'id' => $this->primaryKey(),
            'name' => $this->text(),
            'count' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('present_items');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210205_185833_presentsItems cannot be reverted.\n";

        return false;
    }
    */
}
