<?php
namespace User\Service;

use User\Entity\Polo;
use User\Entity\User;
use User\Functions\Slug;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class PoloManager
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
        $this->entity = Polo::class;
    }

    /**
     * This method adds a new user.
     */
    public function insert(array $post)
    {
        /* @var \User\Entity\Polo $entity*/
        $entity = new $this->entity($post);


        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * This method updates data of an existing user.
     */
    public function update($post, $data)
    {

        $post->setCidade($data['cidade']);
        $post->setStatus($data['status']);
        $post->setEstado($data['estado']);

        $this->entityManager->flush();

        return $post;
    }

    /**
     * Removes post and all associated comments.
     */
    public function delete($post)
    {

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }


    public function getProdutoStatusAsString($post)
    {
        switch ($post->getStatus()) {
            case Polo::STATUS_DRAFT: return 'Inativo';
            case Polo::STATUS_PUBLISHED: return 'Ativo';
        }

        return 'Unknown';
    }



}

