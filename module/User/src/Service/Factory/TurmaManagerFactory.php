<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;

use User\Service\TurmaManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\PostManager;

/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class TurmaManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        // Instantiate the service and inject dependencies

        return new TurmaManager($entityManager);
    }
}




