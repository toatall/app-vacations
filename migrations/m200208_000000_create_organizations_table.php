<?php

use yii\db\Migration;

/**
 * Class m200208_000000_create_organizations_table
 */
class m200208_000000_create_organizations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('organizations', [
            'code' => 'VARCHAR(5) PRIMARY KEY',           
            'name' => $this->string(250)->notNull(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
            'deleted_at' => $this->dateTime()->null()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('organizations');
    }
}
