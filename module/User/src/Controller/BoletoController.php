<?php
namespace User\Controller;


use User\Entity\Boleto;
use User\Entity\Setup;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Zend\Filter\File\RenameUpload;



/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class BoletoController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var
     */
    private $boletoForm;

    /**
     * @var \User\Service\BoletoManager
     */
    private $boletoManager;
    /**
     * @var
     */
    private $config;
    /**
     * @var
     */
    private $view;
    private $entity;
    private $route;
    private $controller;
    private $autentication;
    private $mail;
    private $email;


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $boletoManager, $boletoForm, $autentication, $email, $config)
    {
        $this->entityManager    = $entityManager;
        $this->boletoForm       = $boletoForm;
        $this->boletoManager    = $boletoManager;
        $this->entity           = Boleto::class;
        $this->controller       = 'boleto';
        $this->route            = 'admin/default';
        $this->config           = $config;
        $this->autentication    = $autentication;

        $this->email            = $email;
    }
    /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function indexAction()
    {

        /** @var \User\Entity\User $identity */
        $identity = $this->autentication->getIdentity();

        $id = $identity->getId();

        $funcao = $identity->getRole();

        if($funcao == 'Administrador') {

            // Get recent posts
            $posts = $this->entityManager->getRepository($this->entity )
                ->findBy([], ['dateCreated'=>'DESC']);

            $buscarTurma = $this->entityManager->getRepository(Turma::class)
                ->findBy(['status' => Turma::STATUS_ACTIVE  ], ['titulo'=>'ASC']);


            // Render the view template
            return new ViewModel([
                'dados' => $posts,
                'postManager' => $this->boletoManager,
                'turma' => $buscarTurma,
                'controller' => 'boleto',
            ]);

        }elseif ($funcao == 'Coordenador'){

            $posts = $this->entityManager->getRepository(Turma::class)
                ->findBy(['coordenador' => $id ]);

            $arryTurma = [];

            foreach ($posts as $resTurma){
                $turma = $this->entityManager->getRepository(Boleto::class)
                    ->findBy(['turma' => $resTurma->getId() ]);

                foreach($turma as $resCron) {
                    $arryTurma[] = $resCron;
                }
            }


            // Render the view template
            return new ViewModel([
                'dados' => $arryTurma,
                'postManager' => $this->boletoManager,

            ]);

        }

    }

    public function alunoAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
            // Get recent posts
        $posts = $this->entityManager->getRepository($this->entity)
                ->findBy(['aluno' => $id], ['vencimento' => 'DESC']);



        // Render the view template
        return new ViewModel([
            'dados' => $posts,
        ]);
    }

    // envia um boleto por vez
    public function newAction()
    {
        // Create the form.
        $form =  $this->boletoForm;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi pastado
            $file = $this->getRequest()->getFiles()->toArray();

            if($file['file']['error'] == '0'){

                $request = $this->getRequest();


                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                // VERIFICO SE O ARQUIVO TEM O CPF DO USUARIO NO NOME
                $numCpf = preg_replace("/[^0-9]/", "", $data['file']['name']);
                $qtdDigitos = substr($numCpf, -11);

                //--INCLUDE THE DOT AND SLASH IN THE CPF
                $cpfFormatado = preg_replace('@^(\d{3})(\d{3})(\d{3})(\d{2})$@', '$1.$2.$3-$4',  $qtdDigitos );

                // faz consulta no banco dos alunos que irão receber o boleto
                $retornAluno = $this->consultaUserCpf($cpfFormatado);

                if(empty($retornAluno)){

                    $this->flashMessenger()->addErrorMessage('Não existe usuário com este CPF!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'new']);
                }

                if($retornAluno != $data['aluno'] ){
                    $this->flashMessenger()->addErrorMessage('CPF do arquivo não corresponde ao do Usuário!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'new']);
                }


                $dados = $request->getPost()->toArray();


                //verifica se existe diretorio
                $filename = getcwd().'/public/boleto/'.$dados['vencimento'];

                if (is_dir($filename)) {

                    $caminhoArquivo = $filename;

                } else {

                    mkdir($filename, 0755);

                    $caminhoArquivo = $filename;

                }


                // faz o upload do arquivo na pasta criada
                $input = $form->getInputFilter()->get('file');

                $input->getFilterChain()->attach(new RenameUpload(array(
                    'options' => array(
                        'target' =>  $caminhoArquivo.'/',
                        'useUploadName'=>true,
                        'randomize' => false,
                        'overwrite'=>true,
                        'useUploadExtension'=>true,
                        ),
                    )
                ));

                $form->setData($data);


                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    //$dados = $request->getPost()->toArray();

                    $data['file'] = array_pop($arquivo);//Nome do arquivo randômico


                    $return = $this->boletoManager->insert($data);

                        if($return->getEnvio() != '1'){

                            $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso sem envio por E-mail!');
                            // Redirect to "view" page
                            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'edit', 'id'=> $return->getId()]);


                        }


                        $this->enviarBoleto($return->getId());



                    $this->flashMessenger()->addSuccessMessage('Cadastro e envio por E-mail com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'edit', 'id'=> $return->getId()]);


                }//---end validação ã

            }//---end verifica arquivo
        }

        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    }

    // enviar multiplus boletos
    public function uploadAction()
    {
        // Create the form.
        $form =  $this->boletoForm;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi pastado
            $file = $this->getRequest()->getFiles()->toArray();

            if($file['file']['error'] == '0'){

                $request = $this->getRequest();



                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                // Pass data to form
                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('aluno');
                $imageFilter->setRequired(false);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();


                    $extensao = pathinfo($data['file']['name'], PATHINFO_EXTENSION);

                    $ext = strtolower($extensao);



                    //verifico se a extensão é zip novamente
                    if($ext != 'zip'){

                        $this->flashMessenger()->addSuccessMessage('Arquivo inválido! Verifique o nome e a extensão do arquivo(zip)');
                        // Redirect to "view" page
                        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'upload']);



                    }else {

                        $dados = $request->getPost()->toArray();




                        date_default_timezone_set('America/Sao_Paulo');

                        $destino = getcwd().'/public/boleto/'.$dados['vencimento'].'/';


                        $arquivoZip = $data['file']['tmp_name'];


                        $types = 'pdf';

                        $zip = new \ZipArchive();

                        $zip->open($arquivoZip);


                        for($num = 0; $num < $zip->numFiles; $num++)
                        {

                            $fileInfo = $zip->statIndex($num);


                            if($zip->extractTo($destino) == TRUE){

                                //---take the extension PATHINFO_EXTENSION
                                $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);

                                if($ext == $types)

                                    //--SEPARATE THE NUMBER OF THE NAME FILE
                                $numCpf = preg_replace("/[^0-9]/", "", $fileInfo['name']);

                                $qtdDigitos = substr($numCpf, -11);

                                    //--INCLUDE THE DOT AND SLASH IN THE CPF
                                $cpfFormatado = preg_replace('@^(\d{3})(\d{3})(\d{3})(\d{2})$@', '$1.$2.$3-$4',  $qtdDigitos );


                                $array = array($num => $cpfFormatado);


                                foreach ($array as $a){

                                    $dados = $request->getPost()->toArray();


                                    // faz consulta no banco dos alunos que irão receber o boleto
                                    $retornAluno = $this->consultaUserCpf($a);


                                    $dados['aluno'] = $retornAluno;
                                    $dados['file'] = $fileInfo['name'];

                                    $return = $this->boletoManager->insert($dados);


                                    if ($return->getEnvio() != '1') {

                                        //$this->flashMessenger()->addSuccessMessage('Cadastro com sucesso (Sem) envio por E-mail!');


                                    } else {

                                        $this->enviarBoleto($return->getId());

                                        unlink($arquivoZip);

                                        ///$this->flashMessenger()->addSuccessMessage('Cadastro e Envio por E-mail com sucesso!');
                                    }


                                }
                                //$this->flashMessenger()->addSuccessMessage('Cadastro e Envio por E-mail com sucesso!');
                            }

//                            else{
//                                echo "Arquivo inválido!";
//
//                            }

                        }
                        $zip->close();


                        $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                        // Redirect to "view" page
                        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

                    }

                }else{
                    print_r($form->getMessages()); die;
                }

            }

        }

        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    }

    function consultaUserCpf($cpf)
    {

        $entityManager = $this->entityManager;

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('p')
            ->from(User::class, 'p')
            ->where('p.cpf = ?1')
            ->setParameter('1',$cpf);

        $retorno = $queryBuilder->getQuery();

        /** @var \User\Entity\User $entity */
        foreach($retorno->getResult() as $entity) {

            return $entity->getId();
        }



    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $dataBase = $this->entityManager->getRepository($this->entity )
            ->find($id);

        if ($dataBase == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = $this->boletoForm;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi pastado
            $file = $this->getRequest()->getFiles()->toArray();

            if($file['file']['error'] == '0'){

                $request = $this->getRequest();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );


                // VERIFICO SE O ARQUIVO TEM O CPF DO USUARIO NO NOME
                $numCpf = preg_replace("/[^0-9]/", "", $data['file']['name']);
                $qtdDigitos = substr($numCpf, -11);

                //--INCLUDE THE DOT AND SLASH IN THE CPF
                $cpfFormatado = preg_replace('@^(\d{3})(\d{3})(\d{3})(\d{2})$@', '$1.$2.$3-$4',  $qtdDigitos );

                // faz consulta no banco dos alunos que irão receber o boleto
                $retornAluno = $this->consultaUserCpf($cpfFormatado);

                if(empty($retornAluno)){

                    $this->flashMessenger()->addErrorMessage('Não existe usuário com este CPF!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'new']);
                }

                if($retornAluno != $data['aluno'] ){
                    $this->flashMessenger()->addErrorMessage('CPF do arquivo não corresponde ao do Usuário!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'new']);
                }

                $dados = $request->getPost()->toArray();

                //verifica se existe diretorio
                $filename = getcwd().'/public/boleto/'.$dados['vencimento'];

                if (is_dir($filename)) {

                    $caminhoArquivo = $filename;

                } else {

                    mkdir($filename, 0755);

                    $caminhoArquivo = $filename;

                }


                // faz o upload do arquivo na pasta criada
                $input = $form->getInputFilter()->get('file');

                $input->getFilterChain()->attach(new RenameUpload(array(
                        'options' => array(
                            'target' =>  $caminhoArquivo.'/',
                            'useUploadName'=>true,
                            'randomize' => false,
                            'overwrite'=>true,
                            'useUploadExtension'=>true,
                        ),
                    )
                ));


                // Pass data to form
                $form->setData($data);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    //unlink('./public/boleto/'.$dataBase->getFile());

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    //$dados = $request->getPost()->toArray();

                    $data['file'] = array_pop($arquivo);//Nome do arquivo randômico

                    // Add user.
                    $return = $this->boletoManager->update($dataBase, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'edit', 'id'=> $return->getId()]);
                }

            }else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('envio');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    // Update the user.
                    $return = $this->boletoManager->update($dataBase, $this->getRequest()->getPost()->toArray());

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route,
                        ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);
                }

