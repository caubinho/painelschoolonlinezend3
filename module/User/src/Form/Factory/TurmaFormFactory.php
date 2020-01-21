<?php
namespace User\Form\Factory;

use Interop\Container\ContainerInterface;
use User\Entity\Polo;
use User\Entity\User;
use User\Form\TurmaForm;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class TurmaFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $repoModulo = $entityManager->getRepository(User::class);
        $coordenador = $repoModulo->findCoordenador();

        $resPolo = $entityManager->getRepository(Polo::class);
        $polo = $resPolo->findCidade();


        return new TurmaForm($coordenador, $polo);
    }
}




