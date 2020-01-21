<?php
namespace User\Service;

use User\Entity\Link;
use Zend\Hydrator;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class LinkManager extends AbstractService
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
        parent::__construct($entityManager);

        $this->entityManager = $entityManager;
        $this->entity = Link::class;
    }

}

