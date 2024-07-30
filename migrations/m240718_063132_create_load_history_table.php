<?php

use yii\db\Migration;

/**
 * Class m240718_063132_create_load_history_table
 */
class m240718_063132_create_load_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('load_history', [
            'id' => $this->primaryKey(),
            'org_code' => $this->string(5)->notNull(),
            'year' => $this->string(4),
            'source' => $this->string('50')->notNull(),
            'source_description' => $this->string(500),            
            'error' => $this->text(),            
            'error_trace' => $this->text(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('load_history');
    }
    
}
