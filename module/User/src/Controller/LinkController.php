<?php
namespace User\Controller;

use User\Entity\Anexo;
use User\Entity\Link;
use User\Form\LinkForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class LinkController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \User\Service\LinkManager
     *
     */
    private $service;
    private $route;
    private $controller;
    private $entity;
    private $form;

    public function __construct($entityManager, $service)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Link::class;
        $this->controller       = 'link';
        $this->route            = 'admin/default';
        $this->form             = new LinkForm();
    }

    public function indexAction()
    {

        $videos = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'dados' => $videos
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
                $return = $this->service->insert($this->getRequest()->getPost()->toArray());

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

        $dataBase = $this->entityManager->getRepository($this->entity)
            ->find($id);

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
                $dados = $this->service->update($dataBase->getId(), $data);

                $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                return  $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit' , 'id' => $dados->getId()]);

            }
        }else {

            $form->setData(array(
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'url'           => $dataBase->getUrl(),
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

        $anexo = $this->checaAnexo('Link', $id);

        if(empty($anexo)) {


        $this->service->delete($user->getId());

        $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');

        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

        }else{

            $this->flashMessenger()->addErrorMessage('Não foi possível excluir existe(m) aulas com este Link anexado!');
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

        }

    }

    public function checaAnexo($controller, $id) {

        $user = $this->entityManager->getRepository(Anexo::class)
            ->findBy(['controller' => $controller, 'anexo' => $id]);

        $contar = count($user);

        return $contar;

    }

}
