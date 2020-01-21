<?php
namespace User\Form\Factory;

use Interop\Container\ContainerInterface;
use User\Entity\User;
use User\Form\TrabalhoForm;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class TrabalhoFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $repo = $entityManager->getRepository(User::class);
        $professor = $repo->findProfessores();


        return new TrabalhoForm($professor);
    }
}




