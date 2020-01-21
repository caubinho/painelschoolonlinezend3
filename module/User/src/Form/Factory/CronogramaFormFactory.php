<?php
namespace User\Form\Factory;

use Interop\Container\ContainerInterface;
use User\Entity\Aula;
use User\Entity\Modulo;
use User\Entity\User;
use User\Form\CronogramaForm;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class CronogramaFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $repoModulo = $entityManager->getRepository(Modulo::class);
        $modulo = $repoModulo->findPublishedModulos();

        $repoAula = $entityManager->getRepository(Aula::class);
        $aula = $repoAula->findPublishedAula();

        $repo = $entityManager->getRepository(User::class);
        $professor = $repo->findProfessores();



        return new CronogramaForm($modulo, $aula, $professor);
    }
}




