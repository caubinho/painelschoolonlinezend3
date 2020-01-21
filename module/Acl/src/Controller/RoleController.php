<?php
namespace Acl\Controller;

use Acl\Entity\Role;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class RoleController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var
     */
    private $roleManager;
    /**
     * @var
     */
    private $roleForm;


    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $roleManager, $roleForm)
    {
        $this->entityManager = $entityManager;

        $this->roleManager = $roleManager;
        $this->roleForm = $roleForm;
        $this->entity    = Role::class;
        $this->route    = 'acl/default';
        $this->controller = "roles";
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * Home page.
     */
    public function indexAction() {

        $list = $this->entityManager
            ->getRepository(Role::class)
            ->findAll();


        return new ViewModel(array('data'=>$list, $this->layout('layout/admin')));

    }

    public function newAction()
    {
        $form = $this->roleForm;

        if ($this->getRequest()->isPost()) {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);

                // Validate form
                if ($form->isValid()) {
                    
                    $return = $this->roleManager->insert($data);
                    
                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                    // Redirect to "edit" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }

            }//end image verify

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

        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form =  $this->roleForm;



        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();


                $form->setData($data);



                // Validate form
                if ($form->isValid()) {

                    // Update the user.
                    $this->roleManager->update($user, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, [ 'controller' => $this->controller,
                        'action' => 'edit', 'id' => $user->getId()]);
                }

        } else {
            $form->setData(array(
                'id'                => $user->getId(),
                'nome'              => $user->getNome(),
                'parent'            => $user->getParent(),
                'idAdmin'           => $user->getIsAdmin(),
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


            $this->roleManager->delete($user);
            $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);


    }

}

