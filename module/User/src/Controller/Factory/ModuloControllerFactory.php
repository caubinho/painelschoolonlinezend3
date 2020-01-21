<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\ModuloController;
use User\Form\ModuloForm;
use User\Service\ModuloManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ModuloControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager    = $container->get(ModuloManager::class);
        $Form = $container->get(ModuloForm::class);

       
        // Instantiate the controller and inject dependencies
        return new ModuloController($entityManager, $manager, $Form);
    }
}