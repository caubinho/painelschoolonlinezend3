<?php
namespace User\Controller;

use User\Entity\Anexo;
use User\Entity\Atividade;
use User\Form\AtividadeForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class AtividadeController extends AbstractActionController
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


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $service)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Atividade::class;
        $this->controller       = 'atividade';
        $this->route            = 'admin/default';
        $this->form             = new AtividadeForm();
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

                $ext = pathinfo($dados['file'], PATHINFO_EXTENSION);

                $dados['tipo'] = $ext;

               //print_r($dados); die;

                // Add user.
                $return = $this->service->insert($dados);

                $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                // Redirect to "view" page
                $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);


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

                    // print_r($data); die;

                    $file = './public/media/' .$dataBase->getFile();

                    if (!is_dir($file)) {

                        unlink($file);

                    }else{}

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    $dados = $request->getPost()->toArray();

                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico

                    $ext = pathinfo($dados['file'], PATHINFO_EXTENSION);

                    $dados['tipo'] = $ext;


                    // Add user.
                    $return = $this->service->update($dataBase->getId(), $dados);

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

                // Validate form
                if ($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();


                    // Update the user.
                    $return = $this->service->update($dataBase->getId(), $this->getRequest()->getPost()->toArray());

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route,
                        ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }
            }
        } else {
            $form->setData([
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'tipo'          => $dataBase->getTipo(),
                'file'          => $dataBase->getFile(),
                'status'        => $dataBase->getStatus(),
                'texto'         => $dataBase->getTexto(),
            ]);
        }


        return new ViewModel(array(
            'dados' => $dataBase,
            'form' => $form
        ));
    }

    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

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

        $anexo = $this->checaAnexo('Atividade', $id);

        if(empty($anexo)) {

        $file = $user->getFile();

        if (!empty($file)) {

            unlink('./public/atividade/' . $file);

        }
        $this->service->delete($user->getId());

        $this->flashMessenger()->addSuccessMessage('Deletado com sucesso!');
        return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

        }else{

        $this->flashMessenger()->addErrorMessage('Não foi possível excluir existe(m) aulas com esta Atividade anexada!');
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
