<?php
namespace User\Controller;

use User\Entity\Alunos;
use User\Entity\Anexo;
use User\Entity\Atividade;
use User\Entity\Aula;
use User\Entity\Cronograma;
use User\Entity\Link;
use User\Entity\Material;
use User\Entity\Modulo;
use User\Entity\Trabalho;
use User\Entity\Turma;
use User\Entity\User;
use User\Entity\Video;
use User\Form\TrabalhoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class CourseController extends AbstractActionController
{
    /**
     *
     * $query instanceof QueryBuilder
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;

    private $form;

    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->entity = Modulo::class;
        $this->route = 'admin/default';
        $this->authService = $authService;
    }

    public function indexAction()
    {
        if ($this->authService->getIdentity() !== null) {
            $statusLog = $this->authService->getIdentity()->getStatus();
            $idAluno = $this->authService->getIdentity()->getId();



            if ($statusLog == '1') {

                //--busco os alunos e suas turmas
                $resTurma = $this->entityManager->getRepository(Alunos::class);
                $idTurma = $resTurma->findBy(['usuario' => $idAluno]);


                $arrayModulos = [];
/** @var \User\Entity\Alunos $item */
                foreach ($idTurma as $item){


                    //buscar aulas e modulos para a turma
                    $repository = $this->entityManager->getRepository(Cronograma::class);

                    $query = $repository->createQueryBuilder('p')
                        ->where('p.turma = :turma')
                        ->setParameter('turma', $item->getTurma())
                        ->groupby('p.modulo')
                        ->getQuery();


                    $array = $query->getResult();

                    foreach($array as $resCron) {
                        $arrayModulos[] = $resCron;
                    }

                }



            } else {
                return $this->redirect()->toRoute('login');
            }

        } else {
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel([
            'modulo' => $arrayModulos,
            'turma' => $idTurma,
        ]);
    }

    public function moduloAction()
    {
        $slug = (int) $this->params()->fromQuery('code');
        $turmaUrl = (int) $this->params()->fromQuery('turma');
        $alunoId = $this->authService->getIdentity()->getId();


        //--busco a id do modulo
        $slugModulo = $this->entityManager->getRepository(Modulo::class);
        $idModulo = $slugModulo->findBy(['code' => $slug]);


        if (empty($idModulo)) {

            return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

        /**
         * @var \User\Entity\Modulo $id
         */
        foreach ($idModulo as $idMod => $id) {}


        //--buscar aluno na turma
        //--busco a id do modulo
        $resTurma = $this->entityManager->getRepository(Alunos::class);
        $idTurma = $resTurma->findBy(['usuario' => $alunoId]);

        /**
         * @var  \User\Entity\Alunos $idTurmaDoAluno
         */
        foreach ($idTurma as $turma => $idTurmaDoAluno) {}



        //busco modulo referente a turma do aluno
        $slug = $this->entityManager->getRepository(Cronograma::class);
        $resAulaCron = $slug->findBy(['modulo' => $id->getId(), 'turma' => $idTurmaDoAluno->getTurma()]);

        foreach ($resAulaCron as $aulas => $idModuloNoCronograma) {}





        return new ViewModel([
            'aula' => $resAulaCron,
            'turma' => $turmaUrl,

            'moduloSlug' => $idModulo,

        ]);

    }

    public function aulaAction()
    {

        $code = $this->params()->fromQuery('code');

        //verifica se existe parametro, se não tiver volta a home
        if($code < 1){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

        //verifica se existe parametro, se não tiver volta a home
        $turma = $this->params()->fromQuery('turma');

        if($turma < 1){
            return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

        $videoDestaque = $this->params()->fromQuery('video');


        //buscar aulas no anexo e listar por grupo
        $repositoryCron = $this->entityManager->getRepository(Aula::class);
        $listarIdDaAula = $repositoryCron->findBy(['code' => $code]);

        foreach ($listarIdDaAula as $aula => $resultadoDaAula) {
        }

        // se nao existir a aula ela vai para home
        if (empty($resultadoDaAula)) {

            return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

        //verifica se turma é válida e esta ativa
        $repositoryTurma = $this->entityManager->getRepository(Turma::class);
        $resTurma = $repositoryTurma->findBy(['code' => $turma, 'status' => Turma::STATUS_ACTIVE]);

        if (empty($resTurma)) {

            return $this->redirect()->toRoute('admin/default', ['controller' => 'home']);
        }

        $idAula = $resultadoDaAula->getId();

        // se codigo do video estiver vazio puxo ultimo video cadastrado para destaque
        if(empty($videoDestaque)) {

            //buscar aulas no anexo e listar por grupo
            $repository = $this->entityManager->getRepository(Anexo::class);
            $listarVideo = $repository->findBy(['aula' => $idAula, 'controller' => 'Video']);

            foreach ($listarVideo as $vid => $video) {
            }

            //buscar video destaque
            if (!empty($video)) {

                $repoLink = $this->entityManager->getRepository(Video::class);
                $destaque = $repoLink->findBy(['id' => $video->getAnexo()]);


            } else {

                $destaque = null;

            }
        }else{

            $repoLink = $this->entityManager->getRepository(Video::class);
            $destaque = $repoLink->findBy(['codigo' => $videoDestaque]);

        }

        //end video destaque

        // listar anexos por categoria

        $repository = $this->entityManager->getRepository(Anexo::class);

        //--buscar atividade no anexo e listar--
        $listarAtiv = $repository->findBy(['aula' => $idAula, 'controller' => 'Atividade']);

        $arrayAtiv = [];

        foreach($listarAtiv as $ativ ){

            $atividade = $this->entityManager->getRepository(Atividade::class);
            $res = $atividade->findBy([  'id' => $ativ->getAnexo()]);

            foreach($res as $entity) {
                $arrayAtiv[] = $entity;
            }

        }
        //----end atividade--



        //--buscar material no anexo e listar--
        $listarMat = $repository->findBy(['aula' => $idAula, 'controller' => 'Material']);

        $arrayMat = [];

        foreach($listarMat as $mat ){

            $material = $this->entityManager->getRepository(Material::class);
            $resMat = $material->findBy([  'id' => $mat->getAnexo()]);

            foreach($resMat as $entityMat) {
                $arrayMat[] = $entityMat;
            }

        }
        //----end material--

        //--buscar Link no anexo e listar--
        $listarLink = $repository->findBy(['aula' => $idAula, 'controller' => 'Link']);

        $arrayLink = [];

        foreach($listarLink as $lin ){

            $link = $this->entityManager->getRepository(Link::class);
            $reslink = $link->findBy([  'id' => $lin->getAnexo()]);

            foreach($reslink as $entityLink) {
                $arrayLink[] = $entityLink;
            }

        }
        //----end link--


        //--buscar videos no anexo e listar--
        $listarVideo = $repository->findBy(['aula' => $idAula, 'controller' => 'Video']);

        $arrayVideo = [];

        foreach($listarVideo as $Vid ){

            $video = $this->entityManager->getRepository(Video::class);
            $resVideo = $video->findBy([  'id' => $Vid->getAnexo()]);

            foreach($resVideo as $entityVideo) {
                $arrayVideo[] = $entityVideo;
            }

        }
        //----end video--


        return new ViewModel([

            'aula' => $listarIdDaAula,
            'destaque' => $destaque,
            'turma' => $turma,

            'atividade' => $arrayAtiv,
            'material' => $arrayMat,
            'link' => $arrayLink,
            'videos' => $arrayVideo,

        ]);

    }


}
