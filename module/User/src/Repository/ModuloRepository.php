<?php
namespace User\Repository;

use Acl\Module;
use Doctrine\ORM\EntityRepository;
use User\Entity\Modulo;
use User\Entity\Post;
use User\Entity\User;

/**
 * This is the custom repository class for Post entity.
 */
class ModuloRepository extends EntityRepository
{

    public function findPublishedModulos()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Modulo::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.id', 'ASC')
            ->setParameter('1', Modulo::STATUS_ACTIVE);

        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\Category $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getTitulo();
        }

        return $array;

    }

}