<?php

namespace Application;

use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\RegisterControllerFactory;
use Application\Controller\IndexController;
use Application\Controller\RegisterController;
use User\Controller\UserController;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'site' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            'register' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/register',
                    'defaults' => [
                        'controller'    => 'register',
                        'action'        => 'register',
                    ],
                ],
            ],

            'activate' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/register/activate[/:id]',
                    'constraints' => [
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => 'register',
                        'action'        => 'activate',
                    ],
                ],
            ],


        ],
    ],


    'module_layouts' => [
        'Application' => 'layout/layout'
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'             => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index'   => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'                 => __DIR__ . '/../view/error/404.phtml',
            'error/index'               => __DIR__ . '/../view/error/index.phtml',
            'header'                    => __DIR__ . '/../view/layout/header.phtml',
            'sidebar'                   => __DIR__ . '/../view/layout/sidebar.phtml',
            'footer'                    => __DIR__ . '/../view/layout/footer.phtml',
            'videos'                    => __DIR__ . '/../view/partials/videos.phtml',
            'material'                  => __DIR__ . '/../view/partials/material.phtml',
            'link'                      => __DIR__ . '/../view/partials/link.phtml',
            'atividades'                => __DIR__ . '/../view/partials/atividades.phtml',
            'cronograma'                => __DIR__ . '/../view/partials/cronograma.phtml',
            'aulavideo'                 => __DIR__ . '/../view/partials/aulavideo.phtml',
            'filterTurma'               => __DIR__ . '/../view/partials/filterTurma.phtml',

        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],

        'strategies' => [
            'ViewJsonStrategy'
        ],
    ],
];
