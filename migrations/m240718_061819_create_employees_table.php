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
            'org_code' => $this->string(5)->notNull(),
            'full_name' => $this->string(250)->notNull(),            
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->createIndex('unique__employees', 'employees', ['id_department', 'org_code', 'full_name'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('employees');
    }

}
