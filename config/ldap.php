<?php

// настройки для подключения к Ldap
return [
    'connectionString' => 'ldap://example.ru:389',
    'user' => 'admin',
    'password' => 'P@ssword',
    'baseDn' => 'OU=dep,DC=server,DC=example,DC=ru',   
];