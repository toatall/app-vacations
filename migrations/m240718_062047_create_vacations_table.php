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
            'created_at' => $this->dateTime()->null(),
        ]);
        $this->addForeignKey('fk__vacations__id_kind', 'vacations', 'id_kind', 'vacations_kind', 'id', 'cascade');
        $this->addForeignKey('fk__vacations__id_employee', 'vacations', 'id_employee', 'employees', 'id', 'cascade');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vacations');        
    }
    
}
