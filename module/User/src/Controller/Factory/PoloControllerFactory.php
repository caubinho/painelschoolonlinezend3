<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\PoloController;
use User\Form\PoloForm;
use User\Service\PoloManager;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class PoloControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $poloManager    = $container->get(PoloManager::class);
        
        // Instantiate the controller and inject dependencies
        return new PoloController($entityManager, $poloManager);
    }
}