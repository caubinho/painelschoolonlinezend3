<?php

namespace Acl\Entity;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository {

    // Finds all published posts having any tag.
    public function findRole()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Role::class, 'p')
            ->orderBy('p.nome', 'DESC');

        $retorno =  $queryBuilder->getQuery();

        $array = [];

        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getNome();
        }

        return $array;
    }

}
