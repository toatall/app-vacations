<?php
namespace app\components\ldap;

use yii\base\Component;

/**
 * Компонент поиска и аутентификации пользователя через LDAP по имени его ученой записи
 * LDAP-атрибут для поиска: sAMAccountName
 * 
 * Аутентификация считается успешной, если удалось подключиться к LDAP-серверу и найти указанную учетную запись
 * Подключение выполняется от указанных логина и пароля (поля $bindUser и $bindPassword)
 * 
 * $searchAttributes - указываются атрибуты, которые следует получить от LDAP-сервера
 * $options - параметры подключения к ldap (ldap_set_option(...))
 * 
 * @author toatall <toatall@mail.ru>
 */
class Ldap extends Component
{
    /**
     * Строка подключения
     * @var string
     */
    public string $connectionString;

    /**
     * Имя пользователя для подключения
     * @var string
     */
    public string $bindUser;

    /**
     * Имя пользователя для подключения
     * @var string
     */
    public string $bindPassword;

    /**
     * Базовое DN
     * @var string
     */
    public string $baseDn;

    /**
     * Опции подключения
     * 
     * Пример:
     * [LDAP_OPT_PROTOCOL_VERSION => 3, LDAP_OPT_REFERRALS => false, ...]
     * @var array
     */
    public array $options = [];

    /**
     * Извлекаемые атрибуты
     * @var array
     */
    public array $searchAttributes = ['sAMAccountName', 'cn', 'mail', 'title'];

    /**
     * Количество выбранных записей
     * @var int
     */
    public int $searchSizeLimit = 1;


    /**
     * Подключение к серверу     
     * @return \LDAP\Connection
     */
    private function connect(string $connectionString): \LDAP\Connection
    {
        $connection = ldap_connect($connectionString);

        $this->options = [
            LDAP_OPT_PROTOCOL_VERSION => 3,
            LDAP_OPT_REFERRALS => false,
        ] + $this->options;

        foreach ($this->options as $option => $value) {
            ldap_set_option($connection, $option, $value);
        }

        return $connection;
    }

    /**
     * Привязывание к каталогу
     * @param \LDAP\Connection $connection
     * @param mixed $dn имя пользователя
     * @param mixed $password пароль
     * @return bool
     */
    private function bind(\LDAP\Connection $connection, ?string $dn, ?string $password): bool
    {
        return ldap_bind($connection, $dn ?? $this->bindUser, $password ?? $this->bindPassword);
    }

    /**
     * Поиск пользователя
     * @param \LDAP\Connection $connection
     * @param string $username
     * @return array
     */
    private function search(\LDAP\Connection $connection, string $username): array
    {
        $result = ldap_search(
            ldap: $connection,
            base: $this->baseDn,
            filter: "(&(objectClass=Person)(objectClass=user)(sAMAccountName=$username))",
            attributes: $this->searchAttributes,
            sizelimit: $this->searchSizeLimit,
        );
        $entry = ldap_first_entry($connection, $result);
        return ldap_get_attributes($connection, $entry);
    }

    /**
     * Аутентификация пользователя
     * При успешной аутентификации возвращается список атрибутов пользователя
     * @param string $user имя пользователя (в формате DOMAIN\0000-00-000 или DOMAIN\n0000-00-000)
     * @return array|null
     */
    public function authenticate(string $user)
    {
        if (!($connection = $this->connect($this->connectionString))) {
            throw new LdapException('Unable connect ldap server!');
        }

        if (!$this->bind($connection, $this->bindUser, $this->bindPassword)) {
            throw new LdapException('Unable bind ldap server!');
        }

        $username = $this->getUsernameFromPrincipalName($user);


        if (!($entry = $this->search($connection, $username))) {
            return null;
        }

        return array_merge($this->getAttributeValues($entry), ['username' => $username]);
    }

    /**
     * Извлечение значений на основании указанных атрибутов в поле $searchAttributes
     * @param array $entry
     * @return array
     */
    private function getAttributeValues(array $entry): array
    {
        $result = [];
        foreach ($this->searchAttributes as $attribute) {
            $result[$attribute] = $entry[$attribute][0] ?? null;
        }
        return $result;
    }

    /**
     * Извлечение имени пользователя из полного имени с доменом 
     * Имя пользователя должно соответствовать регулярному выражению
     * 
     * Например: 
     * DOMAIN\n0000-11-001 => n0000-11-001, 
     * DOMAIN\0000-00-00 => 0000-00-00, 
     * 0101-00-002@domain.com => 0101-00-002
     * @param string $username
     * @return string
     */
    private function getUsernameFromPrincipalName(string $username): string
    {
        if (preg_match('/n?\d{4}-\d{2}-\d{3}/i', $username, $matches)) {
            return $matches[0];
        }
        return $username;
    }

}