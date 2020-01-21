<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\TrabalhoController;
use User\Entity\Setup;
use User\Form\TrabalhoForm;
use User\Mail\Mail;
use User\Service\TrabalhoManager;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class TrabalhoControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager          = $container->get('doctrine.entitymanager.orm_default');
        $authenticationService  = $container->get(AuthenticationService::class);
        $manager                = $container->get(TrabalhoManager::class);
              $form                   = $container->get(TrabalhoForm::class);


        $email = $container->get(Mail::class);
        $config =  $entityManager->getRepository(Setup::class)
            ->findAll();


        // Instantiate the controller and inject dependencies
        return new TrabalhoController($entityManager,   $authenticationService, $manager,  $form, $email, $config );
    }
}