<?php
namespace User\Service;

use User\Entity\Boleto;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class BoletoManager
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $entity;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entity = Boleto::class;
    }

    /**
     * This method adds a new user.
     */
    public function insert(array $data)
    {

        $entity = new $this->entity($data);

        $aluno = $this->entityManager->getReference(User::class, $data['aluno']);

        $entity->setAluno($aluno);


        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * This method updates data of an existing user.
     */
    public function update($db, $data)
    {

        $entity =  (new Hydrator\ClassMethods())->hydrate($data, $db);
        $aluno = $this->entityManager->getReference(User::class, $data['aluno']);
        $entity->setAluno($aluno);

        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * Removes post and all associated comments.
     */
    public function delete($post)
    {

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }

    public function status($post, $data)
    {

        $repo = $this->entityManager->getRepository($this->entity);

        $boleto = $repo->find($post);

        $boleto->setStatus($data['status']);

        // Apply changes to database.
        $this->entityManager->flush();

        return true;

    }



}

