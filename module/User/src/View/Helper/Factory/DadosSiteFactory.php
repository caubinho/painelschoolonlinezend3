<?php
namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use User\View\Helper\DadosSite;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class DadosSiteFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        // Instantiate the helper.
        return new DadosSite($entityManager);
    }
}

