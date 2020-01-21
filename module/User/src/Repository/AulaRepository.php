<?php
namespace User\Repository;


use Doctrine\ORM\EntityRepository;
use User\Entity\Aula;


/**
 * This is the custom repository class for Post entity.
 */
class AulaRepository extends EntityRepository
{

    public function findPublishedAula()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Aula::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.id', 'ASC')
            ->setParameter('1', Aula::STATUS_ACTIVE);

        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\Aula $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getTitulo();
        }

        return $array;

    }

}