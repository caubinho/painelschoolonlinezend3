<?php

namespace User;

use User\View\Helper\DadosSite;
use User\View\Helper\Factory\DadosSiteFactory;
use User\View\Helper\Factory\UserIdentityFactory;
use User\View\Helper\LimitaCaracter;
use User\View\Helper\UserIdentity;
use Zend\Router\Http\Literal;
use User\Controller;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'login' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => 'auth',
                        'action'     => 'login',
                    ],
                ],
            ],





            'logout' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        'controller' => 'auth',
                        'action'     => 'logout',
                    ],
                ],
            ],


            'set-password' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/set-password',
                    'defaults' => [
                        'controller' => 'users',
                        'action'     => 'setPassword',
                    ],
                ],
            ],

            'boleto' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/boleto[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'codigo' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'boleto',
                        'action'        => 'download',
                    ],
                ],
            ],

            'recover' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/recover[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'codigo' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'users',
                        'action'        => 'recover',
                    ],
                ],
            ],

            //rota padrao admin
            'admin' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/dashboard'
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'default' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'home',
                                'action' => 'index'
                            ]
                        ]
                    ],



                    'cronograma' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cronograma[/:action]/turma[/:id]',
                            'constraints' => [
                                //'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z0-9_-]+',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'cronograma',
                                'action' => 'video'
                            ]
                        ]
                    ],

                    'modulo' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/course[/:action][:code][:turma][:video]',
                            'constraints' => [
                                //'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'code' => '[0-9]+',
                                'aula' => '[0-9]+',
                                'turma' => '[0-9]+',
                                'video' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'course',
                                'action' => 'index'
                            ]
                        ]
                    ],

                    'trabalho' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/trabalho[/:action][/:code][/:turma][/:aluno]',
                            'constraints' => [
                                'turma'  => '[0-9]+',
                                'aluno'  => '[0-9]+',
                                'action' => '[a-z_-]+',
                                'code' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'trabalho',
                                'action' => 'index',
                            ]
                        ]
                    ],

                    'imagem' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/imagem[/:action][/:user][/:perfil]',
                            'constraints' => [
                                'user'  => '[0-9]+',
                                'perfil'  => '[a-z_-]+',
                                'action' => '[a-z_-]+',
                            ],
                            'defaults' => [
                                'controller' => 'imagem',
                                'action' => 'index',
                            ]
                        ]
                    ],


//                    'aula' => [
//                        'type' => Segment::class,
//                        'options' => [
//                            'route' => '/course[/:action][:code][:turma][:video]',
//                            'constraints' => [
//                                'code' => '[0-9]+',
//                                'video'    => '[0-9]+',
//                                'turma' => '[0-9]+',
//                            ],
//                            'defaults' => [
//                                'controller' => 'course',
//                                'action' => 'aula'
//                            ]
//                        ]
//                    ],

                    'print' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:controller/print[/:action[/:id[/:pdf]]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'home',
                                'action' => 'index'
                            ]
                        ]
                    ],



                    'turma' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:controller/turma[/:turma[/:action[/:id]]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'home',
                                'action' => 'index'
                            ]
                        ]
                    ],

                    'filter' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '[/:controller][/:action][/:turma]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'turma' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',

                            ],
                            'defaults' => [
                                'controller' => 'home',
                                'action' => 'index'
                            ]
                        ]
                    ],


                    
                ]
            ],


            'api-rest' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/dashboard/api/[:controller][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                    )
                ),
            ),
          
        ],
    ],

    'module_layouts' => [
        'User' => 'layout/admin'
    ],


    'view_manager' => [
        'template_path_stack' => [
            'user' => __DIR__ . "/../view"
        ],


        'strategies' => [
            'ViewJsonStrategy'
        ],


    ],

    'view_helpers' => [
        'factories' => [
            UserIdentity::class => UserIdentityFactory::class,
            DadosSite::class => DadosSiteFactory::class,
            LimitaCaracter::class => InvokableFactory::class,
        ],
        'aliases' => [
            'UserIdentity' => UserIdentity::class,
            'DadosSite'     => DadosSite::class,
            'LimitaCaracter' => LimitaCaracter::class,
        ],
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
    ]
];