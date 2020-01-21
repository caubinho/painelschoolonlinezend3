<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\PoloManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class PoloManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        // Instantiate the service and inject dependencies

        return new PoloManager($entityManager);
    }
}




