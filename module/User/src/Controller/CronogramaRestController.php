<?php

namespace User\Controller;

use User\Entity\Cronograma;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class CronogramaRestController extends AbstractRestfulController
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    /**
     * @var \User\Service\AnexoManager
     */
    private $manager;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $manager)
    {
        $this->entityManager = $entityManager;
        $this->manager = $manager;
        $this->entity = Cronograma::class;
    }

    // Listar - GET
    public function getList()
    {

        $anexo = $this->entityManager->getRepository($this->entity)
            ->findBy([], ['id'=>'ASC']);

        $a = [];


        foreach ($anexo as $user) {
            $a[$user->getId()]['aula'] = $user->getAula()->getTitulo();
            $a[$user->getId()]['turma'] = $user->getTurma()->getTitulo();
            $a[$user->getId()]['modulo'] = $user->getModulo()->getTitulo();
            $a[$user->getId()]['professor'] = $user->getProfessor()->getFullName();
        }

        $result = new JsonModel(array('data' =>$a));

        return $result;

    }

    // Retornar o registro especifico - GET
    public function get($id)
    {

        $repo = $this->entityManager->getRepository($this->entity);

        $data = $repo->find($id)->toArray();

        return new JsonModel(array('data'=>$data));

    }



}
