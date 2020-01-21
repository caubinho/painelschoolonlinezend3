<?php
namespace User\Form\Factory;

use Acl\Entity\Role;
use Interop\Container\ContainerInterface;
use User\Entity\Polo;
use User\Entity\Turma;
use User\Form\UserForm;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;


/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class UserFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $repoFuncao = $entityManager->getRepository(Role::class);
        $funcao = $repoFuncao->findRole();

        $repoPolo = $entityManager->getRepository(Polo::class);
        $polo = $repoPolo->findCidade();


        return new UserForm($entityManager, $funcao, $polo);
    }
}




