<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\TurmaController;
use User\Entity\User;
use User\Form\TurmaForm;
use User\Service\TurmaManager;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class TurmaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authenticationService = $container->get(AuthenticationService::class);
        $manager    = $container->get(TurmaManager::class);
        $turmaForm = $container->get(TurmaForm::class);


        // Instantiate the controller and inject dependencies
        return new TurmaController($entityManager, $manager, $turmaForm, $authenticationService);
    }
}