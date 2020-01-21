<?php

namespace User\Service;

use Zend\Hydrator;

abstract class AbstractService
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entitymanager;
    protected $entity;


    public function __construct($entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }

    public function insert(array $data)
    {
        $entity = new $this->entity($data);

        $this->entitymanager->persist($entity);
        $this->entitymanager->flush();
        return $entity;
    }

    public function update($id, $data)
    {
        $entity = $this->entitymanager->getReference($this->entity, $id);
        (new Hydrator\ClassMethods())->hydrate($data, $entity);

        $this->entitymanager->persist($entity);
        $this->entitymanager->flush();
        return $entity;

    }

    public function delete($id)
    {
        $entity = $this->entitymanager->getReference($this->entity, $id);

        if($entity)
        {
            $this->entitymanager->remove($entity);
            $this->entitymanager->flush();
            return $id;
        }

    }

   
} 