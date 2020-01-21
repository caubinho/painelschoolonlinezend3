<?php
namespace User\Service;

use User\Entity\Alunos;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */

class AlunosManager extends AbstractService
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
        $this->entity = Alunos::class;
    }

    public function insert(array $data)
    {

       // print_r($data); die;
        /* @var \User\Entity\Alunos $entity*/
        $entity = new $this->entity($data);


        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        $teacher = $this->entityManager->getReference(User::class, $data['usuario']);
        $entity->setUsuario($teacher);

        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    public function delete($post)
    {

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }

}

