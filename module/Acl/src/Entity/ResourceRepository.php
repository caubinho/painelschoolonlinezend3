<?php

namespace Acl\Entity;

use Doctrine\ORM\EntityRepository;

class ResourceRepository extends EntityRepository {

    // Finds all published posts having any tag.
    public function finResource()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Resource::class, 'p')
            ->orderBy('p.nome', 'ASC');

        $retorno =  $queryBuilder->getQuery();

        $array = [];

        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getNome();
        }

        return $array;
    }
        
}
