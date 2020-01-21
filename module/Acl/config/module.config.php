<?php

namespace Acl;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Literal;
use Acl\Controller;

return [
    'router' => [
        'routes' => [
            'acl' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/acl'
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'default' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\RoleController::class,
                                'action' => 'index'
                            ]
                        ]
                    ],
                ]
            ],
        ],
    ],

    'module_layouts' => [
        'Acl' => 'layout/admin'
    ],

    'view_manager' => [
        'template_path_stack' => [
            'acl' => __DIR__ . "/../view"
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ],
        'fixtures' => [
            'AclFixture' => __DIR__ . '/../src/Entity/Fixture'
        ]
    ]
];