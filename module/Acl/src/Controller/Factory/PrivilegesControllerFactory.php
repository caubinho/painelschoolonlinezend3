<?php
namespace Acl\Controller\Factory;

use Acl\Controller\PrivilegesController;
use Acl\Entity\Privilege;
use Acl\Service\PrivilegeManager;
use Interop\Container\ContainerInterface;
use Acl\Form\PrivilegeForm;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class PrivilegesControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager = $container->get(PrivilegeManager::class);
        $form   = $container->get(PrivilegeForm::class);
        // Instantiate the controller and inject dependencies
        return new PrivilegesController($entityManager, $manager, $form );
    }
}