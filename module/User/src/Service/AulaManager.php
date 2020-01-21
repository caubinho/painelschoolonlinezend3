<?php
namespace User\Service;

use User\Entity\Aula;
use User\Functions\Slug;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class AulaManager extends AbstractService
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
        $this->entity = Aula::class;
    }

    public function insert(array $data)
    {
        $entity = new $this->entity($data);

        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);

        $this->entitymanager->persist($entity);
        $this->entitymanager->flush();
        return $entity;
    }

    public function update($id, $data)
    {
        $entity = $this->entitymanager->getReference($this->entity, $id);
        (new Hydrator\ClassMethods())->hydrate($data, $entity);


        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);

        $this->entitymanager->persist($entity);
        $this->entitymanager->flush();
        return $entity;

    }


}

