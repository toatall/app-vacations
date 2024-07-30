<?php

namespace app\models;

use app\components\SoftDeleteTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string|null $org_code
 * @property string|null $org_code_select
 * @property string|null $full_name
 * @property string|null $email
 * @property string|null $password
 * @property string|null $post
 * @property string|null $userPost
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    use SoftDeleteTrait;

    public $newPassword;    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [            
            [['username', 'email'], 'required'],          
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['full_name'], 'string', 'max' => 250],
            [['email'], 'string', 'max' => 50],
            [['org_code', 'org_code_select'], 'string', 'max' => 5],
            [['password'], 'string', 'max' => 255],            
            [['username', 'email'], 'unique'],
            [['newPassword'], 'string', 'max' => 255],            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return static::attributeLabelsStatic();
    }
    
    /**
     * @return string[]
     */
    public static function attributeLabelsStatic()
    {
        return [
            'id' => 'ИД',            
            'username' => 'Учетная запись',
            'org_code' => 'Организация',
            'org_code_select' => 'Код организации выбран',
            'full_name' => 'ФИО',
            'email' => 'Email',
            'password' => 'Пароль',
            'newPassword' => 'Пароль',
            'post' => 'Должность',
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
     * {@inheritDoc}
     */
    public function beforeSave($insert) 
    {        
        if (!empty($this->newPassword)) {
            $this->password = Yii::$app->security->generatePasswordHash($this->newPassword);
        }        
        return parent::beforeSave($insert);
    }

    public function getUserPost()
    {
        return $this->post;
    }

    public function setUserPost($value)
    {
        $this->post = $value;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * @param string $email
     * @return User|Yii\db\ActiveRecord|null
     */
    public static function findByEmail($email)
    {
        return static::findActual()
            ->andWhere(['email' => $email])
            ->one();
    }

    public static function findActual()
    {
        return static::find()
            ->where(['deleted_at' => null]);
    }


    /**
     * {@inheritDoc}
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->where(['id' => $id])
            ->one();
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * @param int $id
     * @return array|null
     */
    public static function findById($id)
    {
        return static::find()            
            ->where('id=:id', ['id' => $id])
            ->asArray()
            ->one();
    }

    public static function findByParams($search = null, $trashed = null)
    {
        $query = (new Query())            
            ->from('users');

        if (!empty($search)) {
            $query->andWhere(['like', 'username', $search]);
            $query->orWhere(['like', 'full_name', $search]);
        }        

        if ($trashed === 'with') {
        } elseif ($trashed === 'only') {
            $query->andWhere(['not', ['deleted_at' => null]]);
        } else {
            $query->andWhere(['deleted_at' => null]);
        }

        $query->orderBy('full_name ASC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100000,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return User
     */
    public static function fromArray(array $params = [])
    {
        $user = new static();
        $user->attributes = $params;
        return $user;
    }

}
