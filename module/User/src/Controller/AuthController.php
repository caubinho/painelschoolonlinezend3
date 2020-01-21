<?php

namespace User\Controller;

use User\Entity\User;
use User\Form\LoginForm;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authService;
    /**
     * @var
     */
    private $entityManager;

    public function __construct(AuthenticationServiceInterface $authService, $entityManager)
    {
        $this->authService = $authService;
        $this->entityManager = $entityManager;
    }

    public function loginAction()
    {
        if ($this->authService->hasIdentity()) {
            return $this->redirect()->toRoute('admin/default');
        }

        $form = new LoginForm();
        $messageError = null;
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $formData = $form->getData();
                /** @var CallbackCheckAdapter $authAdapter */


                $authAdapter = $this->authService->getAdapter();
                $authAdapter->setIdentity($formData['email']);
                $authAdapter->setCredential($formData['password']);

                $result = $this->authService->authenticate();
                if ($result->isValid()) {

                    $status =  $authAdapter->authenticate()->getIdentity();


                    if( $status->getStatus() === '2'){


                        $this->authService->clearIdentity();

                        $this->flashMessenger()->addErrorMessage('Usuário Inativo!. Entre em contato com a SPP');

                        return $this->redirect()->toRoute('login');



                    }else{
                        return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);


                    }

                } else {
                    
                    $messageError = "Login Inválido!";
                }


            }
        }
        return new ViewModel([
            'form' => $form,
            'messageError' => $messageError,
             $this->layout('layout/login')
        ]);
    }

    public function logoutAction()
    {
        $this->authService->clearIdentity();
        return $this->redirect()->toRoute('login');
    }
}
