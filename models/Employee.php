<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property int $id_department
 * @property string $full_name
 * @property string|null $created_at
 *
 * @property Vacation[] $vacations
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_department', 'full_name'], 'required'],
            [['id_department'], 'default', 'value' => null],
            [['id_department'], 'integer'],
            [['created_at'], 'safe'],
            [['full_name'], 'string', 'max' => 250],
            [['id_department', 'full_name'], 'unique', 'targetAttribute' => ['id_department', 'full_name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'id_department' => 'Отдел',
            'full_name' => 'ФИО',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[Vacations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacations()
    {
        return $this->hasMany(Vacation::class, ['id_employee' => 'id']);
    }
}
