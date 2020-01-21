<?php
namespace User\Controller;

use User\Entity\Anexo;
use User\Entity\Atividade;
use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Link;
use User\Entity\Material;
use User\Entity\Video;
use User\Form\AulaForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class AulaController extends AbstractActionController
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

    private $form;
    private $controller;
    private $route;
    private $entity;
    private $cronograma;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $service)
    {
        $this->entityManager    = $entityManager;
        $this->service          = $service;
        $this->entity           = Aula::class;
        $this->controller       = 'aula';
        $this->route            = 'admin/default';
        $this->form             = new AulaForm();
        $this->cronograma       = Cronograma::class;
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

                    // print_r($data); die

                    $arquivo = array_filter(explode(DIRECTORY_SEPARATOR, $data['file']['tmp_name']));

                    $dados = $request->getPost()->toArray();

                    $dados['file'] = array_pop($arquivo);//Nome do arquivo randômico


                    // Add user.
                    $return = $this->service->insert($dados);

                    $this->flashMessenger()->addSuccessMessage('Cadastro com sucesso!');
                    // Redirect to "view" page
                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action'=>'edit', 'id'=> $return->getId()]);


                }

            }else {

            // Get POST data.
            $data = $this->params()->fromPost();

            $imageFilter = $form->getInputFilter()->get('file');
            $imageFilter->setRequired(false);

            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {

                // Get validated form data.
                //$data = $form->getData();

                // Use post manager service to add new post to database.
                $return = $this->service->insert($this->getRequest()->getPost()->toArray());

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

        //buscar Anexox ref. a aula
        $ListarAnexos = $this->entityManager->getRepository(Anexo::class);

        /*--listar videos da aula----------------------------------------------------*/
        $resVideoAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Video'], null, null, null);

        //buscar todos Videos ativos para comparar
        $ListarVideos = $this->entityManager->getRepository(Video::class);
        $resVideos = $ListarVideos->findBy(['status' => '1'], null, null, null);

        /*--end videos----------------------------------------------------------------*/

        /*--listar Atividades da aula----------------------------------------------------*/
        $resAtividadeAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Atividade'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllAtividade = $this->entityManager->getRepository(Atividade::class);
        $resAtividades = $ListAllAtividade->findBy(['status' => '1'], null, null, null);

        /*--end atividade----------------------------------------------------------------*/

        /*--listar Material da aula----------------------------------------------------*/
        $resMaterialAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Material'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllMaterial = $this->entityManager->getRepository(Material::class);
        $resMaterial = $ListAllMaterial->findBy(['status' => '1'], null, null, null);

        /*--end material----------------------------------------------------------------*/

        /*--listar Links da aula----------------------------------------------------*/
        $resLinkAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Link'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllLink = $this->entityManager->getRepository(Link::class);
        $resLink = $ListAllLink->findBy(['status' => '1'], null, null, null);

        /*--end material----------------------------------------------------------------*/


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

                    $data['file'] =  $dataBase->getFile();

                    // Update the user.
                    $dados = $this->service->update($dataBase->getId(), $data);

                    $this->flashMessenger()->addSuccessMessage('Alterado com sucesso!');

                    return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $dados->getId()]);

                }
            }

        }else {

            $form->setData(array(
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'tipo'          => $dataBase->getTipo(),
                'status'        => $dataBase->getStatus(),
                'texto'         => $dataBase->getTexto(),
                'file'          => $dataBase->getFile(),

            ));
        }

        return new ViewModel(array(
            'dataBase'              => $dataBase,
            'form'                  => $form,

            'videoAnexado'          => $resVideoAnexado ,
            'resVideos'             => $resVideos,

            'resAtividadeAnexado'   => $resAtividadeAnexado,
            'resAtividades'         => $resAtividades,
            
            'resMaterialAnexado'    => $resMaterialAnexado,
            'resMaterial'           => $resMaterial,

            'resLinkAnexado'        => $resLinkAnexado,
            'resLink'               => $resLink,

        ));
    }

    public function viewAction()
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

        //buscar Anexox ref. a aula
        $ListarAnexos = $this->entityManager->getRepository(Anexo::class);

        /*--listar videos da aula----------------------------------------------------*/
        $resVideoAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Video'], null, null, null);

        //buscar todos Videos ativos para comparar
        $ListarVideos = $this->entityManager->getRepository(Video::class);
        $resVideos = $ListarVideos->findBy(['status' => '1'], null, null, null);

        /*--end videos----------------------------------------------------------------*/

        /*--listar Atividades da aula----------------------------------------------------*/
        $resAtividadeAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Atividade'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllAtividade = $this->entityManager->getRepository(Atividade::class);
        $resAtividades = $ListAllAtividade->findBy(['status' => '1'], null, null, null);

        /*--end atividade----------------------------------------------------------------*/

        /*--listar Material da aula----------------------------------------------------*/
        $resMaterialAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Material'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllMaterial = $this->entityManager->getRepository(Material::class);
        $resMaterial = $ListAllMaterial->findBy(['status' => '1'], null, null, null);

        /*--end material----------------------------------------------------------------*/

        /*--listar Links da aula----------------------------------------------------*/
        $resLinkAnexado = $ListarAnexos->findBy(['aula' => $id, 'controller' => 'Link'], null, null, null);

        //buscar todos videos ativos para comparar
        $ListAllLink = $this->entityManager->getRepository(Link::class);
        $resLink = $ListAllLink->findBy(['status' => '1'], null, null, null);

        /*--end material----------------------------------------------------------------*/


        // Create user form
        $form = $this->form;


            $form->setData(array(
                'id'            => $dataBase->getId(),
                'titulo'        => $dataBase->getTitulo(),
                'tipo'          => $dataBase->getTipo(),
                'status'        => $dataBase->getStatus(),
                'texto'         => $dataBase->getTexto(),
                'file'          => $dataBase->getFile(),

            ));


        return new ViewModel(array(
            'dataBase'              => $dataBase,
            'form'                  => $form,

            'videoAnexado'          => $resVideoAnexado ,
            'resVideos'             => $resVideos,

            'resAtividadeAnexado'   => $resAtividadeAnexado,
            'resAtividades'         => $resAtividades,

            'resMaterialAnexado'    => $resMaterialAnexado,
            'resMaterial'           => $resMaterial,

            'resLinkAnexado'        => $resLinkAnexado,
            'resLink'               => $resLink,

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

        $buscaAula = $this->entityManager->getRepository($this->cronograma)
            ->findBy(['aula' => $id]);

        $contaAula = count($buscaAula);

        if($contaAula != 0) {

            $this->flashMessenger()->addErrorMessage('Esta aula esta cadastrada para alguma turma. Necessário exclui-la da turma antes.');

            return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);

            
        }else {


            $anexo = $this->entityManager->getRepository(Anexo::class)
                ->findBy(['aula' => $id]);

            if (!empty($anexo)) {

                $this->flashMessenger()->addSuccessMessage('Exclua todos elementos anexados a Aula!');

                return $this->redirect()->toRoute($this->route, ['controller' => $this->controller, 'action' => 'edit', 'id' => $id]);

            } else {

                $this->service->delete($user->getId());

                $this->flashMessenger()->addSuccessMessage('Excluido com sucesso!');

                return $this->redirect()->toRoute($this->route, ['controller' => $this->controller]);
            }

        }

    }



}
