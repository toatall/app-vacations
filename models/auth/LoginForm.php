<?php

namespace app\models\auth;

use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 * @property \app\models\User|null $user This property is read-only.
 *
 */
class LoginForm extends LoginAbstract
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();

            if (!$this->_user || !$this->_user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return \app\models\User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = $this->getUserIdentity()::findByEmailOrName($this->email);
        }
        return $this->_user;
    }

    /**
     * @return \app\models\User
     */
    private function getUserIdentity()
    {
        return Yii::createObject(Yii::$app->user->identityClass);
    }

}
