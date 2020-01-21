<?php
namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use User\View\Helper\UserIdentity;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;


/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class UserIdentityFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authenticationService = $container->get(AuthenticationService::class);
      


        // Instantiate the helper.
        return new UserIdentity($entityManager, $authenticationService);
    }
}

