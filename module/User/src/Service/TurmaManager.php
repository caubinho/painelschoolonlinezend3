<?php
namespace User\Service;

use User\Entity\Polo;
use User\Entity\Turma;
use User\Entity\User;
use User\Functions\Slug;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class TurmaManager
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
        $this->entityManager = $entityManager;
        $this->entity = Turma::class;
    }

    public function insert(array $data)
    {
        /** @var \User\Entity\Turma $entity */
        $entity = new $this->entity($data);

        $coordenador = $this->entityManager->getReference(User::class, $data['coordenador']);
        $entity->setCoordenador($coordenador);

        $polo = $this->entityManager->getReference(Polo::class, $data['polo']);
        $entity->setPolo($polo);

        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);
        $entity->setSlug($geraSlug);

        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    public function update($db, $data)
    {
        /** @var \User\Entity\Turma $entity */
        $entity =  (new Hydrator\ClassMethods())->hydrate($data, $db);

        $coordenador = $this->entityManager->getReference(User::class, $data['coordenador']);
        $entity->setCoordenador($coordenador);

        $polo = $this->entityManager->getReference(Polo::class, $data['polo']);
        $entity->setPolo($polo);

        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);
        $entity->setSlug($geraSlug);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    public function delete($post)
    {

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }

}

