<?php

use yii\db\Migration;

/**
 * Class m240718_062047_create_vacations_table
 */
class m240718_062047_create_vacations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('vacations', [
            'id' => $this->primaryKey(),
            'id_kind' => $this->integer()->notNull(),
            'id_employee' => $this->integer()->notNull(),
            'date_from' => $this->date()->notNull(),
            'date_to' => $this->date()->notNull(),
            'status' => $this->string(5)->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'update_hash' => $this->string(32)->notNull(),
        ]);
        $this->addForeignKey('fk__vacations__id_kind', 'vacations', 'id_kind', 'vacations_kind', 'id', 'CASCADE');
        $this->addForeignKey('fk__vacations__id_employee', 'vacations', 'id_employee', 'employees', 'id', 'CASCADE');
        $this->createIndex('index__vacations__uniques', 'vacations', ['id_kind', 'id_employee', 'date_from', 'date_to'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vacations');        
    }
    
}
