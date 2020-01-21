<?php

namespace User;

use Application\Controller\IndexController;
use User\Controller\AlunoController;
use User\Controller\AlunosController;
use User\Controller\AnexoController;
use User\Controller\AtividadeController;
use User\Controller\AulaController;
use User\Controller\CourseController;
use User\Controller\CronogramaRestController;
use User\Controller\CronogramaController;
use User\Controller\Factory\AlunosControllerFactory;
use User\Controller\Factory\AnexoControllerFactory;
use User\Controller\Factory\AtividadeControllerFactory;
use User\Controller\Factory\AulaControllerFactory;
use User\Controller\Factory\CourseControllerFactory;
use User\Controller\Factory\CronogramaRestControllerFactory;
use User\Controller\Factory\CronogramaControllerFactory;
use User\Controller\Factory\ImagemControllerFactory;
use User\Controller\Factory\LinkControllerFactory;
use User\Controller\Factory\MaterialControllerFactory;
use User\Controller\Factory\ModuloControllerFactory;
use User\Controller\Factory\RecoverControllerFactory;
use User\Controller\Factory\SetupControllerFactory;
use User\Controller\Factory\SliderControllerFactory;
use User\Controller\Factory\TrabalhoControllerFactory;
use User\Controller\Factory\VideoControllerFactory;
use User\Controller\ImagemController;
use User\Controller\LinkController;
use User\Controller\MaterialController;
use User\Controller\ModuloController;
use User\Controller\PermissaoController;
use User\Controller\RecoverController;
use User\Controller\SetupController;
use User\Controller\SliderController;
use User\Controller\TrabalhoController;
use User\Controller\VideoController;
use User\Entity\Alunos;
use User\Entity\User;
use User\Form\CronogramaForm;
use User\Form\Factory\CronogramaFormFactory;
use User\Form\Factory\ModuloFormFactory;
use User\Form\Factory\PoloFormFactory;
use User\Form\Factory\RegisterFormFactory;
use User\Form\Factory\TrabalhoFormFactory;
use User\Form\ModuloForm;
use User\Form\PoloForm;
use User\Form\RegisterForm;
use User\Form\TrabalhoForm;
use User\Mail\Mail;
use User\Mail\Factory\MailFactory;
use User\Service\AlunosManager;
use User\Service\AnexoManager;
use User\Service\AtividadeManager;
use User\Service\AulaManager;
use User\Service\BoletoManager;
use User\Controller\Factory\PoloControllerFactory;
use User\Controller\Factory\TurmaControllerFactory;
use User\Controller\Factory\AlunoControllerFactory;
use User\Controller\Factory\AuthControllerFactory;
use User\Controller\AuthController;
use User\Controller\Factory\BoletoControllerFactory;
use User\Controller\Factory\HomeControllerFactory;
use User\Controller\Factory\UserControllerFactory;
use User\Controller\PoloController;
use User\Form\BoletoForm;
use User\Form\Factory\BoletoFormFactory;
use User\Form\Factory\TurmaFormFactory;
use User\Form\Factory\UserFormFactory;
use User\Form\TurmaForm;
use User\Form\UserForm;
use User\Service\CronogramaManager;
use User\Service\Factory\AlunosManagerFactory;
use User\Service\Factory\AnexoManagerFactory;
use User\Service\Factory\AtividadeManagerFactory;
use User\Service\Factory\AulaManagerFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use User\Service\Factory\BoletoManagerFactory;
use User\Service\Factory\CronogramaManagerFactory;
use User\Service\Factory\LinkManagerFactory;
use User\Service\Factory\MaterialManagerFactory;
use User\Service\Factory\ModuloManagerFactory;
use User\Service\Factory\PoloManagerFactory;
use User\Service\Factory\SetupManagerFactory;
use User\Service\Factory\SliderManagerFactory;
use User\Service\Factory\TrabalhoManagerFactory;
use User\Service\Factory\TurmaManagerFactory;
use User\Service\Factory\UserManagerFactory;
use User\Service\Factory\VideoManagerFactory;
use User\Service\LinkManager;
use User\Service\MaterialManager;
use User\Service\ModuloManager;
use User\Service\PoloManager;
use User\Service\SetupManager;
use User\Service\SliderManager;
use User\Service\TrabalhoManager;
use User\Service\TurmaManager;
use User\Service\UserManager;
use User\Service\VideoManager;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Factory\InvokableFactory;

