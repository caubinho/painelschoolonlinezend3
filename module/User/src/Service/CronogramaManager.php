<?php
namespace User\Service;

use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Modulo;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */

class CronogramaManager extends AbstractService
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
        $this->entity = Cronograma::class;
    }

    public function insert(array $data)
    {

       // print_r($data); die;
        /* @var \User\Entity\Cronograma $entity*/
        $entity = new $this->entity($data);

        $modulo = $this->entityManager->getReference(Modulo::class, $data['modulo']);
        $entity->setModulo($modulo);

        $turma = $this->entityManager->getReference(Turma::class, $data['turma']);
        $entity->setTurma($turma);

        $aula = $this->entityManager->getReference(Aula::class, $data['aula']);
        $entity->setAula($aula);

        $teacher = $this->entityManager->getReference(User::class, $data['professor']);
        $entity->setProfessor($teacher);

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

