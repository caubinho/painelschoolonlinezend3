<?php
namespace User\Controller;

use User\Entity\Modulo;
use User\Entity\Turma;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Plugin\FlashMessenger;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class ModuloController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var
     */
    private $moduloForm;
    /**
     * @var
     */
    private $moduloManager;

    private $route;
    private $controller;


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $moduloManager, $moduloForm)
    {
        $this->entityManager    = $entityManager;
        $this->moduloForm       = $moduloForm;
        $this->moduloManager    = $moduloManager;
        $this->route            = "admin/default";
        $this->controller       = "modulos";
    }
    /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function indexAction()
    {
        // Get recent posts
        $posts = $this->entityManager->getRepository(Modulo::class)
            ->findBy([], ['titulo'=>'ASC']);



        // Render the view template
        return new ViewModel([
            'dados' => $posts,
            'postManager' => $this->moduloManager,
        ]);
    }


    /**
     * This action displays the "New Post" page. The page contains a form allowing
     * to enter post title, content and tags. When the user clicks the Submit button,
     * a new Post entity will be created.
     */
    public function newAction()
    {
        // Create the form.
        $form =  $this->moduloForm;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

                // Get POST data.
                $datas = $this->params()->fromPost();

                // Fill form with data.
                $form->setData($datas);

                if ($form->isValid()) {

                    // Get validated form data.
                    $data = $form->getData();

                    // print_r($data); die;

                    // Use post manager service to add new post to database.
                    $return = $this->moduloManager->insert($this->getRequest()->getPost()->toArray());

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                    // Redirect the user to "index" page.
                    return $this->redirect()->toRoute($this->route, ['controller' =>$this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }

        }

        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        $dataBase = $this->entityManager->getRepository(Modulo::class)
            ->find($id);

        if ($dataBase == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

       foreach($dataBase as $a){

           echo $a->getTitulo(); die;
       }

        // Create user form
        $form = $this->moduloForm;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);


                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    // Update the user.
                    $return = $this->moduloManager->update($dataBase, $this->getRequest()->getPost()->toArray());

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route,
                        ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }

        } else {
            $form->setData([
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'texto'         => $dataBase->getTexto(),
                'status'        => $dataBase->getStatus(),
            ]);
        }

        return new ViewModel(array(
            'dataBase' => $dataBase,
            'form' => $form
        ));
    }


    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(Modulo::class)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

            $this->moduloManager->delete($user);
            $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);


    }

}
