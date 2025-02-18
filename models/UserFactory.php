<?php
namespace app\models;

use Yii;

/**
 * Поиск и создание пользователя в БД
 */
class UserFactory
{
    /**
     * Имя класса работы с пользователями
     * @var string
     */
    public $userClass = 'app\models\User';

    /**
     * Создание объекта работы с пользователями
     * @return User
     */
    private function getUserModel($params = [])
    {
        return Yii::createObject($this->userClass, $params);
    }

    /**
     * Поиск пользователя в БД
     * если пользователя нет, то создается новая запись
     * @param string $username имя пользователя
     * @param array $attributes аттрибуты, полученные от сервера Ldap
     * @return User|null возвращается модель пользователя
     */
    public function findOrCreate(string $username, array $attributes)
    {
        $userModel = $this->getUserModel();

        $model = $userModel->find()->where(['username' => $username])->one();
        if ($model === null) {
            Yii::info("Пользователь $username отсутствует в БД. Создание новой записи!");
            if (($model = $this->createUser($username, $attributes)) === null) {
                Yii::warning("Пользователь $username не создан в БД!");
                return null;
            }
        }
        return $model;
    }

    /**
     * Создание пользователя в БД
     * @param mixed $username
     * @param mixed $attributes 
     * @return User|null
     */
    private function createUser(string $username, array $attributes)
    {        
        $userModel = $this->getUserModel();
        $userModel->username = $username;
        $userModel->org_code = $this->getOrgCodeFromUsername($username);
        $userModel->org_code_select = $userModel->org_code;
        $userModel->full_name = $attributes['displayName'][0] ?? null;
        $userModel->email = $attributes['mail'][0] ?? null;
        $userModel->position = $attributes['title'][0] ?? null;
        $userModel->newPassword = md5(time());

        if ($userModel->save()) {
            return $userModel;
        }

        Yii::warning("Пользователь $username не сохранен в БД! Ошибки: " . print_r($userModel->getErrors(), true));
        
        return null;
    }

    /**
     * Извлечение кода организации из имени учетной записи
     * @param mixed $username
     * @return string|null
     */
    private function getOrgCodeFromUsername(string $username)
    {
        $result = null;
        if (preg_match('/^\d{4}|^n\d{4}/', $username, $result)) {
            if (is_array($result) && count($result)>0) {
                return $result[0];
            }
        }
        return null;
    }

}