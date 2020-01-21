<?php
namespace Acl\Controller;

use Acl\Entity\Resource;
use Acl\Form\ResourceForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class ResourcesController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var
     */
    private $resourceManager;


    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $resourceManager)
    {
        $this->entityManager    = $entityManager;
        $this->resourceManager  = $resourceManager;
        $this->route            = "acl/default";
        $this->controller       = "resources";
        $this->entity           = Resource::class;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * Home page.
     */
    public function indexAction() {

        $list = $this->entityManager
            ->getRepository(Resource::class)
            ->findAll();


        return new ViewModel(array('data'=>$list));

    }

    public function newAction()
    {
        // Create the form.
        $form =  new ResourceForm();

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            // Get POST data.
            $data = $this->params()->fromPost();

            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {

                // Use post manager service to add new post to database.
                $return = $this->resourceManager->insert($this->getRequest()->getPost()->toArray());

                $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                // Redirect the user to "index" page.
                return $this->redirect()->toRoute($this->route , ['controller' => $this->controller, 'action' => 'edit' , 'id' => $return->getId()]);


            }
        }

        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function editAction(){

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

        // Create user form
        $form =  new ResourceForm();



        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();


            $form->setData($data);



            // Validate form
            if ($form->isValid()) {

                // Update the user.
                $this->resourceManager->update($user, $data);

                $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                // Redirect to "view" page
                return $this->redirect()->toRoute($this->route, [ 'controller' => $this->controller,
                    'action' => 'edit', 'id' => $user->getId()]);
            }

        } else {
            $form->setData(array(
                'id'                => $user->getId(),
                'nome'              => $user->getNome(),
            ));
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form,

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


        $this->resourceManager->delete($user);
        $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');
        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);


    }


}

