<?php
namespace User\View\Helper;

use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\View\Helper\AbstractHelper;

/**
 * This view helper class displays breadcrumbs.
 */
class UserIdentity extends AbstractHelper
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Auth service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;



    /**
     * Constructor.
     * @param array $items Array of items (optional).
     */
    public function __construct($entityManager, $authService )
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
       
    }
    /**
     * Sets the items.
     * @param array $items Items.
     */
    public function getDados()
    {        
        $identity = $this->authService->getIdentity();

        if($identity!= null){


            $d = $this->entityManager->getRepository(User::class)->findBy(['email' => $identity->getEmail()]);

            foreach ($d as $r => $dados){

                 return $dados;
            }



        }else{

            return 'Não esta logado!';
        }

    }



}
