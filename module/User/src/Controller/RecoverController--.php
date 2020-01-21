<?php
namespace User\Controller;


use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;




/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's password).
 */
class RecoverControllerR extends AbstractActionController
{
    /**
     * Entity manager.
     */
    private $entityManager;

    /**
     * User manager.
     */
    private $userManager;

    /**
     * @var
     */
    private $config;
    /**
     * @var
     */
    private $view;


    /**
     * Constructor.
     * @param $entityManager
     * @param $userManager
     */
    public function __construct($entityManager, $userManager, $config, $view)
    {
        $this->entityManager    = $entityManager;
        $this->userManager      = $userManager;
        $this->entity           = User::class;
        $this->controller       = 'recover';
        $this->route            = 'recover';

        $this->config = $config;
        $this->view = $view;
    }
    

    /**
     * This action displays the "Reset Password" page.
     */
    public function indexAction()
    {
        // Create form
        $form = new PasswordResetForm();

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Look for the user with such email.
                $user = $this->entityManager->getRepository(User::class)
                    ->findOneByEmail($data['email']);

                if ($user!=null) {
                    // Generate a new password for user and send an E-mail
                    // notification about that.


                    $this->userManager->generatePasswordResetToken($data);

                    return $this->redirect()->toRoute('recover', ['action' => 'message', 'id' => 'sent']);


                } else {
                    return $this->redirect()->toRoute('recover',['action'=>'message', 'id'=>'invalid-email']);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            $this->layout('layout/login')
        ]);
    }

    /**
     * This action displays an informational message page.
     * For example "Your password has been resetted" and so on.
     */
    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            return $this->redirect()->toRoute('recover');
        }

        return new ViewModel([
            'id' => $id,
            $this->layout('layout/login')
        ]);
    }

    /**
     * This action displays the "Reset Password" page.
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }

        if($token===null ||
            !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('users',
                ['action'=>'message', 'id'=>'failed']);
        }

        // Create form
        $form = new PasswordChangeForm('reset');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                $data = $form->getData();

                // Set new password for the user.
                if ($this->userManager->setNewPasswordByToken($token, $data['new_password'])) {

                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'set']);
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users',
                        ['action'=>'message', 'id'=>'failed']);
                }
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }


    public function senhaAction(){

        $id = (int)$this->params()->fromRoute('id', -1);

        $return = $this->generatePassword($id);

        if($return) {
            return new JsonModel([
                'data' =>  $return,
            ]);
        }

    }

    public function generatePassword($l = 10, $c = 0, $n = 0, $s = 0) {
        // get count of all required minimum special chars
        $count = $c + $n + $s;
        $out = '';
        // sanitize inputs; should be self-explanatory
        if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
            trigger_error('Argument(s) not an integer', E_USER_WARNING);
            return false;
        }
        elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
            trigger_error('Argument(s) out of range', E_USER_WARNING);
            return false;
        }
        elseif($c > $l) {
            trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
            return false;
        }
        elseif($n > $l) {
            trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
            return false;
        }
        elseif($s > $l) {
            trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
            return false;
        }
        elseif($count > $l) {
            trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
            return false;
        }

        // all inputs clean, proceed to build password

        // change these strings if you want to include or exclude possible password characters
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $caps = strtoupper($chars);
        $nums = "0123456789";
        $syms = "!@#$%^&*()-+?";

        // build the base password of all lower-case letters
        for($i = 0; $i < $l; $i++) {
            $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        // create arrays if special character(s) required
        if($count) {
            // split base password to array; create special chars array
            $tmp1 = str_split($out);
            $tmp2 = array();

            // add required special character(s) to second array
            for($i = 0; $i < $c; $i++) {
                array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
            }
            for($i = 0; $i < $n; $i++) {
                array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
            }
            for($i = 0; $i < $s; $i++) {
                array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
            }

            // hack off a chunk of the base password array that's as big as the special chars array
            $tmp1 = array_slice($tmp1, 0, $l - $count);
            // merge special character(s) array with base password array
            $tmp1 = array_merge($tmp1, $tmp2);
            // mix the characters up
            shuffle($tmp1);
            // convert to string for output
            $out = implode('', $tmp1);
        }

        return $out;

    }

    /**
     * Checks whether an active user with given email address already exists in the database.
     */

    public function email($data)
    {

        $nome = $data->getFullName();
        $ativacao = $data->getActivationKey();
        $destino = $data->getEmail();

        //$data->getEmail()

        //**busca dados globais de envio**/
        $emailConfig = $this->config['mail'];

        //**seta dados
        $dataEmail = array('nome'=>$nome,'activationKey'=>$ativacao);
        $page = 'add-user';

        /** config de envio */
        $quemEnvia      = "contato@spp.psc.org";
        $destinatario   = $destino;
        $assunto        = 'Confirmação de Cadastro AVA-SPP';
        $corpoMSG       = $this->renderView($page, $dataEmail);

        $usernameServidor   =   $emailConfig['connection_config']['username'];
        $passServidor       =   $emailConfig['connection_config']['password'];
        $portServidor       =   $emailConfig['port'];
        $hostServidor       =   $emailConfig['host'];


// Inclui o arquivo class.phpmailer.php localizado na pasta class
        require_once("public/email/class/class.phpmailer.php");

// Inicia a classe PHPMailer
        $mail = new \PHPMailer(true);
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('br', 'public/email/language/');

// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->IsSMTP(); // Define que a mensagem será SMTP

        try {
            $mail->Host     =  $hostServidor ; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudomínio.com.br)
            $mail->SMTPAuth =   true;  // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
            $mail->Port     =  $portServidor; //  Usar 587 porta SMTP
            $mail->Username =  $usernameServidor; // Usuário do servidor SMTP (endereço de email)
            $mail->Password =  $passServidor ; // Senha do servidor SMTP (senha do email usado)

            //Define o remetente
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->SetFrom($quemEnvia, 'AVA-SPP'); //Seu e-mail
            //$mail->AddReplyTo($quemEnvia, 'AVA-SPP'); //Seu e-mail
            $mail->Subject =  $assunto;//Assunto do e-mail


            //Define os destinatário(s)
            //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->AddAddress($destinatario, $nome);

            //Campos abaixo são opcionais
            //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            //$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
            //$mail->AddBCC('destinatario_oculto@dominio.com.br', 'Destinatario2`'); // Cópia Oculta
            //$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo


            //Define o corpo do email
            $mail->MsgHTML($corpoMSG);

            ////Caso queira colocar o conteudo de um arquivo utilize o método abaixo ao invés da mensagem no corpo do e-mail.
            //$mail->MsgHTML(file_get_contents('arquivo.html'));

            $mail->Send();

            $this->flashMessenger()->addSuccessMessage('Cadastro e email enviado com sucesso!');

            //caso apresente algum erro é apresentado abaixo com essa exceção.
        }catch (\phpmailerException $e) {

            echo $e->errorMessage();

        }

    }

    public function renderView($page, array $data)
    {
        $model = new ViewModel;
        $model->setTemplate("mailer/{$page}.phtml");
        $model->setOption('has_parent',true);
        $model->setVariables($data);

        return $this->view->render($model);
    }


}


