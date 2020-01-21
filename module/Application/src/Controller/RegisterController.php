<?php
namespace Application\Controller;

use User\Form\RegisterForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class RegisterController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var
     */
    private $userManager;

    private $form;
    private $config;

    private $renderer;
    private $email;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $userManager, $form, $renderer, $email, $config)
    {
       $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->form = $form;

        $this->renderer = $renderer;
        $this->email = $email;
        $this->config = $config;
    }


    public function indexAction()
    {
        return new ViewModel();
    }


    public function activateAction() 
    {

        $activationKey =   $id = $this->params()->fromRoute('id', -1);


        $result = $this->userManager->activate($activationKey);

        switch ($result) {
            case 'true':

                $mensagem  = "<p>Bem-Vindo ao Ambiente Virtual de Aprendizagem da SPP</p>";
                $mensagem .= "<p>Seu cadastro foi ativado com sucesso! Clique em Acessar e faça o login no sistema!</p>";
                $mensagem .= "<a href=\"/login\" class=\"btn btn-primary btn-xl page-scroll\">Acessar</a>";

                return new ViewModel(array('msg' => $mensagem));
                break;
            default:

                $mensagem  = "<p>Necessário uma chave de ativação válida!</p>";

                return new ViewModel(array('msg' => $mensagem));

                break;

        }

    }

    public function registerAction()
    {

        $form = $this->form;

        if ($this->getRequest()->isPost()) {

            $request = $this->getRequest();

            $data = $request->getPost()->toArray();

            $form->setData($data);

            if ($form->isValid())
            {

                $data = $form->getData();


                $data['polo'] = '1';

                /** @var \User\Entity\User $return */
                $return = $this->userManager->registerUser($data);

                if(!empty($return)) {

                    $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
                    $link = 'http://' . $httpHost . '/register/activate/' . $return->getActivationKey();

                    $body = "Olá, ".$return->getFullName().",<br>\n<br>";
                    $body .= "Parabéns! Você foi cadastrado(a) em nosso sistema, para confirmar acesse o link abaixo ou clique no botão <br>\n<br>";
                    $body .= "$link\n<br>";

                   
                    /** @var \User\Mail\Mail $mail */
                    $mail = $this->email;

                    $mail->sendEmail($return->getFullName(), 'Confirmação de Cadastro AVA-SPP', $return->getEmail(), $link, $body, 'add-user', null, $this->config );

                    $this->flashMessenger()->addSuccessMessage('Parabéns seu cadastro foi um sucesso! Falta pouco, consulte o e-mail de cadastro para ativar!');


                    return $this->redirect()->toRoute('register', ['action' => 'register']);

                }else{

                    return $this->redirect()->toRoute('register', ['action' => 'register']);
                }


            }


//            else{

//                print_r($form->getMessages()); die;
//            }

        }


        return new ViewModel([

            'form' => $form,

            $this->layout('layout/login')

        ]);
    }

}

