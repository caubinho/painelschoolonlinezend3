<?php
namespace User\Service;

use User\Entity\Video;
use User\Functions\Slug;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class VideoManager
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
        $this->entity = Video::class;
    }

    /**
     * This method adds a new user.
     */
    public function insert(array $data)
    {
        $entity = new $this->entity($data);



        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);


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

        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);

        //print_r($entity); die;

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



}

