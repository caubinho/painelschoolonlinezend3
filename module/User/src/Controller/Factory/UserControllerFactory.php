<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Entity\Setup;
use User\Form\UserForm;
use User\Mail\Mail;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Controller\UserController;
use User\Service\UserManager;
use Zend\View\Renderer\RendererInterface;


/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(UserManager::class);
        $userForm   = $container->get(UserForm::class);

        $email = $container->get(Mail::class);

        $config =  $entityManager->getRepository(Setup::class)
            ->findAll();


        // Instantiate the controller and inject dependencies
        return new UserController( $entityManager,  $userManager, $userForm, $email, $config);
    }
}