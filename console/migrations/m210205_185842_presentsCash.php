<?php

use yii\db\Migration;

/**
 * Class m210205_185842_presentsCash
 */
class m210205_185842_presentsCash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('present_cash', [
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
        $this->dropTable('present_cash');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210205_185842_presentsCash cannot be reverted.\n";

        return false;
    }
    */
}
