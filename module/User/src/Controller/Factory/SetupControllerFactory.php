<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\SetupController;
use User\Service\SetupManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class SetupControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager = $container->get(SetupManager::class);


        // Instantiate the controller and inject dependencies
        return new SetupController($entityManager,  $manager );
    }
}