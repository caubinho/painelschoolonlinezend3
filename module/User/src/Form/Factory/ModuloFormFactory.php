<?php
namespace User\Form\Factory;

use Interop\Container\ContainerInterface;
use User\Form\ModuloForm;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class ModuloFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');


        return new ModuloForm($entityManager);
    }
}




