<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\CourseController;
use User\Entity\Atividade;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class CourseControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authenticationService = $container->get(AuthenticationService::class);



        // Instantiate the controller and inject dependencies
        return new CourseController($entityManager, $authenticationService);
    }
}