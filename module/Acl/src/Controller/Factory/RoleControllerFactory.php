<?php
namespace Acl\Controller\Factory;

use Acl\Controller\RoleController;
use Acl\Form\RoleForm;
use Acl\Service\RoleManager;
use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class RoleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roleManager = $container->get(RoleManager::class);
        $roleForm   = $container->get(RoleForm::class);


        // Instantiate the controller and inject dependencies
        return new RoleController($entityManager, $roleManager, $roleForm);
    }
}