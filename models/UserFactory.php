<?php
namespace app\models;

use Yii;

/**
 * Поиск и создание пользователя в БД
 */
class UserFactory
{

    /**
     * Поиск пользователя в БД
     * если пользователя нет, то создается новая запись
     * @param string $username имя пользователя
     * @param array $attributes аттрибуты, полученные от сервера Ldap
     * @return User|null возвращается модель пользователя
     */
    public function findOrCreate(string $username, array $attributes)
    {
        $model = User::find()->where(['username' => $username])->one();
        if ($model === null) {
            Yii::info("Пользователь $username отсутствует в БД. Создание новой записи!");
            $model = $this->createUser($username, $attributes);
        }
        return $model;
    }

    /**
     * Создание пользователя в БД
     * @param mixed $username
     * @param mixed $attributes 
     * @return User
     */
    private function createUser(string $username, array $attributes)
    {
        $orgCode = $this->getOrgCodeFromUsername($username);
        $userModel = new User([
            'username' => $username,
            'org_code' => $orgCode,
            'org_code_select' => $orgCode,
            'full_name' => $attributes['cn'],
            'email' => $attributes['mail'],
            'position' => $attributes['title'],
            'newPassword' => Yii::$app->security->generateRandomString(),
        ]);

        if (!$userModel->save()) {
            Yii::warning("Пользователь $username не сохранен в БД! Ошибки: " . print_r($userModel->getErrors(), true));
        }

        return $userModel;
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
            if (is_array($result) && count($result) > 0) {
                return $result[0];
            }
        }
        return null;
    }

}