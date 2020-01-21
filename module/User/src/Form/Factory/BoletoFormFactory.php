<?php
namespace User\Form\Factory;

use DoctrineORMModule\Proxy\__CG__\User\Entity\Turma;
use Interop\Container\ContainerInterface;
use User\Entity\User;
use User\Form\BoletoForm;
use User\Form\TurmaForm;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class BoletoFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $repo = $entityManager->getRepository(User::class);
        $alunos = $repo->findAlunos();

        $repoTurma = $entityManager->getRepository(Turma::class);
        $turmas = $repoTurma->findTurmas();


        return new BoletoForm($alunos, $turmas);
    }
}




