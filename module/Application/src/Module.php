<?php

namespace Application;

use Application\Controller\Factory\IndexControllerFactory;
use Application\Controller\Factory\RegisterControllerFactory;
use Application\Controller\IndexController;
use Application\Controller\RegisterController;

class Module
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                IndexController::class              => IndexControllerFactory::class,
                RegisterController::class           => RegisterControllerFactory::class,
            ],

            'aliases' => [
                'register'  => RegisterController::class,
                'index'     => IndexController::class,
            ]
        ];
    }
}
