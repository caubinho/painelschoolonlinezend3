<?php
namespace User\Controller;

use User\Entity\Anexo;
use User\Entity\Video;
use User\Form\VideoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;




/**
 * This controller is responsible for user management (adding, editing, 
 * viewing users and changing user's password).
 */
class VideoController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \User\Service\VideoManager
     */
    private $videoManager;

    private $route;
    private $controller;
    private $entity;


    /**
     * Constructor.
     * @param $entityManager
     * @param $videoManager
     * @param \User\entity\Video
     */
    public function __construct($entityManager, $videoManager)
    {
        $this->entityManager    = $entityManager;
        $this->videoManager     = $videoManager;
        $this->entity           = Video::class;
        $this->controller       = 'videos';
        $this->route            = 'admin/default';


    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */
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
        $form = new VideoForm();

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
                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômic

                    // Add user.
                    $return = $this->videoManager->insert($dados);

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

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

                    $return = $this->videoManager->insert($data);

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                    // Redirect to "edit" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $return->getId()]);

                }

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

        $user = $this->entityManager->getRepository(Video::class)
                ->find($id);

        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form =  new VideoForm();

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


                // Pass data to form
                $form->setData($data);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $file = './public/video/' .$user->getFile();

                    if (!is_dir($file)) {
                        unlink($file);
                    }else{}
                    //
                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    $dados = $request->getPost()->toArray();
                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico

                    $dados['link'] = 'http://'.$_SERVER['HTTP_HOST'].'/video/'.$dados['file'];

                    // Update the user.
                    $this->videoManager->update($user, $dados);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, [ 'controller' => $this->controller ,'action' => 'edit', 'id' => $user->getId()]);

                }//end envia imagem

            } else {

                // Fill in the form with POST data
                $data = $this->params()->fromPost();
                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {


                    $data['file'] = $user->getFile();

                    //print_r($data); die;
                    // Update the user.
                    $this->videoManager->update($user, $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, [ 'controller' => $this->controller,
                        'action' => 'edit', 'id' => $user->getId()]);
                }

            }
        } else {


            $form->setData(array(
                    'id'                => $user->getId(),
                    'titulo'            => $user->getTitulo(),
                    'codigo'            => $user->getCodigo(),
                    'status'            => $user->getStatus(),
                    'file'              => $user->getFile(),
                    'texto'             => $user->getTexto(),
                    'link'              => $user->getLink(),
                    'tipo'              => $user->getTipo(),

                ));
        }

        return new ViewModel(array(
            'dados' => $user,
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

        $user = $this->entityManager->getRepository($this->entity)
            ->find($id);


        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $anexo = $this->checaAnexo('Video', $id);

        if(empty($anexo)) {

            $file = $user->getFile();
            if (!empty($file)) {

                unlink('public/video/' . $file);

            }

            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');
            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
        }else{

            $this->flashMessenger()->addErrorMessage('Não foi possível excluir existe(m) aulas com este vídeo anexado!');
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


