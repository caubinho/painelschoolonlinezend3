<?php
namespace User\Controller;

use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Modulo;
use User\Entity\Turma;
use User\Entity\User;
use User\Form\TurmaForm;
use User\Module;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Plugin\FlashMessenger;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class CronogramaController extends AbstractActionController
{

    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \User\Service\TurmaManager
     */
    private $turmaManger;


    private $entity;
    /**
     * @var
     */
    private $form;
    /**
     * @var \User\Service\CronogramaManager
     */
    private $manager;

    private $route;
    private $controller;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $manager, $form)
    {
        $this->entityManager    = $entityManager;
        $this->route            = "admin/cronograma";
        $this->controller       = "cronograma";
        $this->entity           = Cronograma::class;
        $this->form             = $form;
        $this->manager          = $manager;
    }

    public function indexAction()
    {
        // Get recent posts
        $posts = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['dateCreated'=>'DESC']);



        // Render the view template
        return new ViewModel([
            'dados' => $posts,
        ]);
    }

    public function newAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        //--Turma
        $turma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['id' => $id]);



        // Create the form.
        $form =  $this->form;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $datas = $this->params()->fromPost();

            // Fill form with data.
            $form->setData($datas);

            if ($form->isValid()) {

                // Get validated form data.
                $data = $form->getData();

                $data['turma'] = $id;
                // print_r($data); die;

                // Use post manager service to add new post to database.
                $return = $this->manager->insert($data);

                $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                // Redirect the user to "index" page.
                return $this->redirect()->toRoute($this->route, ['controller' =>$this->controller, 'action' => 'list', 'id' => $data['turma']]);

            }

        }

        // Render the view template.
        return new ViewModel([
            'form'      => $form,
            'id'        => $id,
            'turma'     => $turma,
        ]);
    }

    public function listAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        //--Turma
        $turma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['id' => $id]);

        //--listar cronograma
        $cronograma = $this->entityManager->getRepository(Cronograma::class)
            ->findBy(['turma' => $id], ['inicio' => 'ASC'] );

        //--listar Modulo
        $modulo = $this->entityManager->getRepository(Modulo::class)->findBy(['status' => Modulo::STATUS_ACTIVE]);

        if(empty($modulo)){
            $verificaModulo = null;
        }else{
            $verificaModulo = $modulo;
        }

        //--listar Aula
        $aula = $this->entityManager->getRepository(Aula::class)->findBy(['status' => Aula::STATUS_ACTIVE]);

        if(empty($aula)){
            $verificaAula = null;
        }else{
            $verificaAula = $aula;
        }




        return new ViewModel(array(
            'cron'              => $cronograma,
            'modulos'           => $modulo,
            'verificaModulo'    => $verificaModulo,
            'verificaAula'      => $verificaAula,
            'aulas'             => $aula,
            'id'                => $id,
            'turma'             => $turma,


        ));
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

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


            $this->manager->delete($user);

            $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');

            return $this->redirect()->toRoute('admin/default', ['controller' => 'turma']);


    }

}