//                else{
//
//                    print_r($form->getMessages()); die;
//                }
            }
        } else {
            $form->setData([
                'id'            => $dataBase->getId(),
                'aluno'         => $dataBase->getAluno(),
                'turma'         => $dataBase->getTurma()->getId(),
                'vencimento'    => $dataBase->getVencimento(),
                'status'        => $dataBase->getStatus(),
                'texto'         => $dataBase->getTexto(),
                'file'          => $dataBase->getFile(),
                'envio'         => $dataBase->getEnvio(),
                'codigo'        => $dataBase->getCodigo(),
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

        $user = $this->entityManager->getRepository($this->entity )
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $file = $user->getFile();

        $vencimento = $user->getVencimento()->format('Y-m-d');


        if(!empty($file)){

            unlink('./public/boleto/'.$vencimento.'/'.$file);

        }
        $this->boletoManager->delete($user);

        $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');
        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

    }

    public function delimageAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository($this->entity )
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $file = $user->getFile();

        if(!empty($file)){

            unlink('public/boleto/'.$file);

        }

        $data['file'] = "";

        $return = $this->boletoManager->update($user, $data);

        if($return) {
            return new JsonModel([
                'data' => true,
            ]);
        }

    }
    //visualiza boleto
    public function viewAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        
        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

         $this->verifica($id);



        // Get recent posts
            $posts = $this->entityManager->getRepository($this->entity)
                ->findBy(['aluno' => $id], ['dateCreated' => 'ASC']);


        // Render the view template
        return new ViewModel([
            'dados' => $posts,
        ]);
    }

    public function downloadAction(){

        $id = (int)$this->params()->fromRoute('id', -1);


        if ($id < 1) {
            return $this->redirect()->toRoute('boleto',
                ['action' => 'message', 'id' => 'invalid']);
        }

        // Get recent posts
        $boleto = $this->entityManager->getRepository($this->entity)
            ->findBy(['codigo' => $id ]);

        $qts = count($boleto);



        if($qts < 1){

            return $this->redirect()->toRoute('boleto',
                ['action' => 'message', 'id' => 'invalid']);

        }

        /** @var \User\Entity\Boleto $b */
        foreach ($boleto as $b) {

                $dataVencimento = $b->getVencimento()->format('Y-m-d');


               if ($b->getStatus() == '1') {

                   $linkValido1 = getcwd()."/public/boleto/".$dataVencimento."/".$b->getFile();

                   $linkValido2 = getcwd()."/public/boleto/".$b->getFile();


                   if(file_exists($linkValido1)) {

                       return $this->redirect()->toUrl("http://" . $_SERVER['HTTP_HOST'] . "/boleto/".$dataVencimento.'/'.$b->getFile());

                   }elseif(file_exists($linkValido2)){

                       return $this->redirect()->toUrl("http://" . $_SERVER['HTTP_HOST'] . "/boleto/".$b->getFile());

                   }else{

                       return $this->redirect()->toRoute('boleto',
                           ['action' => 'message', 'id' => 'invalid']);
                   }

               } else {
                   return $this->redirect()->toRoute('boleto',
                       ['action' => 'message', 'id' => 'paid']);

               };
        }


        return new ViewModel([
            $this->layout('layout/login')
        ]);
    }

    public function messageAction()
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');

        // Validate input argument.
        if($id!='paid' && $id!='invalid' && $id!='failed') {
            return $this->redirect()->toRoute('home');
        }

        return new ViewModel([
            'id' => $id,
            $this->layout('layout/login')
        ]);
    }

    public function statusAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            return new JsonModel(['data' => 'Necessário um boleto',
            ]);
        }

        $dataBase = $this->entityManager->getRepository($this->entity )
            ->find($id);

        if ($dataBase == null) {
            return new JsonModel([
                'data' => 'Não existe este boleto!',
            ]);
        }


        $status['status'] = $_POST['status'];
        // Update the user.
        //
        $return = $this->boletoManager->status($dataBase, $status);

        if($return) {
           return new JsonModel(['response'=> $return]);
        }else{
           return new JsonModel(['response'=> false]);
        }



    }

    public function verificaAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            return new JsonModel(['data' => 'Necessário um boleto',
            ]);
        }

        $dataBase = $this->entityManager->getRepository($this->entity )
            ->findBy(['id' => $id]);

        if ($dataBase == null) {
            return new JsonModel([
                'data' => 'Não existe este boleto!',
            ]);
        }

        $array = [];

        foreach($dataBase as $boleto){

            $array[$boleto->getId()] = $boleto->getStatus();


        }

        return new JsonModel(["data" => $array]);


    }

    // ação de enviar pela rota
    public function enviarAction(){

        $id = (int)$this->params()->fromRoute('id', -1);

        // Get recent posts
        $boleto = $this->entityManager->getRepository($this->entity)
            ->findBy(['id' => $id ]);


        if ($boleto == null) {
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
        }

        /** @var \User\Entity\Boleto $b */
        foreach ($boleto as $b) {

            if ($b->getStatus() == '1') {

                $aluno = $b->getAluno()->getFullName();


                $vencimento = $b->getVencimento()->format('Y-m-d');

                $file = getcwd()."/public/boleto/".$vencimento."/" . $b->getFile();


                $link = "http://" . $_SERVER['HTTP_HOST'] . "/boleto/download/" . $b->getCodigo();


                $mensagem = $b->getTexto();

                if($mensagem == "") {

                    $body = "Prezado(a) aluno(a),  $aluno, Saudações Psicanalíticas.<br>\n<br>";
                    $body .= "Em anexo está(ão) seu(s) boleto(s).<br>\n<br>";
                    $body .= "Para maior comodidade, você pode verificar com o(a) gerente de sua conta sobre o DDA (DÉBITO DIRETO AUTORIZADO) para efetuar o pagamento diretamente em seu smartphone, notebook ou PC..<br>\n<br>";
                    $body .= "Caso precise atualizar seu boleto vencido, bastar acessar o link abaixo:<br>\n<br>";
                    $body .= "https://www63.bb.com.br/portalbb/boleto/boletos/hc21e,802,3322,10343.bbx\n<br><br>";
                    $body .= "Atenciosamente,<br><br>\n";
                    $body .= "A direção<br>\n<br>";

                }else{

                    $body = $mensagem;
                }

                /** @var \User\Mail\Mail $mail */
                $mail = $this->email;

                $mail->sendEmail($aluno, 'Boleto - Sociedade Psicanalitica do Paraná', $b->getAluno()->getEmail(), $link ,$body, 'boleto-user', $file, $this->config);

                return new JsonModel([

                    'msg' => 'Boleto enviado com sucesso!',
                    'data' => true,

                ]);

            } else {

                return new JsonModel([
                    'msg' => 'Boleto já pago!',
                    'data' => false,
                ]);

            };
        }

    }

    // funçao para enviar dentro do new e do upload(Multiplos)
    public function enviarBoleto($idBoleto){



        // Get recent posts
        $boleto = $this->entityManager->getRepository($this->entity)
            ->findBy(['id' => $idBoleto ]);


        if ($boleto == null) {
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
        }

        /** @var \User\Entity\Boleto $b */

        foreach ($boleto as $b) {

                $aluno = $b->getAluno()->getFullName();


                $vencimento = $b->getVencimento()->format('Y-m-d');

                $file = getcwd()."/public/boleto/".$vencimento."/" . $b->getFile();


                $link = "http://" . $_SERVER['HTTP_HOST'] . "/boleto/download/" . $b->getCodigo();


                $mensagem = $b->getTexto();

                if($mensagem == "") {

                    $body = "Prezado(a) aluno(a),  $aluno, Saudações Psicanalíticas.<br>\n<br>";
                    $body .= "Em anexo está(ão) seu(s) boleto(s).<br>\n<br>";
                    $body .= "Para maior comodidade, você pode verificar com o(a) gerente de sua conta sobre o DDA (DÉBITO DIRETO AUTORIZADO) para efetuar o pagamento diretamente em seu smartphone, notebook ou PC..<br>\n<br>";
                    $body .= "Caso precise atualizar seu boleto vencido, bastar acessar o link abaixo:<br>\n<br>";
                    $body .= "https://www63.bb.com.br/portalbb/boleto/boletos/hc21e,802,3322,10343.bbx\n<br><br>";
                    $body .= "Atenciosamente,<br><br>\n";
                    $body .= "A direção<br>\n<br>";

                }else{


                    $body = $mensagem;
                }


                /** @var \User\Mail\Mail $mail */
                $mail = $this->email;

                $mail->sendEmail($aluno, 'Boleto - Sociedade Psicanalitica do Paraná', $b->getAluno()->getEmail(), $link ,$body, 'boleto-user', $file, $this->config);



        }

    }


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

    public function filterAction()
    {


        $turma =  $this->params()->fromQuery('turma');

        $resTurma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['titulo' => $turma]);

        foreach ($resTurma as $a => $reIdTurma){}

        $resAlunosDaTurma = $this->entityManager->getRepository(Boleto::class)
            ->findBy(['turma' => $reIdTurma->getId()]);


        $buscarTurma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['status' => Turma::STATUS_ACTIVE  ], ['titulo'=>'ASC']);


        return new ViewModel([
            'users' => $resAlunosDaTurma,
            'turma' => $buscarTurma,
            'controller' => 'boleto',
        ]);



    }
}
