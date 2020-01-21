<?php
namespace User\Controller;

use User\Entity\Polo;
use User\Entity\Slider;
use User\Entity\Turma;
use User\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * This is the main controller class of the User Demo application. It contains
 * site-wide actions such as Home or About.
 */
class HomeController extends AbstractActionController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * Home page.
     */
    public function indexAction()
    {

        //contar alunos
        $alunos = $this->entityManager->getRepository(User::class)
            ->findBy(['role'=>'2']);
        $countAlunos = count($alunos);
        /**-------**/

        //contar homens
        $masculino = $this->entityManager->getRepository(User::class)
            ->findBy(['sexo'=>'Masculino', 'status' => '1']);
        $countMasculino = count($masculino);
        /**-------**/

        //contar homens
        $feminino = $this->entityManager->getRepository(User::class)
            ->findBy(['sexo'=>'Feminino', 'status' => '1']);
        $countFeminino = count($feminino);
        /**-------**/

        //contar homens
        $polo = $this->entityManager->getRepository(Polo::class)
            ->findBy(['status' => '1']);
        $countPolo = count($polo);
        /**-------**/

        //contar homens
        $turma = $this->entityManager->getRepository(Turma::class)
            ->findBy(['status' => '1']);
        $countTurma = count($turma);
        /**-------**/

        $banner = $this->entityManager->getRepository(Slider::class)
            ->findBy(['status' => Slider::STATUS_ACTIVE]);


        return new ViewModel([
            'countAlunos'       => $countAlunos,
            'countMasculino'    => $countMasculino,
            'countFeminino'     => $countFeminino,
            'countPolo'         => $countPolo,
            'countTurma'         => $countTurma,
            'banner'            => $banner,

        ]);
    }

 
}

