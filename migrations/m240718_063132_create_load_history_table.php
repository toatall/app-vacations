<?php

use yii\db\Migration;

/**
 * Class m240718_063132_create_load_history_table
 */
class m240718_063132_create_load_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('load_history', [
            'id' => $this->primaryKey(),
            'source' => $this->string('50')->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('load_history');
    }
    
}
