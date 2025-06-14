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
            'org_code' => $this->string(5)->notNull(),
            'name' => $this->string(500)->notNull(),            
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),            
        ]);
        $this->addForeignKey('fk__vacations_kind__org_code', 'vacations_kind', 'org_code', 'organizations', 'code', 'CASCADE', 'CASCADE');       
        $this->createIndex('index__vacations_kind__org_code__name', 'vacations_kind', ['org_code', 'name'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vacations_kind');
    }
    
}
