<?php

return [
    'modules' => [
        'Dashboard' => [
            'view dashboard',
        ],
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
            'view user permissions',
            'create permission',
            'edit permission',
            'delete permission',
        ],
        'Customer Management' => [
            'view customer',
            'create customer',
            'edit customer',
            'delete customer',
        ],
        'Booking Request Management' => [
            'view booking request',
            'approve booking request',
            'reject booking request',
        ],
        'Booking Management' => [
            'view booking',
            'create booking',
            'edit booking',
            'delete booking',
            'assign vendor to booking',
        ],
        'Settings Management' => [
            'view settings',
            'edit settings',
            'view pricing settings',
            'edit pricing settings',
            'view profile settings',
            'edit profile settings',
        ],
        
        'Vendor-Supervisor' => [
            'create vendor supervisor link',
            'view vendor supervisor link',
            'edit vendor supervisor link',
            'delete vendor supervisor link',
        ],
        'Vendor' => [
            'view vendor',
            'create vendor',
            'edit vendor',
            'delete vendor',
        ],
        'Supervisor' => [
            'view supervisor',
            'create supervisor',
            'edit supervisor',
            'delete supervisor',
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
