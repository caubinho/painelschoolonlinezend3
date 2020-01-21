<?php
namespace Acl\Permissions\Factory;

use Acl\Entity\Privilege;
use Acl\Entity\Resource;
use Acl\Entity\Role;
use Acl\Permissions\Acl;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class AclFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roles      = $entityManager->getRepository(Role::class)->findAll();
        $resources  = $entityManager->getRepository(Resource::class)->findAll();
        $privileges = $entityManager->getRepository(Privilege::class)->findAll();

        // Instantiate the controller and inject dependencies
        return new Acl($roles, $resources, $privileges);
    }
}