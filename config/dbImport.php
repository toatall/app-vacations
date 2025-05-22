<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlsrv:server=server-name;database=db',
    'username' => 'user',
    'password' => 'secret',

    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
