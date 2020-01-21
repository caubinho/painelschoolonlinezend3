<?php
namespace User\Service;

use User\Entity\Anexo;
use User\Entity\Aula;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */

class AnexoManager extends AbstractService
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
        $this->entity = Anexo::class;
    }

    public function insert(array $data)
    {
        /* @var \User\Entity\Anexo $entity*/
        $entity = new $this->entity($data);

        $aula = $this->entityManager->getReference(Aula::class, $data['aula']);

        $entity->setAula($aula);

        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

}

