<?php
namespace User\Service;

use User\Entity\Aula;
use User\Entity\Trabalho;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */

class TrabalhoManager extends AbstractService
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        parent::__construct($entityManager);

        $this->entityManager = $entityManager;
        $this->entity = Trabalho::class;
    }

    public function insert(array $data)
    {
        //print_r($data); die;
        /* @var \User\Entity\Trabalho $entity*/
        $entity = new $this->entity($data);

        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        $aula = $this->entityManager->getReference(Aula::class, $data['aula']);
        $entity->setAula($aula);

        $aluno = $this->entityManager->getReference(User::class, $data['aluno']);
        $entity->setAluno($aluno);

        $professor = $this->entityManager->getReference(User::class, $data['professor']);
        $entity->setProfessor($professor);


        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }


    public function update($id, $data)
    {
        $entity = $this->entitymanager->getReference($this->entity, $id);
        (new Hydrator\ClassMethods())->hydrate($data, $entity);

        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        $aula = $this->entityManager->getReference(Aula::class, $data['aula']);
        $entity->setAula($aula);

        $aluno = $this->entityManager->getReference(User::class, $data['aluno']);
        $entity->setAluno($aluno);

        $professor = $this->entityManager->getReference(User::class, $data['professor']);
        $entity->setProfessor($professor);

        $this->entitymanager->persist($entity);
        $this->entitymanager->flush();
        return $entity;

    }

}

