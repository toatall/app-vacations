<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    // используется windows-аутентификация
    // должна быть выполнена настройка на веб-сервере
    'useWindowsAuthenticate' => true,
    'ldapConfig' => require __DIR__ . '/ldap.php',
];
