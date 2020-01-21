<?php
namespace User\Controller;

use User\Entity\Setup;
use User\Form\SetupForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class SetupController extends AbstractActionController
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


    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $service)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Setup::class;
        $this->controller       = 'setup';
        $this->route            = 'admin/default';
        $this->form             = new SetupForm();
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

            //print_r($file); die;

            if($file['file']['error'] == '0' || $file['background']['error'] == '0' || $file['contrato']['error'] == '0' ){

                $request = $this->getRequest();

                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );

                // Pass data to form
                $form->setData($data);

                //não existe imagem para enviar - tira a validaçao do file do form
                $imageFilter = $form->getInputFilter()->get('file');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('background');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                if($form->isValid()) {

                    // Get filtered and validated data
                    $data = $form->getData();

                    $dados = $request->getPost()->toArray();

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    if(empty($arquivo)){
                        $dados['file'] = $dataBase->getFile();
                    }else{

                        $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico

                        $file = './public/media/' .$dataBase->getFile();

                        if (!is_dir($file)) {
                            unlink($file);
                        }else{}

                    }

                    $background = array_filter(explode(DIRECTORY_SEPARATOR, $data['background']['tmp_name']));

                    if(empty($background)){
                        $dados['background'] = $dataBase->getBackground();
                    }else{

                        $dados['background'] = array_pop($background);

                        $file = './public/media/' .$dataBase->getBackground();

                        if (!is_dir($file)) {
                            unlink($file);
                        }else{}

                    }

                    $contrato = array_filter(explode(DIRECTORY_SEPARATOR, $data['contrato']['tmp_name']));

                    if(empty($contrato)){
                        $dados['contrato'] = $dataBase->getContrato();
                    }else{

                        $dados['contrato'] = array_pop($contrato);

                        $file = './public/media/' .$dataBase->getContrato();

                        if (!is_dir($file)) {
                            unlink($file);
                        }else{}

                    }


                    $senhaHost = $dados['passhost'];
                    if(empty($senhaHost)){
                        $dados['passhost'] = $dataBase->getPassHost();
                    }


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

                $imageFilter = $form->getInputFilter()->get('background');
                $imageFilter->setRequired(false);

                $imageFilter = $form->getInputFilter()->get('contrato');
                $imageFilter->setRequired(false);

                // Validate form
                if ($form->isValid()) {


                    // Get filtered and validated data
                    $dados = $form->getData();

                    $passhost = $dados['passhost'];

                    if(empty($passhost)){
                        $dados['passhost'] = $dataBase->getPassHost();
                    }else{
                        $dados['passhost'] = $passhost;
                    }


                    $dados['file'] = $dataBase->getFile();
                    $dados['background'] = $dataBase->getBackground();
                    $dados['contrato'] = $dataBase->getContrato();


                    $dados['id'] = $dataBase->getId();


                    // Update the user.
                    $return = $this->service->update($dataBase->getId(), $dados);



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
                'fone'          => $dataBase->getFone(),
                'file'          => $dataBase->getFile(),
                'status'        => $dataBase->getStatus(),
                'email'         => $dataBase->getEmail(),
                'background'    => $dataBase->getBackground(),
                'host'          => $dataBase->getHost(),
                'port'          => $dataBase->getPort(),
                'emailhost'     => $dataBase->getEmailhost(),
                'debug'         => $dataBase->getDebug(),
                'security'      => $dataBase->getSecurity(),
                'passhost'      => $dataBase->getPasshost(),
                'contrato'      => $dataBase->getContrato(),
            ]);
        }


        return new ViewModel(array(
            'dados' => $dataBase,
            'form' => $form
        ));
    }


}
