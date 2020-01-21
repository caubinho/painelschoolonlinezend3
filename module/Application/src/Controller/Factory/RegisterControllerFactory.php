<?php
namespace Application\Controller\Factory;

use Application\Controller\RegisterController;
use Interop\Container\ContainerInterface;
use User\Entity\Setup;
use User\Form\RegisterForm;
use User\Mail\Mail;
use User\Service\UserManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Renderer\RendererInterface;


/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class RegisterControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(UserManager::class);
        $form       =   $container->get(RegisterForm::class);

        $renderer = $container->get(RendererInterface::class);



        $email = $container->get(Mail::class);

        $config =  $entityManager->getRepository(Setup::class)
            ->findAll();
        
        // Instantiate the controller and inject dependencies
        return new RegisterController($entityManager, $userManager, $form, $renderer, $email, $config );
    }
}