<?php
namespace User\Mail\Factory;

use Interop\Container\ContainerInterface;
use User\Mail\Mail;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Renderer\RendererInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class MailFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $renderer       = $container->get(RendererInterface::class);
        $view           = $container->get('View');

        // Instantiate the controller and inject dependencies
        return new Mail($entityManager, $renderer, $view);
    }
}