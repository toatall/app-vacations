<?php

use yii\db\Migration;

/**
 * Class m200208_000000_create_users_table
 */
class m200208_000000_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull()->unique(),
            'password' => $this->string()->null(),            
            'full_name' => $this->string(250),
            'position' => $this->string(250),
            'email' => $this->string(50)->unique(),
            'org_code' => $this->string(5),
            'org_code_select' => $this->string(5),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
            'deleted_at' => $this->dateTime()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }

}
