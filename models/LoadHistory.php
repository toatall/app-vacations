<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "load_history".
 *
 * @property int $id
 * @property string $org_code
 * @property string $year
 * @property string $source
 * @property string $source_description
 * @property string $error
 * @property string $error_trace
 * @property string|null $created_at
 */
class LoadHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'load_history';
    }    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'org_code' => 'Код организации',
            'year' => 'Год',            
            'source' => 'Источник',
            'source_description' => 'Источник (подробнее)',
            'error' => 'Ошибка',
            'error_trace' => 'Ошибка (детализация)',
            'created_at' => 'Дата',
        ];
    }
}
