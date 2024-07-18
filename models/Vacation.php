<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vacations".
 *
 * @property int $id
 * @property int $id_kind
 * @property int $id_employee
 * @property string $date_from
 * @property string $date_to
 * @property string $status
 * @property string|null $created_at
 *
 * @property Employee $employee
 */
class Vacation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_kind', 'id_employee', 'date_from', 'date_to', 'status'], 'required'],
            [['id_kind', 'id_employee'], 'default', 'value' => null],
            [['id_kind', 'id_employee'], 'integer'],
            [['date_from', 'date_to', 'created_at'], 'safe'],
            [['status'], 'string', 'max' => 5],
            [['id_employee'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['id_employee' => 'id']],            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'id_kind' => 'Вид отпуска',
            'id_employee' => 'Сотрудник',
            'date_from' => 'Дата начала',
            'date_to' => 'Дата окончания',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[Employee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'id_employee']);
    }
    
}
