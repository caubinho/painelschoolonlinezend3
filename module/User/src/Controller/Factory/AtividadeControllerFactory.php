<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\AtividadeController;
use User\Service\AtividadeManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class AtividadeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager = $container->get(AtividadeManager::class);


        // Instantiate the controller and inject dependencies
        return new AtividadeController($entityManager,  $manager );
    }
}