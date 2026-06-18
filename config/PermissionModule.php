<?php

return [
    'modules' => [
        'User Management' => [
            'view user',
            'create user',
            'edit user',
            'delete user',
        ],
        'Role Management' => [
            'view role',
            'create role',
            'edit role',
            'delete role',
        ],
        'Permission Management' => [
            'view permission',
            'create permission',
            'edit permission',
            'delete permission',
        ],
        'Dashboard' => [
            'view dashboard',
        ],
    ],
    'roles' => [
        'Admin' => [
            'User Management',
            'Role Management',
            'Permission Management',
            'Dashboard',
        ],
        'Employee' => [
            'Dashboard',
        ],
    ]
];
