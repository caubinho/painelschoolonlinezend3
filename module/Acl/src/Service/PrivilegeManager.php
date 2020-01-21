<?php

namespace Acl\Service;

use Acl\Entity\Privilege;
use Acl\Entity\Resource;
use Acl\Entity\Role;
use Zend\Hydrator;

class PrivilegeManager
{
    /**
     * @var
     */
    private $entityManager;

    public function __construct($entityManager) {

        $this->entity = Privilege::class;
        $this->entityManager = $entityManager;
    }

    public function insert(array $data)
    {
        $entity = new $this->entity($data);

        $role = $this->entityManager->getReference(Role::class,$data['role']);
        $entity->setRole($role); // Injetando entidade carregada

        $resource = $this->entityManager->getReference(Resource::class,$data['resource']);
        $entity->setResource($resource); // Injetando entidade carregada

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $entity;
    }

    public function update($db, $data)
    {
        $entity = $this->entityManager->getReference($this->entity, $db);
        (new Hydrator\ClassMethods())->hydrate($data, $entity);

        $role = $this->entityManager->getReference(Role::class,$data['role']);
        $entity->setRole($role); // Injetando entidade carregada

        $resource = $this->entityManager->getReference(Resource::class,$data['resource']);
        $entity->setResource($resource); // Injetando entidade carregada

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
