<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\LinkController;
use User\Service\LinkManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class LinkControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager = $container->get(LinkManager::class);


        // Instantiate the controller and inject dependencies
        return new LinkController($entityManager,  $manager );
    }
}