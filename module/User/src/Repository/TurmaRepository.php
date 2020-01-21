<?php

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Turma;

class TurmaRepository extends EntityRepository {

    public function fetchPairs() {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->getRepository(Turma::class);

        $retorno = $queryBuilder->findBy(['status' => 1],['dateCreated' => 'ASC']);

        $array = array();

        foreach($retorno as $entity) {
            $array[$entity->getId()] = $entity->getTitulo();
        }

        return $array;

    }

    public function findTurmas()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->getRepository(Turma::class);

        $retorno = $queryBuilder->findBy(['status' => 1],['titulo' => 'ASC']);


        $array = [];

        /** @var \User\Entity\Turma $entity */
        foreach($retorno as $entity) {

            $array[$entity->getId()] = $entity->getTitulo();
        }

        return $array;

    }

    public function findPolos()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Turma::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.cidade', 'ASC')
            ->addGroupBy('p.cidade')
            ->setParameter('1', Turma::STATUS_ACTIVE);

        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\Turma $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getCidade()] = $entity->getCidade();
        }

        return $array;

    }




}

