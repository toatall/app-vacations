<?php

use yii\db\Migration;

/**
 * Class m240718_061744_create_vacations_kind_table
 */
class m240718_061744_create_vacations_kind_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('vacations_kind', [
            'id' => $this->primaryKey(),
            'name' => $this->string(150)->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vacations_kind');
    }
    
}
