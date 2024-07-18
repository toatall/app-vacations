<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "load_history".
 *
 * @property int $id
 * @property string $source
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
    public function rules()
    {
        return [
            [['source'], 'required'],
            [['created_at'], 'safe'],
            [['source'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'source' => 'Источник',
            'created_at' => 'Дата',
        ];
    }
}
