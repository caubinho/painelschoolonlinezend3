<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\BoletoController;
use User\Entity\Setup;
use User\Form\BoletoForm;
use User\Mail\Mail;
use User\Service\BoletoManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class BoletoControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager          = $container->get('doctrine.entitymanager.orm_default');
        $authenticationService  = $container->get(AuthenticationService::class);
        $manager                = $container->get(BoletoManager::class);
        $Form                   = $container->get(BoletoForm::class);
        $mail                   = $container->get(Mail::class);


        $config                 =  $entityManager->getRepository(Setup::class)
            ->findAll();


       
        // Instantiate the controller and inject dependencies
        return new BoletoController($entityManager, $manager, $Form, $authenticationService, $mail, $config);
    }
}