<?php
namespace Acl\Service;

use Acl\Entity\Role;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class RoleManager
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
        $this->entity = Role::class;
    }

    /**
     * This method adds a new user.
     */
    public function insert(array $data)
    {

        $entity = new $this->entity($data);

        if($data['parent'])
        {
            $parent = $this->entityManager->getReference($this->entity, $data['parent']);
            $entity->setParent($parent);
        }
        else
            $entity->setParent(null);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;

    }

    /**
     * This method updates data of an existing user.
     */
    public function update($db, $data)
    {

        $entity = (new Hydrator\ClassMethods())->hydrate($data, $db);

        if($data['parent'])
        {
            $parent = $this->entityManager->getReference($this->entity, $data['parent']);
            $entity->setParent($parent);
        }
        else {
            $entity->setParent(null);
        }
        if($data['isAdmin'] == '1'){

            $entity->setParent(null);

        }else {
            $parent = $this->entityManager->getReference($this->entity, $data['parent']);
            $entity->setParent($parent);
        }
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

