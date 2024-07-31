<?php

use yii\db\Migration;

/**
 * Class m240730_094622_create_table_vacations_merged
 */
class m240730_094622_create_table_vacations_merged extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('vacations_merged', [
            'id' => $this->primaryKey(),            
            'id_employee' => $this->integer()->notNull(),
            'year' => $this->string(4)->notNull(),
            'date_from' => $this->date()->notNull(),
            'date_to' => $this->date()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'update_hash' => $this->string(32)->notNull(),
        ]);
        $this->addForeignKey('fk__vacations_merged__id_employee', 'vacations_merged', 'id_employee', 'employees', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('vacations_merged');
    }

}
