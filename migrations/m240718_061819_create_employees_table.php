<?php

use yii\db\Migration;

/**
 * Class m240718_061819_create_employees_table
 */
class m240718_061819_create_employees_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('employees', [
            'id' => $this->primaryKey(),
            'id_department' => $this->integer()->notNull(),            
            'full_name' => $this->string(250)->notNull(),   
            'post' => $this->string(250)->null(),         
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'update_hash' => $this->string(32)->notNull(),
        ]);
        $this->createIndex('unique__employees', 'employees', ['id_department', 'full_name'], true);
        $this->addForeignKey('fk__employees__id_department', 'employees', 'id_department', 'departments', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('employees');
    }

}
