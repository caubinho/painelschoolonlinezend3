<?php
namespace Acl\Controller\Factory;

use Acl\Controller\ResourcesController;
use Acl\Service\ResourceManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ResourcesControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $resourceManager = $container->get(ResourceManager::class);

        // Instantiate the controller and inject dependencies
        return new ResourcesController($entityManager, $resourceManager);
    }
}