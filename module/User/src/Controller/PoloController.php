<?php
namespace User\Controller;

use User\Entity\Polo;
use User\Entity\Turma;
use User\Form\PoloForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;



/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class PoloController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var
     */
    private $poloManager;
    /**
     * @var \User\Form\PoloForm
     */
    private $form;

    private $entity;



    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $poloManager )
    {
        $this->entityManager    = $entityManager;
        $this->poloManager      = $poloManager;
        $this->route            = 'admin/default';
        $this->controller       = 'polo';
        $this->entity           = Polo::class;

        $this->form = new PoloForm();
    }
    /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function indexAction()
    {
        // Get recent posts
        $posts = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['estado'=>'ASC']);
        

        // Render the view template
        return new ViewModel([
            'dados' => $posts,
            'postManager' => $this->poloManager,
        ]);
    }

    public function newAction()
    {
        // Create the form.
        $form =  $this->form;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $data = $this->params()->fromPost();

            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {

                // Get validated form data.
                //$data = $form->getData();


                // Use post manager service to add new post to database.
                $return = $this->poloManager->insert($this->getRequest()->getPost()->toArray());

                $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                // Redirect the user to "index" page.
                return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit' , 'id' => $return->getId()]);


            }
        }

        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $dataBase = $this->entityManager->getRepository(Polo::class)
            ->findOneById($id);

        if ($dataBase == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = $this->form;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the user.
                $dados = $this->poloManager->update($dataBase, $data);

                $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                return  $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit' , 'id' => $dados->getId()]);

            }
        } else {




            $form->setData(array(
                'id'            => $dataBase->getId(),
                'cidade'        => $dataBase->getCidade(),
                'estado'        => $dataBase->getEstado(),
                'status'        => $dataBase->getStatus(),

            ));
        }


        return new ViewModel(array(
            'dataBase'  => $dataBase,
            'form'      => $form,

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



        if($id != 1){

            $this->poloManager->delete($user);

            $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');

            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);


        }else {

            $this->flashMessenger()->addErrorMessage('Este polo não pode ser deletado!');

            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

        }

    }

    public function viewAction()
    {
        // Get recent posts
        $posts = $this->entityManager->getRepository(Turma::class)
            ->findBy(['status' => Turma::STATUS_ACTIVE], ['titulo'=>'ASC']);


        // Render the view template
        return new ViewModel([
            'dados' => $posts,
            'postManager' => $this->poloManager,
        ]);

    }
    
}
