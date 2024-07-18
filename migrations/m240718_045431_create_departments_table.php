<?php

use yii\db\Migration;

/**
 * Class m240718_045431_create_departments_table
 */
class m240718_045431_create_departments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('departments', [
            'id' => $this->primaryKey(),
            'org_code' => $this->string(5)->notNull(),
            'name' => $this->string(200)->notNull(),
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk__departments__org_code', 'departments', 'org_code', 'organizations', 'code', 'cascade', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('departments');
    }

}
