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
    '500' => 'site/500',

    // Chart
    'GET chart/count-of-vacations-per-year-by-day/<code_org:\d+>/<year:\d{4}>' => 'chart/count-of-vacations-per-year-by-day',

    // Statistics    
    [
        'pattern' => 'statistics/total-employees/<code_org:\d+>/<year:\d{4}>/<int_days_from:\d+>/<int_days_to:\d+>',
        'route' => 'statistics/total-employees',
        'defaults' => ['int_days_from' => 1, 'int_days_to' => 7],
        'verb' => 'GET',
    ],
    'GET statistics/years/<code_org:\d+>' => 'statistics/years',

    // Vacations
    'GET vacations/employees-on-vacations/<code_org:\d+>/<year:\d{4}>' => 'vacations/employees-on-vacations',
    [
        'pattern' => 'vacations/employees-will-be-on-vacations/<code_org:\d+>/<year:\d{4}>/<int_days_from:\d+>/<int_days_to:\d+>',
        'route' => 'vacations/employees-will-be-on-vacations',
        'defaults' => ['int_days_from' => 1, 'int_days_to' => 7],
        'verb' => 'GET',
    ],
    'GET find' => 'vacations/find',
    'GET find-data/<code_org:\d+>/<year:\d{4}>' => 'vacations/find-data',

    // Table
    'GET table/index' => 'table/index',
    'GET table/table-data/<code_org:\d+>/<year:\d{4}>' => 'table/table-data',
    

];