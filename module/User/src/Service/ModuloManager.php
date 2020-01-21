<?php
namespace User\Service;

use User\Entity\Modulo;
use User\Functions\Slug;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class ModuloManager
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entity = Modulo::class;
    }

    /**
     * This method adds a new user.
     */
    public function insert(array $data)
    {
        $entity = new $this->entity($data);

//        $professor = $this->entityManager->getReference(User::class, $data['professor']);
//
//        $entity->setProfessor($professor);

        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);

        // Add the entity to the entity manager.
        $this->entityManager->persist($entity);

        // Apply changes to database.
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * This method updates data of an existing user.
     */
    public function update($db, $data)
    {

        $entity =  (new Hydrator\ClassMethods())->hydrate($data, $db);

//        $professor = $this->entityManager->getReference(User::class, $data['professor']);
//
//        $entity->setProfessor($professor);
        $slug = new Slug();
        $geraSlug = $slug->geraSlug($data['titulo']);

        $entity->setSlug($geraSlug);


        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * Removes post and all associated comments.
     */
    public function delete($post)
    {

        $this->entityManager->remove($post);

        $this->entityManager->flush();
    }

    function urlAmigavel($string)
    {
        $fraseSlug =  preg_replace(
            [ '/([`^~\'"])/', '/([-]{2,}|[-+]+|[\s]+)/', '/(,-)/' ],
            [null, '-', ', ' ],
            iconv( 'UTF-8', 'ASCII//TRANSLIT', $string
            )
        );

        $slug = strtolower($fraseSlug);

        return $slug;
    }

    public function getProdutoStatusAsString($post)
    {
        switch ($post->getStatus()) {
            case Modulo::STATUS_DRAFT: return 'Inativo';
            case Modulo::STATUS_PUBLISHED: return 'Ativo';
        }

        return 'Unknown';
    }



}

