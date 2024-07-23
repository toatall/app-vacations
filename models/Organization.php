<?php

namespace app\models;

use app\components\SoftDeleteTrait;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "organizations".
 *
 * @property string $code
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class Organization extends ActiveRecord
{
    use SoftDeleteTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organizations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [          
            [['code', 'name'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['code'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 250],
            [['code'], 'unique'],                        
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return static::attributeLabelsStatic();
    }    

    public static function attributeLabelsStatic()
    {
        return [
            'id' => 'ИД',            
            'code' => 'Код',
            'name' => 'Наименование',            
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'deleted_at' => 'Дата удаления',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => date('Y-m-d H:i:s')
            ],            
        ];
    }   

    /**
     * @param string $code
     * @return array|null
     */
    public static function findByCode($code)
    {
        return static::find()                       
            ->where(['code' => $code])
            ->asArray()
            ->one();
    }

    /**
     * @param array $params
     * @return Organization
     */
    public static function fromArray(array $params = [])
    {
        $organization = new static();
        $organization->attributes = $params;
        return $organization;
    }

    /**
     * @param string $search
     * @param string $trashed
     * @return ActiveDataProvider
     */
    public static function findByParams($search = null, $trashed = null)
    {
        $query = (new Query())            
            ->from('organizations')
            ->orderBy(['code' => SORT_ASC]);

        if (!empty($search)) {
            $query->andWhere(['like', 'code', $search]);
            $query->orWhere(['like', 'name', $search]);
        }

        if ($trashed === 'with') {
        } elseif ($trashed === 'only') {
            $query->andWhere(['not', ['deleted_at' => null]]);
        } else {
            $query->andWhere(['deleted_at' => null]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100000,
            ],            
        ]);

        return $dataProvider;
    }

    public static function findActual()
    {
        return static::find()->andWhere(['deleted_at' => null]);
    }

    /**
     * @return array
     */
    public static function getPairs()
    {
        $pairs = (new Query())
            ->select('code, name')
            ->from('organizations')
            ->orderBy('code')
            ->where(['deleted_at' => null])
            ->all();
        return $pairs;
    }

}
