<?php
namespace User\Controller;

use User\Entity\Alunos;
use User\Entity\Anexo;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class AlunosController extends AbstractActionController
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
        $this->entity           = Alunos::class;
        $this->controller       = 'alunos';
        $this->route            = 'admin/default';

    }


    public function indexAction()
    {

        $anexo = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['id'=>'ASC']);

        $a = [];

        /** @var \User\Entity\Alunos $user */
        foreach ($anexo as $user) {
            $a[$user->getId()]['usuario'] = $user->getUsuario()->getFullName();
            $a[$user->getId()]['turma'] = $user->getTurma();
        }

        $result = new JsonModel(array('data' =>$a));

        return $result;

    }

    public function listAction(){

        $idAluno = $id = (int)$this->params()->fromQuery('usuario', -1);

        $repoAluno = $this->entityManager->getRepository($this->entity)
            ->findBy(['usuario' => $idAluno ], ['id'=>'ASC']);

        $a = [];

        /** @var \User\Entity\Alunos $user */
        foreach ($repoAluno as $user) {
            $a[$user->getId()]['id'] = $user->getTurma()->getId();
            $a[$user->getId()]['turma'] = $user->getTurma()->getTitulo();
        }

        $result = new JsonModel(array('data' =>$a));

        return $result;


    }


    public function newAction()
    {
       $data = [
           "usuario"    => $_POST['usuario'],
           "turma"      => $_POST['turma'],
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

        $turma = (int)$this->params()->fromRoute('turma', -1);


        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


                $this->entityManager->remove($user);
                $this->entityManager->flush();

                $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');
                return $this->redirect()->toRoute($this->route, ['controller' => 'turma', 'action' => 'alunos' , 'id' => $turma]);


    }
    
}
