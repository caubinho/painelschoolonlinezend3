<?php
namespace User\Controller;

use User\Entity\Alunos;
use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Modulo;
use User\Entity\Polo;
use User\Entity\Turma;
use User\Entity\User;
use User\Form\TurmaForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Plugin\FlashMessenger;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class TurmaController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \User\Service\TurmaManager
     */
    private $turmaManger;
    /**
     * @var TurmaForm
     */
    private $turmaForm;

    private $users;

    private $entity;
    /**
     * @var
     */
    private $autentication;

    private $route;
    private $controller;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $turmaManger, $turmaForm, $autentication)
    {
        $this->entityManager    = $entityManager;
        $this->turmaManger      = $turmaManger;
        $this->turmaForm        = $turmaForm;
        $this->route            = "admin/default";
        $this->controller       = "turma";
        $this->entity           = Turma::class;
        $this->users            = User::class;
        $this->autentication    = $autentication;

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

        $isTeacher = $identity->getIsteacher();



       if($funcao == 'Administrador') {

           $posts = $this->entityManager->getRepository($this->entity)
               ->findBy([],['titulo' => 'DESC']);

           // Render the view template
           return new ViewModel([
               'dados' => $posts,
               'postManager' => $this->turmaManger,
           ]);


       }elseif ($funcao == 'Coordenador'){

           $posts = $this->entityManager->getRepository($this->entity)
               ->findBy(['coordenador' => $id ]);


           // Render the view template
           return new ViewModel([
               'dados' => $posts,
               'postManager' => $this->turmaManger,
           ]);

       }

       elseif ($isTeacher == '1'){


           $localizaProfessor = $this->entityManager->getRepository(Cronograma::class)
               ->findBy(['professor' => $id ]);

           /**
            * @var  \User\Entity\Cronograma $value
            */
           foreach ($localizaProfessor as $item => $value){}


           $posts = $this->entityManager->getRepository($this->entity)
               ->findBy(['id' => $value->getTurma() ]);


           // Render the view template
           return new ViewModel([
               'dados' => $posts,
               'postManager' => $this->turmaManger,
           ]);

       }
    }

    public function newAction()
    {
        // Create the form.
        $form =  $this->turmaForm;

        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {

            //verifica se imagem foi postado
            $file = $this->getRequest()->getFiles()->toArray();

            if ($file['file']['error'] == '0') {

                $request = $this->getRequest();
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                // Pass data to form
                $form->setData($data);

                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();
                    //
                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    $dados = $request->getPost()->toArray();
                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico

                    // Add user.
                    $return = $this->turmaManger->insert($dados);

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);


                }

            } else {

                // Get POST data.
                $datas = $this->params()->fromPost();

                // Fill form with data.
                $form->setData($datas);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                if ($form->isValid()) {

                    // Use post manager service to add new post to database.
                    $return = $this->turmaManger->insert($this->getRequest()->getPost()->toArray());

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                    // Redirect the user to "index" page.
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }
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
        $form = $this->turmaForm;

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

                // Pass data to form
                $form->setData($data);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $file = './public/media/' .$dataBase->getFile();

                    if (!is_dir($file)) {

                    unlink($file);

                    }else{}

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    $dados = $request->getPost()->toArray();

                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico


                    // Add user.
                    $return = $this->turmaManger->update($dataBase, $dados);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller,'action'=>'edit', 'id'=> $return->getId()]);


                }

            }else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();

                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    // Update the user.
                    $return = $this->turmaManger->update($dataBase, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }

            }
        } else {

            $form->setData([
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'inicio'        => $dataBase->getInicio(),
                'termino'       => $dataBase->getTermino(),
                'status'        => $dataBase->getStatus(),
                'texto'         => $dataBase->getTexto(),
                'file'          => $dataBase->getFile(),
                'coordenador'   => $dataBase->getCoordenador(),
                'polo'          => $dataBase->getPolo()->getId(),
                'telefone'      => $dataBase->getTelefone(),
                'endereco'      => $dataBase->getEndereco(),

            ]);
        }


        return new ViewModel(array(
            'dataBase'          => $dataBase,
            'form'              => $form,
            'id'                => $id,
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

        if($id != '1'){

            $alunoTurma = $this->entityManager->getRepository(Alunos::class)
                ->findBy(['turma' => $id]);

            $alunos = count($alunoTurma);

            if($alunos != '1'){


                $file = $user->getFile();
                if (!empty($file)) {

                    unlink('./public/media/' . $file);

                }
                $this->turmaManger->delete($user);
                $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');
                return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

            }else{

                $this->flashMessenger()->addErrorMessage('Esta Turma não pode ser deletada, porque possui aluno(s) cadastrado(s)!');

                return $this->redirect()->toRoute($this->route, ['controller' =>$this->controller]);

            }

        }else {

            $this->flashMessenger()->addErrorMessage('Esta Turma não pode ser deletada!');

            return $this->redirect()->toRoute($this->route, ['controller' =>$this->controller]);


        }

    }

    public function delimageAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $user = $this->entityManager->getRepository(Turma::class)
            ->find($id);


        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $file = $user->getFile();

        $data['polo'] = $user->getPolo()->getId();


        if(!empty($file)){
            unlink('public/media/'.$file);
        }

        $data['file'] = "";


        $return = $this->turmaManger->update($user, $data);

        if($return) {
            return new JsonModel([
                'data' => true,
            ]);
        }

    }

//    public function alunoAction(){
//
//        $id = (int)$this->params()->fromRoute('id', -1);
//        if ($id<1) {
//            $this->getResponse()->setStatusCode(404);
//            return;
//        }
//
//        $turma = $this->entityManager->getRepository($this->entity)
//            ->findBy(['id' => $id]);
//
//
//        $alunos = $this->entityManager->getRepository($this->users)
//            ->findBy(['turma'  => $id], ['fullName' => 'ASC']);
//
//        if ($alunos == null) {
//            $this->flashMessenger()->addErrorMessage('Sem Alunos para esta Turma!');
//            // Redirect to "view" page
//            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
//        }
//
//
//        return new ViewModel(array(
//            'alunos'    => $alunos,
//            'turma'     => $turma,
//
//        ));
//    }

    public function alunosAction(){

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        $identity = $this->autentication->getIdentity();
        $funcao = $identity->getRole();

        $turma = $this->entityManager->getRepository($this->entity)
            ->findBy(['id' => $id]);

        $alunos = $this->entityManager->getRepository(Alunos::class)
            ->findBy(['turma' => $id]);

        $qtdAlunosParaTurma = count($alunos);

        $listarAlunos = $this->entityManager->getRepository(User::class)
            ->findBy(['status'=> User::STATUS_ACTIVE],['fullName' => 'ASC']);



        return new ViewModel(array(
            'alunos'    => $alunos,
            'turma'     => $turma,
            'listarAlunos' => $listarAlunos,
            'qtdalunosturma' => $qtdAlunosParaTurma,

        ));
    }

}
