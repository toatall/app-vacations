<?php
return [
    // Auth
    'login' => 'site/login',
    'DELETE logout' => 'site/logout',

    // Contacts
    'GET contacts' => 'contact/index',
    'POST contacts' => 'contact/insert',
    'GET contacts/create' => 'contact/create',
    'GET contacts/<id:\d+>/edit' => 'contact/edit',
    'PUT contacts/<id:\d+>' => 'contact/update',
    'DELETE contacts/<id:\d+>' => 'contact/delete',
    'PUT contacts/<id:\d+>/restore' => 'contact/restore',

    // Organizations
    'GET organizations' => 'organization/index',
    'POST organizations' => 'organization/insert',
    'GET organizations/create' => 'organization/create',
    'GET organizations/<code:\d+>/edit' => 'organization/edit',
    'PUT organizations/<code:\d+>' => 'organization/update',
    'DELETE organizations/<code:\d+>' => 'organization/delete',
    'PUT organizations/<code:\d+>/restore' => 'organization/restore',

    // Users
    'GET users' => 'user/index',
    'POST users' => 'user/insert',
    'GET users/create' => 'user/create',
    'GET users/<id:\d+>/edit' => 'user/edit',
    'PUT users/<id:\d+>' => 'user/update',
    'DELETE users/<id:\d+>' => 'user/delete',
    'PUT users/<id:\d+>/restore' => 'user/restore',

    'reports' => 'report/index',
    '500' => 'site/500'
];