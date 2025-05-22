<?php

namespace app\models\auth;

use app\models\UserFactory;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * LoginForm is the model behind the login form.
 *
 * @property \app\models\User|null $user This property is read-only.
 *
 */
class LoginLdap extends LoginAbstract
{
    /**
     * @var string
     */
    private $_username;

    /**
     * @var bool
     */
    public $remember = true;

    /**
     * Атрибуты (из LDAP) пользователя
     * @var array
     */
    private $_userAttributes;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [];
    }

    /**
     * Аутентифицированный пользователь через веб-сервер
     * @return string|null
     */
    private function getAuthUser(): string|null
    {
        return $_SERVER['AUTH_USER'] ?? null;
    }

    /**
     * Ошибка при неудачном входе
     * Для исключения зацикливания при входе
     * @throws \yii\web\ForbiddenHttpException
     * @return never
     */
    private function throwError(): never
    {
        $userIdentity = $this->_username ?? Yii::$app->request->getRemoteIP();
        Yii::error("Не удалось выполнить вход для пользователя {$userIdentity}!");

        throw new ForbiddenHttpException();
    }

    /**
     * {@inheritDoc}
     */
    public function load($data, $formName = null)
    {
        $this->_username = $this->getAuthUser();
        if (!$this->_username) {
            Yii::error("Отсутствует поле \$_SERVER['AUTH_USER'], либо имя не указано! Проверьте настройку Windows-аутентификации в настройках веб-сервера.");
            $this->throwError();
        }

        $this->_userAttributes = Yii::$app->ldap->authenticate($this->_username);
        if (!$this->_userAttributes) {
            Yii::error("Пользователь {$this->_username} не найден на сервере LDAP!");
            $this->throwError();
        }

        return true;
    }

    /**
     * Logs in a user using the provided email and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            return Yii::$app->user->login($user, $this->remember ? 3600 * 24 * 30 : 0);
        }
        $this->throwError();
    }

    /**
     * Получение пользователя по имени
     * @return \app\models\User|null
     */
    public function getUser()
    {
        $userFactory = new UserFactory();
        return $userFactory->findOrCreate($this->_userAttributes['username'], $this->_userAttributes);
    }

}
