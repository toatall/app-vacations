<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departments".
 *
 * @property int $id
 * @property string $org_code
 * @property string $name
 * @property string|null $created_at
 *
 * @property Organization $orgCode
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['org_code', 'name'], 'required'],
            [['created_at'], 'safe'],
            [['org_code'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 200],
            [['org_code'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::class, 'targetAttribute' => ['org_code' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ИД',
            'org_code' => 'Код',
            'name' => 'Наименование',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[OrgCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrgCode()
    {
        return $this->hasOne(Organization::class, ['code' => 'org_code']);
    }
}
