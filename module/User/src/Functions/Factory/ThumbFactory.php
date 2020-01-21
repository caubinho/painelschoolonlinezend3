<?php
namespace User\Thumb\Factory;

use Interop\Container\ContainerInterface;
use User\Thumb\Thumb;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ThumbFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $thumb = $container->get('WebinoImageThumb');

        // Instantiate the controller and inject dependencies
        return new Thumb($thumb);
    }
}