class Module implements ConfigProviderInterface, ServiceProviderInterface, ControllerProviderInterface
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $container    = $e->getApplication()->getServiceManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH,
            function (MvcEvent $e) use ($container) {
                $match = $e->getRouteMatch();
                $authService = $container->get(AuthenticationServiceInterface::class);
                $routeName = $match->getMatchedRouteName();
                $em = $container->get(\Doctrine\ORM\EntityManager::class);

                /* Get Controller and Action */
                $matchedController = $match->getParam('controller');
                $matchedAction = $match->getParam('action');

                /* Default Role */
                //$role = 'Visitante';


                    $rotaAtual =  $routeName;
                    $array1Rota = explode('/', $rotaAtual);
                    $rota1  = $array1Rota[0];


                /* Check if user exists, if it has authenticated and set role */
                if ($authService->hasIdentity()) {

                    $user = $em->getReference(User::class, $authService->getIdentity()->getId());
                    

                    if (is_object($user)) {

                        $role = $user->getRole()->getNome();


                        /* Valid ACL */
                        $acl = $container->get(\Acl\Permissions\Acl::class);

                        if ($acl->isAllowed($role, $matchedController, $matchedAction)) {

                            if ($role == 'Anônimo') {

                                $match->setParam('controller', AuthController::class)
                                    ->setParam('action', 'login');
                            }
                            
                        }else {


                            $url = $e->getRouter()->assemble(array(), array('name' => 'admin/default'));

                            $response = $e->getResponse();
                            $response->getHeaders()->addHeaderLine('Location', $url.'/'.'permissao');
                            // The HTTP response status code 302 Found is a common way of performing a redirection.
                            // http://en.wikipedia.org/wiki/HTTP_302
                            $response->setStatusCode(302);
                            $response->sendHeaders();
                            exit;
                    }

                    } else {
                        $match->setParam('controller', AuthController::class)
                            ->setParam('action', 'logout');
                    }

                }elseif(strpos($rota1, 'admin') !== false){

                    $match->setParam('controller', AuthController::class)
                        ->setParam('action', 'login');

                }elseif(strpos($rota1, 'acl') !== false){

                    $match->setParam('controller', AuthController::class)
                        ->setParam('action', 'login');
                }else{



                }

            }, 100);
    }

    public function getConfig()
    {
        return include __DIR__ . "/../config/module.config.php";
    }

    public function getServiceConfig()
    {
        return [

            'aliases' => [
                AuthenticationService::class => AuthenticationServiceInterface::class
            ],

            'factories' => [
                AuthenticationServiceInterface::class => AuthenticationServiceFactory::class,

                PoloManager::class              => PoloManagerFactory::class,
                PoloController::class           => PoloControllerFactory::class,

                TurmaManager::class             => TurmaManagerFactory::class,
                TurmaForm::class                => TurmaFormFactory::class,

                AlunosManager::class            => AlunosManagerFactory::class,

                ModuloManager::class            => ModuloManagerFactory::class,
                ModuloForm::class               => ModuloFormFactory::class,


                BoletoManager::class            => BoletoManagerFactory::class,
                BoletoForm::class               => BoletoFormFactory::class,

                UserManager::class              => UserManagerFactory::class,
                UserForm::class                 => UserFormFactory::class,

                Mail::class                     => MailFactory::class,

                VideoManager::class             => VideoManagerFactory::class,
                LinkManager::class              => LinkManagerFactory::class,
                MaterialManager::class          => MaterialManagerFactory::class,
                AtividadeManager::class         => AtividadeManagerFactory::class,

                AulaManager::class              => AulaManagerFactory::class,
                AnexoManager::class             => AnexoManagerFactory::class,
                
                CronogramaManager::class        => CronogramaManagerFactory::class,
                CronogramaForm::class           => CronogramaFormFactory::class,

                SetupManager::class             => SetupManagerFactory::class,

                TrabalhoManager::class          => TrabalhoManagerFactory::class,
                TrabalhoForm::class             => TrabalhoFormFactory::class,


                SliderManager::class            => SliderManagerFactory::class,

                RegisterForm::class             => RegisterFormFactory::class,


            ]
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                AuthController::class                   => AuthControllerFactory::class,
                Controller\UserController::class        => UserControllerFactory::class,
                Controller\HomeController::class        => HomeControllerFactory::class,
                Controller\PoloController::class        => PoloControllerFactory::class,
                Controller\TurmaController::class       => TurmaControllerFactory::class,
                AlunosController::class                 => AlunosControllerFactory::class,
                Controller\BoletoController::class      => BoletoControllerFactory::class,
                ModuloController::class                 => ModuloControllerFactory::class,
                VideoController::class                  => VideoControllerFactory::class,
                LinkController::class                   => LinkControllerFactory::class,
                MaterialController::class               => MaterialControllerFactory::class,
                AtividadeController::class              => AtividadeControllerFactory::class,
                AulaController::class                   => AulaControllerFactory::class,
                AnexoController::class                  => AnexoControllerFactory::class,
                CronogramaRestController::class         => CronogramaRestControllerFactory::class,
                CourseController::class                 => CourseControllerFactory::class,
                PermissaoController::class              => InvokableFactory::class,
                SetupController::class                  => SetupControllerFactory::class,
                CronogramaController::class             => CronogramaControllerFactory::class,
                TrabalhoController::class               => TrabalhoControllerFactory::class,
                SliderController::class                 => SliderControllerFactory::class,
                ImagemController::class                 => ImagemControllerFactory::class



            ],

            'aliases' => [
                'auth'          => AuthController::class,
                'login'         => Controller\AuthController::class,
                'home'          => Controller\HomeController::class,
                'polo'          => Controller\PoloController::class,
                'turma'         => Controller\TurmaController::class,
                'users'         => Controller\UserController::class,
                'alunos'        => AlunosController::class,

                'boleto'        => Controller\BoletoController::class,
                'modulos'       => ModuloController::class,
                'videos'        => VideoController::class,
                'link'          => LinkController::class,
                'material'      => MaterialController::class,
                'atividade'     => AtividadeController::class,
                'aula'          => AulaController::class,
                'anexo'         => AnexoController::class,
                'cronograma'    => CronogramaController::class,
                'course'        => CourseController::class,
                'permissao'     => PermissaoController::class,
                'setup'         => SetupController::class,
                'trabalho'      => TrabalhoController::class,
                'banner'        => SliderController::class,
                'imagem'        => ImagemController::class


            ]

        ];
    }

}