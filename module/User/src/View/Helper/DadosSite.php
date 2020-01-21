<?php
namespace User\View\Helper;

use Doctrine\ORM\EntityManager;
use User\Entity\Setup;
use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays breadcrumbs.
 */
class DadosSite extends AbstractHelper
{
    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * Constructor.
     * @param array $items Array of items (optional).
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;

    }
    /**
     * Sets the items.
     * @param array $items Items.
     */
    public function dados()
    {
            $d = $this->entityManager->getRepository(Setup::class)->findAll();
            foreach ($d as $r => $dados){

                return $dados;
            }

    }



}
