<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\RecoverController;
use User\Form\UserForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\UserManager;


/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class RecoverControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(UserManager::class);
        $mailConfig = $container->get('Config');
        $view       = $container->get('View');

        // Instantiate the controller and inject dependencies
        return new RecoverController( $entityManager,  $userManager, $mailConfig,  $view  );
    }
}