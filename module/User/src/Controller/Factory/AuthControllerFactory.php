<?php

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\AuthController;
use Zend\Authentication\AuthenticationServiceInterface;

class AuthControllerFactory
{

    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get(AuthenticationServiceInterface::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        return new AuthController($authService, $entityManager);
    }


}