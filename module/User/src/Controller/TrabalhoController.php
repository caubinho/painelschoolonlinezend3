<?php
namespace User\Controller;


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Setup;
use User\Entity\Trabalho;
use User\Entity\Turma;
use User\Form\TrabalhoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;


class TrabalhoController extends AbstractActionController
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
    private $entity;
    private $controller;
    private $route;
    private $form;
    /**
     * @var
     */
    private $authService;

    private $view;
    private $email;
    private $config;


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $authService, $service,  $form, $email, $config)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Trabalho::class;
        $this->controller       = 'trabalho';
        $this->route            = 'admin/default';
        $this->authService      = $authService;


        $this->form = $form;


        $this->email = $email;
        $this->config = $config;
    }


    public function indexAction()
    {

//        $videos = $this->entityManager->getRepository($this->entity)
//            ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
//            'dados' => $videos
        ]);
    }

    public function newAction()
    {
        // Create the form.
        $form =  $this->form;

        $alunoGet = $this->params()->fromQuery('aluno');
        //verifico se existe aluno na url
        if(empty($alunoGet)){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }
        $alunoId = $this->authService->getIdentity()->getId();
        //verifico se aluno é o mesmo autenticado
        if($alunoGet != $alunoId){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }


        $turma = $this->params()->fromQuery('turma');
        //verifico se tem turma na url
        if(empty($turma)){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }

        //verifico se turma esta ativa
        $repoTurma = $this->entityManager->getRepository(Turma::class);
        $resTurma = $repoTurma->findBy(['code' =>$turma, 'status' => Turma::STATUS_ACTIVE ]);

        if($resTurma < 1){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }

        /** @var \User\Entity\Turma  $turmaDoAluno */
        foreach ($resTurma as $t => $turmaDoAluno){}


        $slugAula = $this->params()->fromQuery('aula');

        // se não existir aula redireciona para modulos
        if (empty($slugAula)) {
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }

        $repoAula = $this->entityManager->getRepository(Aula::class);
        $aula = $repoAula->findBy(['code' =>$slugAula , 'status' => Aula::STATUS_ACTIVE]);


        if ($aula == null) {
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home' ]);
        }

        /*** @var \User\Entity\Aula  $resAula */
        foreach ($aula as $a => $resAula){}



        //buscar professor do cronograma
        $resCron = $this->entityManager->getRepository(Cronograma::class);
        $cron = $resCron->findBy(['turma' =>$turma, 'aula' => $resAula->getId(),  ]);

        foreach ($cron as $c => $professor){}


        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            $request = $this->getRequest();

            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $imageFilter = $form->getInputFilter()->get('corrigido');
            $imageFilter->setRequired(false);

            $imageFilter = $form->getInputFilter()->get('texto');
            $imageFilter->setRequired(false);


            // Pass data to form
            $form->setData($data);

            if ($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                //
                $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                $dados = $request->getPost()->toArray();
                $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico

                $dados['aluno'] = $alunoId;
                $dados['turma'] = $turmaDoAluno->getId();
                $dados['aula'] = $resAula->getId();

              //print_r($dados); die;

                // Add user.
                $return = $this->service->insert($dados);

                if(!$return){

                    $this->flashMessenger()->addErrorMessage('Erro ao enviar!');

                    return $this->redirect()->toRoute('admin/trabalho', ['action' => 'list', 'sub' => 'aluno', 'id' => $alunoId]);



                }else {


                    $nomeAluno = $this->authService->getIdentity();

                    $link = ' http://ava.spp.psc.br/dashboard/trabalho/edit?code='.$return->getId().'&turma='.$turma.'&aluno='.$alunoId;

                    $file = getcwd()."/public/trabalho/" . $return->getFile();

                    $nomeProfessor = $return->getProfessor();
                    $emailProfessor = $return->getProfessor()->getEmail();

                    $assunto = 'Envio de Trabalho SPP';

                    $body = "Olá Docente, o(a) aluno(a) {$nomeAluno}, enviou um trabalho para ser corrigido no AVA da SPP. Faça seu login no AVA e depois clique no link abaixo para ser direcionado ao trabalho. Favor corrigir o mesmo em até no máximo 30 dias após seu envio.<br><br>\n<br>";


                    /** @var \User\Mail\Mail $mail */
                    $mail = $this->email;

                    $enviar = $mail->sendEmail($nomeProfessor, 'Envio de Trabalho SPP', $emailProfessor, $link ,$body, 'trabalho-user', $file , $this->config);

                    if($enviar == true){

                        $this->flashMessenger()->addSuccessMessage('Envio de trabalho com sucesso!');

                        return $this->redirect()->toRoute('admin/trabalho', ['action' => 'list'],[ 'query' => ['aluno' => $alunoId]]);

                    }


                }

            }

        };

        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'professor' => $cron,
        ]);
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromQuery('code', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $aluno = (int)$this->params()->fromQuery('aluno', -1);

        $turma = (int)$this->params()->fromQuery('turma', -1);

        $repoTurma= $this->entityManager->getRepository(Turma::class);
        $resTurma = $repoTurma->findBy(['code' => $turma, 'status' => Turma::STATUS_ACTIVE]);

        if(empty($resTurma)){

            $this->flashMessenger()->addSuccessMessage('Esta turma não existe ou esta inativa! Contate a SPP. Obrigado!');
            // Redirect to "view" page
            return $this->redirect()->toRoute('admin/trabalho',
                ['action' => 'list'], [ 'query' => [ 'aluno'=> $id]]);

        }



        $resTrab = $this->entityManager->getRepository($this->entity);
        $verifica = $resTrab->findBy(['id' => $id, 'turma' => $resTurma ,'aluno' => $aluno]);


        if ($verifica == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $dataBase = $this->entityManager->getRepository($this->entity)->find($id);

        // Create user form
        $form = $this->form;

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    //$dados = $form->getData();

                    //print_r($dados);

                    $data['aluno'] = $dataBase->getAluno()->getId();
                    $data['turma'] = $dataBase->getTurma()->getId();
                    $data['aula'] = $dataBase->getAula()->getId();


                    // Update the user.
                    $return = $this->service->update($dataBase->getId(), $data);


                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('admin/trabalho',
                        ['action' => 'edit'], [ 'query' => ['code' => $return->getId(), 'turma' => $turma , 'aluno'=> $return->getAluno()->getId()]]);

                }else{

                    print_r($form->getMessages()); die;

                }

        } else {

            $form->setData([
                'id'            => $dataBase->getId(),
                'aula'          => $dataBase->getAula(),
                'professor'     => $dataBase->getProfessor(),
                'file'          => $dataBase->getFile(),
                'corrigido'     => $dataBase->getCorrigido(),
                'texto'         => $dataBase->getTexto(),
                'aluno'         => $dataBase->getAluno(),
                'turma'         => $dataBase->getTurma(),
                'nota'          => $dataBase->getNota(),
            ]);
        }


        return new ViewModel(array(
            'dados' => $dataBase,
            'form' => $form,
        ));
    }

    public function listAction()
    {
        $id = (int)$this->params()->fromQuery('aluno', -1);


        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $role = $this->authService->getIdentity()->getRole();

        $idLogado = $this->authService->getIdentity()->getId();

        if($role == 'Aluno' || $role == 'Visitante') {

            if($id == $idLogado){

                $dataBase = $this->entityManager->getRepository($this->entity)
                    ->findBy(['aluno' => $id]);

            }else{

                return $this->redirect()->toRoute('admin/default',
                    ['controller' => 'home']);
            }

        }else{

            $dataBase = $this->entityManager->getRepository($this->entity)
                ->findBy(['aluno' => $id]);

            if (empty($dataBase)) {


                $this->flashMessenger()->addSuccessMessage('Não existe Atividade enviada por este Aluno(a).');
                // Redirect to "view" page
                return $this->redirect()->toRoute('admin/trabalho',
                    ['action' => 'index']);

            }



        }


        return new ViewModel(array(
            'trabalho' => $dataBase,
        ));


    }

    public function deleteAction()
    {
        $alunoId = $this->authService->getIdentity()->getId();

        $id = (int)$this->params()->fromQuery('code', -1);

        if ($id < 1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        $file = $user->getFile();

        if (!empty($file)) {

            unlink('./public/trabalho/' . $file);

        }
        $this->service->delete($user->getId());

        $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');


// Redirect to "view" page
        return $this->redirect()->toRoute('admin/trabalho', ['action' => 'list'], ['query'=> ['aluno' => $alunoId]]);

    }

    public function viewAction()
    {
        $id = (int)$this->params()->fromQuery('code', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $aluno = (int)$this->params()->fromQuery('aluno', -1);

        $turma = (int)$this->params()->fromQuery('turma', -1);

        $repoTurma= $this->entityManager->getRepository(Turma::class);
        $resTurma = $repoTurma->findBy(['code' => $turma, 'status' => Turma::STATUS_ACTIVE]);

        if(empty($resTurma)){

            $this->flashMessenger()->addSuccessMessage('Esta turma não existe ou esta inativa! Contate a SPP. Obrigado!');
            // Redirect to "view" page
            return $this->redirect()->toRoute('admin/trabalho',
                ['action' => 'list'], [ 'query' => [ 'aluno'=> $id]]);

        }



        $resTrab = $this->entityManager->getRepository($this->entity);
        $verifica = $resTrab->findBy(['id' => $id, 'turma' => $resTurma ,'aluno' => $aluno]);


        if ($verifica == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        return new ViewModel(array(
            'dados' => $verifica,
            'idAluno'   => $id,
        ));


    }



}
