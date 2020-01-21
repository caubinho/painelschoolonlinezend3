<?php
namespace User\Controller;

use User\Entity\Alunos;
use User\Entity\Boleto;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;


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
class ImagemController extends AbstractActionController
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
     * @var
     */
    private $view;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    private $entity;
    private $controller;
    private $route;

    /**
     * Constructor.
     * @param $entityManager
     * @param $userManager
     */
    public function __construct($entityManager, $userManager, $userform, $config, $view, $renderer)
    {
        $this->entityManager    = $entityManager;
        $this->userManager      = $userManager;
        $this->entity           = User::class;
        $this->controller       = 'users';
        $this->route            = 'admin/default';


        $this->userform = $userform;

        $this->config = $config;
        $this->view = $view;

        $this->renderer = $renderer;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */

    public function editAction()
    {
        $id = (int)$this->params()->fromQuery('user', -1);
        $perfil = $this->params()->fromQuery('perfil', -1);


        $this->verifica($id);

        $resperfil = 'false';

        if(empty($perfil)){

            $resperfil = 'false';
        }elseif($perfil == 'true'){
            $resperfil = 'true';
        }


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

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

        } else {


            $form->setData(array(
                    'id'                => $user->getId(),
                    'file'              => $user->getFile(),
                ));
        }

        return new ViewModel(array(
            'id'    => $id,
            'user'  => $user,
            'form'  => $form,
            'perfil' => $resperfil,
        ));
    }

    public function savethumbAction(){

        $id = (int)$this->params()->fromQuery('user', -1);



        $dataBase = $this->entityManager->getRepository(User::class )
            ->find($id);

        if ($this->getRequest()->isPost()) {

        $dadosImagem = $this->entityManager->getRepository(User::class )
            ->findBy([ 'id' => $id]);
        /**
         * @var \User\Entity\User  $dados
         */
        foreach ($dadosImagem as $data => $dados){}

        $databaseThumb = $dados->getThumb();

        if(!empty($databaseThumb)){
            unlink(getcwd().'/public/media/thumb/'.$databaseThumb);
        }

        $fileThumb = 'thumb-'.date('dmYhis').'-'.$dados->getFile();


        $croppedImage = $_FILES['croppedImage'];
        $to_be_upload = $croppedImage['tmp_name'];

        $caminhoArquivo = getcwd().'/public/media/thumb/'.$fileThumb;

        move_uploaded_file ( $to_be_upload , $caminhoArquivo );



            //$file = $this->getRequest()->getFiles()->toArray();

            $request = $this->getRequest();

            $data = $request->getPost()->toArray();

            $data['thumb'] = $fileThumb;

           // print_r($data); die;

            $return = $this->userManager->updateThumb($dataBase, $data  );

            return new JsonModel(['dados' => $return ]);
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

}


