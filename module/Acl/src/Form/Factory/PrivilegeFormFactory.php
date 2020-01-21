<?php
namespace Acl\Form\Factory;

use Interop\Container\ContainerInterface;
use Acl\Form\PrivilegeForm;
use Zend\ServiceManager\Factory\FactoryInterface;



/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class PrivilegeFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');


        return new PrivilegeForm($entityManager);
    }
}
