<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use User\Controller\VideoController;
use User\Service\VideoManager;
use Zend\ServiceManager\Factory\FactoryInterface;



/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class VideoControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $manager = $container->get(VideoManager::class);
             

        // Instantiate the controller and inject dependencies
        return new VideoController( $entityManager,  $manager );
    }
}