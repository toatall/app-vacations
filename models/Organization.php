<?php

namespace app\models;

use app\components\SoftDeleteTrait;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "organizations".
 *
 * @property int $id
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
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
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        $organization = static::find()                       
            ->where('id=:id', ['id' => $id])
            ->asArray()
            ->one();

        if (is_null($organization)) {
            return $organization;
        }        

        return $organization;
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
            ->from('organizations');

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
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
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
