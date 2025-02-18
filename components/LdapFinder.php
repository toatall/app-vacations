<?php
namespace app\components;

use Symfony\Component\Ldap\Ldap;
use yii\base\InvalidConfigException;

/**
 * Поиск пользователя на сервере Ldap
 */
class LdapFinder
{
    /**
     * Строка подключения к серверу
     * Например, ldap://example.ru:389
     * @var string
     */
    public $connectionString;
    /**
     * Учетная запись
     * @var string
     */
    public $user;
    /**
     * Пароль учетной записи
     * @var string
     */
    public $password;
    /**
     * Объект каталога для поиска
     * Например, OU=dep,DC=server,DC=example,DC=ru
     * @var string
     */
    public $baseDn;

    /**
     * @param array $config настройки подключения
     * [connectionString, user, password, baseDn]
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config)
    {
        $this->connectionString = $config['connectionString'] ?? null;
        if ($this->connectionString === null) {
            throw new InvalidConfigException('Параметр "connectionString" не указан!');            
        }
        $this->user = $config['user'] ?? null;
        $this->password = $config['password'] ?? null;
        $this->baseDn = $config['baseDn'] ?? null;
        if ($this->baseDn === null) {
            throw new InvalidConfigException('Параметр "baseDn" не указан!');
        }
    }

    /**
     * Поиск пользователя по учетной записи пользователя (sAMAccountName)
     * @param string $username учетная запись пользователя
     * @return array|bool
     */
    public function find(string $username)
    {
        $ldap = Ldap::create('ext_ldap', ['connection_string' => $this->connectionString]);
        $ldap->bind($this->user, $this->password);
        $result = $ldap->query($this->baseDn, $this->generateQuery($username));
        $records = $result->execute()->toArray();
        if ($records && ($attributes = $records[0]->getAttributes())) {
            return $attributes;
        }
        return false;
    }

    /**
     * Запрос
     * @param string $username
     * @return string
     */
    private function generateQuery(string $username): string
    {        
        return "(&(objectCategory=person)(objectClass=user)(sAMAccountName=$username))";
    }

}