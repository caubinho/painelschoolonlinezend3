<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\ImagemController;
use User\Form\UserForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Controller\UserController;
use User\Service\UserManager;
use Zend\View\Renderer\RendererInterface;


/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ImagemControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(UserManager::class);
        $userForm   = $container->get(UserForm::class);
        $mailConfig = $container->get('Config');
        $view       = $container->get('View');

        $renderer = $container->get(RendererInterface::class);

        // Instantiate the controller and inject dependencies
        return new ImagemController( $entityManager,  $userManager, $userForm, $mailConfig,  $view, $renderer  );
    }
}