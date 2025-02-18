<?php
namespace app\components;

use Yii;

/**
 * Аутентификация пользователя с использованием Ldap.
 * 
 * Требования:
 * - на веб-сервере должна быть включена windows-аутентификация
 * - от веб-сервера должен передаваться параметр AUTH_USER (в php $_SERVER['AUTH_USER'])
 */
class LdapAuthenticate
{

    /**
     * Объект поиска пользователя в Ldap
     * @var LdapFinder
     */
    public $ldapFinder;

    /**
     * Объект проверяющий наличие, и при отсутствии создание пользователя в БД
     * @var \app\models\UserFactory
     */
    public $userFactory;

    /**
     * @param LdapFinder $ldapFinder
     * @param \app\models\UserFactory $userFactory
     */
    public function __construct($ldapFinder, $userFactory)
    {
        $this->ldapFinder = $ldapFinder;
        $this->userFactory = $userFactory;
    }
    
    /**
     * Аутентификация пользователя на основании учетной записи windows
     * 
     * Последовательность:
     * - поиск пользователя по учетной записи на сервере Ldap
     * - в случае успеха, проверяется пользователь в БД,
     * если его нет, то создается новая запись
     * 
     * @param string $username имя пользователя
     * @return bool
     */
    public function login(string $username)
    {
        $ldapUser = $this->ldapFinder->find($username);
        if (!$ldapUser) {
            Yii::warning("Пользователь $username не найден на сервере Ldap!");
            return false;
        }        

        if (($user = $this->userFactory->findOrCreate($username, $ldapUser)) !== null) {
            return Yii::$app->user->login($user);
        }
        
        return false;
    }
    

}