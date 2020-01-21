<?php

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Polo;

class PoloRepository extends EntityRepository {

    public function findCidade()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Polo::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.cidade', 'ASC')
            ->setParameter('1', Polo::STATUS_ACTIVE);


        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\Polo $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getCidade();
        }

        return $array;

    }

}

