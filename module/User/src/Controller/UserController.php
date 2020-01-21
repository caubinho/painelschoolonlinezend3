<?php
namespace User\Controller;

use User\Entity\Alunos;
use User\Entity\Boleto;

use User\Entity\Cronograma;
use User\Entity\Polo;
use User\Entity\Setup;
use User\Entity\Turma;
use User\Mail\Mail;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Entity\User;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;
use Zend\View\Model\JsonModel;
use Zend\View\Renderer\RendererInterface;




/**
 * This controller is responsible for user management (adding, editing, 
 * viewing users and changing user's password).
 */
class UserController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * User manager.
     * @var \User\Service\UserManager
     */
    private $userManager;
    /**
     * @var
     */
    private $userform;
    /**
     * @var
     */
    private $config;


    /**
     * @var RendererInterface
     */
    protected $renderer;

    private $entity;
    private $controller;
    private $route;

    protected $mail;
    private $email;

    /**
     * Constructor.
     * @param $entityManager
     * @param $userManager
     */
    public function __construct($entityManager, $userManager, $userform, $email, $config)
    {
        $this->entityManager    = $entityManager;
        $this->userManager      = $userManager;
        $this->entity           = User::class;
        $this->controller       = 'users';
        $this->route            = 'admin/default';


        $this->userform = $userform;

        $this->email = $email;
        $this->config = $config;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */
    public function indexAction() 
    {

        $users = $this->entityManager->getRepository(User::class)
                ->findBy([], ['id'=>'ASC']);

        $turmas = $this->entityManager->getRepository(Turma::class)
            ->findBy(['status' => Turma::STATUS_ACTIVE], ['titulo'=>'ASC']);
        
        return new ViewModel([
            'users' => $users,
            'turma' => $turmas,
            'controller' => 'users',
        ]);
    }

    public function addAction()
    {
        $form = $this->userform;

        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi postado
            $file = $this->getRequest()->getFiles()->toArray();



            if ($file['file']['error'] == '0') {

                $request = $this->getRequest();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );


                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                // Pass data to form
                $form->setData($data);

                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    //
                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));
                    $data['file'] = array_pop($arquivo);//Nome do arquivo randômico

                    // Add user.
                    $return = $this->userManager->addUser($data);

                    // se o status foi 1 envio o email de boas vindas
                    if( $data['status'] !== '1'){

                        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
                        $link = 'http://' . $httpHost . '/register/activate/' . $return->getActivationKey();

                        $body = "Olá, $return->getFullName(),<br>\n<br>";
                        $body .= "Parabéns! Você foi cadastrado(a) em nosso sistema, para confirmar acesse o link abaixo ou clique no botão <br>\n<br>";
                        $body .= "$link\n<br>";

                       //------envio de email----
                        /** @var \User\Mail\Mail $mail */
                        $mail = $this->email;

                        $enviar = $mail->sendEmail($return->getFullName(), 'Confirmação de Cadastro AVA-SPP', $return->getEmail(), $link ,$body, 'add-user', null, $this->config);

                        if($enviar == true){

                            $this->flashMessenger()->addSuccessMessage('Cadastro e envio de email para ativação com sucesso!');

                            // redireciona to "view" page
                            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                        }

                    }else{

                        $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                        // Redirect to "view" page
                        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                    }

                }
