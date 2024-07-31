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
            'year' => $this->string(4)->notNull(),
            'name' => $this->string(200)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'update_hash' => $this->string(32)->notNull(),
        ]);
        $this->addForeignKey('fk__departments__org_code', 'departments', 'org_code', 'organizations', 'code', 'CASCADE', 'CASCADE');
        $this->createIndex('index__departments__unique__org_code__name', 'departments', ['org_code', 'name', 'year'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {        
        $this->dropTable('departments');
    }

}
