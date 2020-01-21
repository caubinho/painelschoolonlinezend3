<?php

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Polo;

class TrabalhoRepository extends EntityRepository {

    public function fetchPairs() {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Polo::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', '1');

        $retorno = $queryBuilder->getQuery();

        $array = array();

        foreach($retorno as $entity) {
            $array[$entity->getId()] = $entity->getTitulo();
        }

        return $array;

    }

}

