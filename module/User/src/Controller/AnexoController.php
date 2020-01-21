<?php
namespace User\Controller;

use User\Entity\Anexo;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class AnexoController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \User\Service\AnexoManager
     *
     */
    private $service;

    private $entity;

    private $controller;

    private $route;


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $service)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Anexo::class;
        $this->controller       = 'anexo';
        $this->route            = 'admin/default';

    }


    public function indexAction()
    {

        $anexo = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['id'=>'ASC']);

        $a = [];

        /** @var \User\Entity\Anexo $user */
        foreach ($anexo as $user) {
            $a[$user->getId()]['aula'] = $user->getAula()->getTitulo();
            $a[$user->getId()]['controller'] = $user->getController();
            $a[$user->getId()]['anexo'] = $user->getAnexo();
        }

        $result = new JsonModel(array('data' =>$a));

        return $result;

    }


    public function newAction()
    {
       $data = [
           "controller" => $_POST['controller'],
           "aula"       => $_POST['aula'],
           "anexo"      => $_POST['anexo'],
       ];

        $return = $this->service->insert($data);

        if($return)
        {
            return new JsonModel(array('data'=>array('success'=>true)));
        }
        else
            return new JsonModel(array('data'=>array('success'=>false)));
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {

            return new JsonModel(array('data'=>array('success'=>'Não existe Post!')));
        }

        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);

        $res = $this->service->delete($user->getId());

        if($res)
        {
            return new JsonModel(array('data'=>array('success'=>true)));
        }else {
            return new JsonModel(array('data' => array('success' => false)));
        }

    }
    
}