//                else{
//
//                    print_r($form->getMessages()); die;
//
//                }

                }else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {

                    $return = $this->userManager->addUser($data);

                    if( $data['status'] !== '1'){

                    $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
                    $link = 'http://' . $httpHost . '/register/activate/' . $return->getActivationKey();

                    $body = "Olá, ".$return->getFullName().",<br>\n<br>";
                    $body .= "Parabéns! Você foi cadastrado(a) em nosso sistema, para confirmar acesse o link abaixo ou clique no botão <br>\n<br>";
                    $body .= $link."\n<br>";



                        //------envio de email----
                        /** @var \User\Mail\Mail $mail */
                        $mail = $this->email;

                        $enviar = $mail->sendEmail($return->getFullName(), 'Confirmação de Cadastro AVA-SPP', $return->getEmail(), $link ,$body, 'add-user', null, $this->config);

                        if($enviar == true){

                            $this->flashMessenger()->addSuccessMessage('Cadastro e envio de email para ativação com sucesso!');

                            // redireciona to "view" page
                            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                        }

                    }else{
                        $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                        // Redirect to "view" page
                        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                    }

                };

            }//end image verify

        }

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

        $user = $this->entityManager->getRepository(User::class)
                ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

       

        // Create user form
        $form =  $this->userform;


        $form->getInputFilter()->get('password')->setRequired(false);
        $form->getInputFilter()->get('confirm_password')->setRequired(false);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi postada
            $file = $this->getRequest()->getFiles()->toArray();


            if ($file['file']['error'] == '0' || $file['contrato']['error'] == '0') {

                $request = $this->getRequest();

                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                if($data['email'] == $user->getEmail()){
                    //se email for igual vindo do forma, tira a validaçao
                    $form->getInputFilter()->remove('email');
                }

                // Pass data to form
                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    //--pego os dados do arrat
                    $dados = $request->getPost()->toArray();

                    //--verifica se existe foto e substitui
                    $foto = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    if (empty($foto)) {
                        $dados['file'] = $user->getFile();
                    } else {

                        $dados['file'] = array_pop($foto);//Nome do arquivo randômico

                        $file = './public/media/' . $user->getFile();

                        if (!is_dir($file)) {
                            unlink($file);
                        } else {
                        }

                    }

                    //--contrato--


                    //--verifica se existe foto e substitui
                    $contrato = array_filter(explode(DIRECTORY_SEPARATOR, $data['contrato']['tmp_name']));

                    if (empty($contrato)) {
                        $dados['contrato'] = $user->getContrato();
                    } else {

                        $dados['contrato'] = array_pop($contrato);//Nome do arquivo randômico

                        $fileContrato = './public/media/' . $user->getContrato();

                        if (!is_dir($fileContrato)) {
                            unlink($fileContrato);
                        } else {
                        }

                    }

                    // Update the user.
                    $this->userManager->updateUser($user, $dados);


                    $recuperaFileEnviado = $this->getRequest()->getFiles()->toArray();

                    if ($recuperaFileEnviado['file']['error'] == '0') {

                        // se foi enviado imagem ele redireciona para imagem
                        return $this->redirect()->toRoute('admin/imagem',[
                            'action' => 'edit'
                                ] ,[
                                    'force_canonical' => true,
                                    'query' => [
                                'user' => $user->getId(),
                            ]
                        ]);

                    } else {

                        // se foi enviado somente contrato continua na mesma pagina
                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                        return $this->redirect()->toRoute('admin/default', ['controller' => 'users', 'action' => 'edit', 'id' => $user->getId()]);

                    }





                }//end envia imagem

            } else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();


                if($data['email'] == $user->getEmail()){
                    //se email for igual vindo do forma, tira a validaçao
                    $form->getInputFilter()->remove('email');
                }

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {


                    $data['file'] = $user->getFile();


                    // Update the user.
                    $this->userManager->updateUser($user, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute('admin/default', [ 'controller' => 'users',
                        'action' => 'edit', 'id' => $user->getId()]);
                }

//                else{
//
//                    print_r($form->getMessages());
//
//                    die;
//                }

            }



        } else {


            $form->setData(array(
                    'id'                => $user->getId(),
                    'isteacher'         => $user->getIsteacher(),
                    'codigo'            => $user->getCodigo(),
                    'full_name'         => $user->getFullName(),
                    'email'             => $user->getEmail(),
                    'status'            => $user->getStatus(),
                    'file'              => $user->getFile(),
                    'nascimento'        => $user->getNascimento(),
                    'rua'               => $user->getRua(),
                    'numero'            => $user->getNumero(),
                    'bairro'            => $user->getBairro(),
                    'complemento'       => $user->getComplemento(),
                    'cidade'            => $user->getCidade(),
                    'estado'            => $user->getEstado(),
                    'cep'               => $user->getCep(),
                    'cpf'               => $user->getCpf(),
                    'rg'                => $user->getRg(),
                    'celular'           => $user->getCelular(),
                    'telefone'          => $user->getTelefone(),
                    'role'              => $user->getRole(),
                    'sexo'              => $user->getSexo(),
                    'polo'              => $user->getPolo()->getId(),
                    'pai'               => $user->getPai(),
                    'mae'               => $user->getMae(),
                    'naturalidade'      => $user->getNaturalidade(),
                    'recado'            => $user->getRecado(),
                    'profissao'         => $user->getProfissao(),
                    'emissor'           => $user->getEmissor(),
                    'formacao'          => $user->getFormacao(),
                    'empresa'           => $user->getEmpresa(),
                    'empresaendereco'   => $user->getEmpresaendereco(),
                    'empresanumero'     => $user->getEmpresanumero(),
                    'empresacomplemento' => $user->getEmpresacomplemento(),
                    'empresabairro'     => $user->getEmpresabairro(),
                    'empresacidade'     => $user->getEmpresacidade(),
                    'empresacep'        => $user->getEmpresacep(),
                    'empresatel'        => $user->getTelefone(),
                    'empresaramal'      => $user->getEmpresaramal(),
                    'empresacel'        => $user->getEmpresacel(),
                    'website'           => $user->getWebsite(),
                    'empresaemail'      => $user->getEmpresaemail(),
                    'contrato'          => $user->getContrato(),
                ));
        }

        return new ViewModel(array(
            'id'    => $id,
            'user'  => $user,
            'form'  => $form
        ));
    }

    public function perfilAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->find($id);


        if ($user == null) {
            return $this->redirect()->toRoute($this->route, [ 'controller' => 'home']);
        }

        $this->verifica($id);

        // Create user form
        $form = $this->userform;

        $form->getInputFilter()->get('password')->setRequired(false);
        $form->getInputFilter()->get('confirm_password')->setRequired(false);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi postada
            $file = $this->getRequest()->getFiles()->toArray();


            if ($file['file']['error'] == '0') {

                $request = $this->getRequest();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                if ($data['email'] == $user->getEmail()) {
                    //se email for igual vindo do forma, tira a validaçao
                    $form->getInputFilter()->remove('email');
                }

                // Pass data to form
                $form->setData($data);

                $form->getInputFilter()->get('password')->setRequired(false);
                $form->getInputFilter()->get('confirm_password')->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('status');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('role');
                $imageFilter->setRequired(false);


                $imageFilter = $form->getInputFilter()->get('isteacher');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('polo');
                $imageFilter->setRequired(false);


                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();


                    $dados = $request->getPost()->toArray();

                    //--verifica se existe foto e substitui
                    $foto = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    if(empty($foto)){
                        $dados['file'] = $user->getFile();
                    }else{

                        $dados['file'] = array_pop($foto);//Nome do arquivo randômico

                        $file = './public/media/' .$user->getFile();

                        if (!is_dir($file)) {
                            unlink($file);
                        }else{}

                    }


                    $dados['role']  = $user->getRole()->getId();
                    $dados['status'] = $user->getStatus();
                    $dados['$contrato'] = $user->getContrato();
                    $dados['polo'] = $user->getPolo()->getId();


                    // Update the user.
                    $this->userManager->updateUser($user, $dados);


                    $recuperaFileEnviado = $this->getRequest()->getFiles()->toArray();

                    if ($recuperaFileEnviado['file']['error'] == '0') {

                        // se foi enviado imagem ele redireciona para imagem
                        return $this->redirect()->toRoute('admin/imagem',[
                            'action' => 'edit'
                        ] ,[
                            'force_canonical' => true,
                            'query' => [
                                'user' => $user->getId(),
                                'perfil' => 'true'
                            ]
                        ]);

                    } else {

                        // se foi enviado somente contrato continua na mesma pagina
                        $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                        // Redirect to "view" page
                        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'perfil', 'id' => $user->getId()]);

                    }

                }
                else{

                        print_r($form->getMessages()); die;
                    }

            } else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();


                if ($data['email'] == $user->getEmail()) {
                    //se email for igual vindo do forma, tira a validaçao
                    $form->getInputFilter()->remove('email');
                }

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('status');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('role');
                $imageFilter->setRequired(false);


                $imageFilter = $form->getInputFilter()->get('isteacher');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('polo');
                $imageFilter->setRequired(false);


                // Validate form
                if ($form->isValid()) {


                    //$data['file'] = $user->getFile();
                    $data['status'] = $user->getStatus();
                    $data['role']  = $user->getRole()->getId();
                    $data['polo']  = $user->getPolo()->getId();


                    // Update the user.
                    $this->userManager->updateUser($user, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller,
                        'action' => 'perfil', 'id' => $user->getId()]);
                }
            }
        } else {
            $form->setData(array(
                'id' => $user->getId(),
                'codigo' => $user->getCodigo(),
                'full_name' => $user->getFullName(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus(),
                'file' => $user->getFile(),
                'nascimento' => $user->getNascimento(),
                'rua' => $user->getRua(),
                'numero' => $user->getNumero(),
                'bairro' => $user->getBairro(),
                'complemento' => $user->getComplemento(),
                'cidade' => $user->getCidade(),
                'estado' => $user->getEstado(),
                'cep' => $user->getCep(),
                'cpf' => $user->getCpf(),
                'rg' => $user->getRg(),
                'celular' => $user->getCelular(),
                'telefone'          => $user->getTelefone(),
                'role'              => $user->getRole(),
                'sexo'              => $user->getSexo(),
                'polo'              => $user->getPolo()->getId(),
                'pai' => $user->getPai(),
                'mae' => $user->getMae(),
                'naturalidade'      => $user->getNaturalidade(),
                'recado'            => $user->getRecado(),
                'profissao'         => $user->getProfissao(),
                'emissor'           => $user->getEmissor(),
                'formacao'          => $user->getFormacao(),
                'empresa'           => $user->getEmpresa(),
                'empresaendereco'   => $user->getEmpresaendereco(),
                'empresanumero'     => $user->getEmpresanumero(),
                'empresacomplemento' => $user->getEmpresacomplemento(),
                'empresabairro'     => $user->getEmpresabairro(),
                'empresacidade'     => $user->getEmpresacidade(),
                'empresacep'        => $user->getEmpresacep(),
                'empresatel'        => $user->getTelefone(),
                'empresaramal'      => $user->getEmpresaramal(),
                'empresacel'        => $user->getEmpresacel(),
                'website'           => $user->getWebsite(),
                'empresaemail'      => $user->getEmpresaemail(),
                'contrato'          => $user->getContrato(),

            ));
        }




        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }

    public function imagemAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->find($id);

        $buscaUser = $this->entityManager->getRepository(User::class)
            ->findBy([ 'id' => $id]);

        foreach ($buscaUser as $u => $resUser){}


        if ($user == null) {
            return $this->redirect()->toRoute($this->route, [ 'controller' => 'home']);
        }

        $idDb = $this->identity()->getId();

        if($idDb == $id) {

            $status = $_POST['status'];

            if($status == 'false') {

                $file = $resUser->getFile();
                $thumb = $resUser->getThumb();

                if(!empty($file)){
                    unlink('public/media/' . $file);
                }

                if(!empty($thumb)){
                    unlink('public/media/thumb/' . $thumb);
                }

                $this->userManager->updateImagem($user);
            }
        }else{
            return  $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

    }

    public function contratoAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->find($id);


        if ($user == null) {
            return $this->redirect()->toRoute($this->route, [ 'controller' => 'home']);
        }
        $idDb = $this->identity()->getId();

        if($idDb == $id) {

            $status = $_POST['status'];

            if($status == 'false') {
                $this->userManager->updateContrato($user);
            }
        }else{
            return  $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

    }

    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->verifica($id);


        // Find a user with such ID.
        $user = $this->entityManager->getRepository(User::class)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    public function changePasswordAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->verifica($id);

        $user = $this->entityManager->getRepository(User::class)
                ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }



        // Create "change password" form
        $form = new PasswordChangeForm('change');

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);


            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();


                // Try to change password.
                $this->userManager->changePassword($user, $data) ;

                $this->flashMessenger()->addSuccessMessage(
                            'Senha alterada com sucesso!');

                // Redirect to "view" page
                return $this->redirect()->toRoute('admin/default',
                        ['controller' => 'users', 'action'=>'change-password', 'id'=>$user->getId()]);
            }else{

                print_r($form->getMessages()); die;

            }
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    public function recoverAction()
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
                    
                    $return = $this->userManager->generatePasswordResetToken($user);
                    
                   if(!empty($return)){

                       $ip = $_SERVER["REMOTE_ADDR"];


                       $dataHora = "Data/Hora: ".date('d/m/Y H:i:s');

                       $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
                       $link = 'http://' . $httpHost . '/set-password?token=' . $return;

                       $body = "Olá, $user,<br>\n<br>";
                       $body .= "Foi solicitado a recuperação de senha do AVA-SPP, para recuperar clique no botão, ou copie e cole no seu navegador o link abaixo. <br>\n<br>";
                       $body .= "$link\n<br><br>";
                       $body .= "IP:  $ip\n<br>";
                       $body .= " $dataHora";

                       $emailUsuario = $user->getEmail();


                       /** @var \User\Mail\Mail $mail */
                       $mail = $this->email;

                       $mail->sendEmail($user, 'Recuperar Senha AVA-SPP', $emailUsuario, $link ,$body, 'recover-user', null, $this->config);


                        //Redirect to "message" page
                       return $this->redirect()->toRoute('recover',
                           ['action' => 'message', 'id' => 'sent']);


                   }else{
                       // Redirect to "message" page
                       return $this->redirect()->toRoute('recover');
                   }

                } else {
                    return $this->redirect()->toRoute('recover',
                            ['action'=>'message', 'id'=>'invalid-email']);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            $this->layout('layout/login')
        ]);
    }

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

    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);

        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            return $this->redirect()->toRoute('recover',
                ['action'=>'message', 'id'=>'failed']);
        }

        if($token===null ||
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('recover',
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
                    return $this->redirect()->toRoute('recover',
                            ['action'=>'message', 'id'=>'set']);
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('recover',
                            ['action'=>'message', 'id'=>'failed']);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            $this->layout('layout/login')
        ]);
    }

    // *** deleta usuario ***
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        $boleto = $this->checkBoletoAluno($id);

        $verificaTurma = $this->checaTurma($id);

        $verificaProfessorDeTurma = $this->checaCronograma($id);

        $checaCoordenador = $this->checaCoodenador($id);



       if(empty($boleto)) {

           // *** verifica se é usuario master
           if ($id != '2') {

               // *** verifica se usuario é professor de alguma turma
               if (empty($verificaProfessorDeTurma)) {

                   // *** verifica se esta em alguma turma
                   if (empty($verificaTurma)) {


                       $file = $user->getFile();
                       if (!empty($file)) {

                           unlink('public/media/' . $file);

                       }

                       if(empty($checaCoordenador)){

                           $this->entityManager->remove($user);
                           $this->entityManager->flush();

                           $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');
                           return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);


                       }else{

                           $this->flashMessenger()->addErrorMessage('Usuário é Coordenador de alguma Turma por isso não pode ser excluido.');

                           return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
                       }



                   } else {

                       $this->flashMessenger()->addErrorMessage('Usuário não pode ser excluido enquanto estiver cadastrado em alguma Turma.');

                       return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
                   }

           }else {

                   $this->flashMessenger()->addErrorMessage('Usuário é Professor  de alguma Turma por isso não pode ser excluido.');

                   return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
               }


           }else {

               $this->flashMessenger()->addErrorMessage('Este Usuário não pode ser excluido!');

               return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

           }
       }else{

           $this->flashMessenger()->addErrorMessage('Este Usuário possui '.$boleto.' boletos emitido(s), exclua os boletos antes de exclui-lo!');

           return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
       }

    }

    public function delimageAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->find($id);


        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $file = getcwd().'/public/media/' .$user->getFile();
        if(!is_dir($file)){

            unlink($file);

        }else{}

        $data['file'] = "";
        $data['email'] = $user->getEmail();
        $data['turma'] = $user->getTurma()->getId();

        $return = $this->userManager->updateUser($user, $data);

        if($return) {
            return new JsonModel([
                'data' =>  true,
            ]);
        }

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

    // *** gera senha para usuario ***
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

    public function checkUserExists($email) {

        $user = $this->entityManager->getRepository(User::class)
            ->findOneByEmail($email);

        return $user !== null;
    }

    public function checkBoletoAluno($id) {

        $user = $this->entityManager->getRepository(Boleto::class)
            ->findBy(['aluno' => $id]);
        $contar = count($user);
        return $contar;
    }

    public function checaTurma($id) {

        $user = $this->entityManager->getRepository(Alunos::class)
            ->findBy(['usuario' => $id]);
        $contar = count($user);
        return $contar;
    }

    /*verifica se usuario é coodenador*/
    public function checaCoodenador($id) {

        $user = $this->entityManager->getRepository(Turma::class)
            ->findBy(['coordenador' => $id]);
        $contar = count($user);
        return $contar;
    }

    // *** verifica se usuario esta cadastrado como professor de alguma turma
    public function checaCronograma($id){

        $user = $this->entityManager->getRepository(Cronograma::class)
            ->findBy(['professor' => $id]);
        $contar = count($user);
        return $contar;

    }

    // *** teste de envio de email ***
    public function enviaAction()
    {

        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $link = 'http://' . $httpHost . '/activate/' . 23613461346;

        $file = "public/teste.txt";

        $body = "Olá, Cau,<br>\n<br>";
        $body .= "Parabéns! Você foi cadastrado(a) em nosso sistema, para confirmar acesse o link abaixo ou clique no botão <br>\n<br>";
        $body .= "$link\n<br>";


        /** @var \User\Mail\Mail $mail */
        $mail = $this->email;

        $enviar = $mail->sendEmail('Caubinho', 'Recuperar Senha', 'caubinho@gmail.com', $link ,$body, 'recover-user', $file, $this->config);

        if($enviar == true){

            echo "Enviado";
        }

    }

    // *** imprime cadastro do aluno ***
    public function printAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        return new ViewModel([
            'user' => $user,
            $this->layout('layout/print')
        ]);

    }

    // *** imprimir cadastro do usuario ***
    public function cadastroAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        $pdf = $this->params()->fromRoute('pdf', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->findBy(['id' => $id]);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->verifica($id);

        foreach ($user as $u => $resUser){}

        if ($pdf == 'pdf') {

            // *** defino layout ***
            $model = new ViewModel(array('user' => $user));
            $model->setTemplate("pdf/cadastro.phtml");
            $model->setOption('has_parent',true);

            $renderer = $this->renderer;

            $html = $renderer->render($model);

            // *** busco a classe Mpdf ***
            require_once getcwd().'/vendor/mpdf/mpdf/src/Mpdf.php';

            $mpdf = new \Mpdf\Mpdf();

            $mpdf->SetHTMLHeader('Ficha Cadastral');
            $mpdf->setFooter('{PAGENO}');
            $mpdf->WriteHTML($html);

            $mpdf->Output();

        }else{

            return new ViewModel(array(
                'user' => $user,
                $this->layout('layout/pdf')
            ));
        }

    }

    // *** verifica se é aluno ou visitante ***
    public function verifica($id)
    {
        $nivel = $this->identity()->getRole();

        if($nivel == 'Administrador' || $nivel == 'Coordenador' ){




        }else{


            //echo $nivel;
            $idDb = $this->identity()->getId();

            //echo $idDb;

            if($id != $idDb) {

                return $this->redirect()->toRoute($this->route, ['controller' => 'permissao',]);

            }


        }

    }

    // *** verifica em quantas turmas o usuario esta cadastrado ****
    public function classAction()
    {

//        $idAluno = $id = (int)$this->params()->fromRoute('id', -1);
//
//            if (empty($idAluno)) {

            $idAluno = $this->identity()->getId();


            $turma = $this->entityManager->getRepository(Alunos::class)
                ->findBy(['usuario' => $idAluno]);


            return new ViewModel(array(
                'user' => $turma,
            ));

      //  }


    }

    // *** filtra usuarios por turma ***
    public function filterAction()
    {


        $turma =  $this->params()->fromQuery('turma');

            $resTurma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['titulo' => $turma]);

                foreach ($resTurma as $a => $reIdTurma){}

            $resAlunosDaTurma = $this->entityManager->getRepository(Alunos::class)
            ->findBy(['turma' => $reIdTurma->getId()]);


        $buscarTurma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['status' => Turma::STATUS_ACTIVE  ], ['titulo'=>'ASC']);


        return new ViewModel([
            'users' => $resAlunosDaTurma,
            'turma' => $buscarTurma,
            'controller' => 'users',
        ]);



    }


}


