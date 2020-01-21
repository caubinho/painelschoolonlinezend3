<?php
namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\Post;
use User\Entity\User;

/**
 * This is the custom repository class for Post entity.
 */
class UserRepository extends EntityRepository
{

    // busca alunos ativos para cadastro de boletos
    public function findAlunos()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.status = ?1')
            ->andWhere('p.role = ?2')
            ->orderBy('p.fullName', 'ASC')
            ->setParameter('1', User::STATUS_ACTIVE)
            ->setParameter('2', '2');

        $retorno = $queryBuilder->getQuery();

        $array = [];


        $array[] = 'Selecione';

        /** @var \User\Entity\User $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getFullName();
        }

        return $array;

    }

    public function findProfessores()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.status = ?1')
            ->andWhere('p.isteacher = ?2')
            ->orderBy('p.fullName', 'ASC')
            ->setParameter('1', User::STATUS_ACTIVE)
            ->setParameter('2', '1');


        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\User $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getFullName();
        }

        return $array;

    }

    public function findCoordenador()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.status = ?1')
            ->andWhere('p.role = ?2')
            ->orWhere('p.role = ?3')
            ->orderBy('p.fullName', 'ASC')
            ->setParameter('1', User::STATUS_ACTIVE)
            ->setParameter('2', '6')
            ->setParameter('3', '4');

        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\User $entity */
        foreach($retorno->getResult() as $entity) {

            $array[$entity->getId()] = $entity->getFullName();
        }

        return $array;

    }

    public function findPesquisar($pesquisa)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->where('p.status = ?1',  'like = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_ACTIVE)
            ->setParameter('2', '%'.$pesquisa.'%');

        return $queryBuilder->getQuery();


    }

    public function findPublishedCategory($Categoria)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->where('p.status = ?1')
            ->andWhere('p.category = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_ACTIVE)
            ->setParameter('2', $Categoria);

        return $queryBuilder->getQuery();
    }

    public function findPublishedPosts()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_ACTIVE);

        return $queryBuilder->getQuery();
    }

    public function findPostsHavingAnyTag()
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_ACTIVE);

        $posts = $queryBuilder->getQuery()->getResult();

        return $posts;
    }

    public function findPostsByTag($tagName)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->andWhere('t.name = ?2')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_ACTIVE)
            ->setParameter('2', $tagName);

        return $queryBuilder->getQuery();
    }

    public function findUserByCpf($cpf)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.cpf = ?1')
            ->setParameter('1',$cpf);

        $retorno = $queryBuilder->getQuery();

        $array = [];

        /** @var \User\Entity\User $entity */
        foreach($retorno->getResult() as $entity) {

            $entity->getCpf();
        }

        return $array;
    }



}