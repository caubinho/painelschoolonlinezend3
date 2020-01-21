<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Cron
 * @ORM\Entity
 * @ORM\Table(name="cronograma")
 * @ORM\HasLifecycleCallbacks
 */
class Cronograma
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Modulo", inversedBy="cronograma")
     * @ORM\JoinColumn(name="modulo_id", referencedColumnName="id")
     */
    protected $modulo;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Turma", inversedBy="cronograma")
     * @ORM\JoinColumn(name="turma_id", referencedColumnName="id")
     */
    protected $turma;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="cronograma")
     * @ORM\JoinColumn(name="professor", referencedColumnName="id")
     */
    protected $professor;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Aula", inversedBy="cronograma")
     * @ORM\JoinColumn(name="aula_id", referencedColumnName="id")
     */

    protected $aula;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inicio", type="date", nullable=true)
     */
    protected $inicio;

    public function __construct($options = array())
    {
        (new Hydrator\ClassMethods)->hydrate($options, $this);


        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Cronograma
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfessor()
    {
        return $this->professor;
    }

    /**
     * @param mixed $professor
     * @return Cronograma
     */
    public function setProfessor($professor)
    {
        $this->professor = $professor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * @param mixed $modulo
     * @return Cronograma
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;
        return $this;
    }



    /**
     * @return int
     */
    public function getTurma()
    {
        return $this->turma;
    }

    /**
     * @param int $turma
     * @return Cronograma
     */
    public function setTurma($turma)
    {
        $this->turma = $turma;
        return $this;
    }

    /**
     * @return int
     */
    public function getAula()
    {
        return $this->aula;
    }

    /**
     * @param int $aula
     * @return Cronograma
     */
    public function setAula($aula)
    {
        $this->aula = $aula;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt() {
        $this->createdAt = new \Datetime("now");
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function setUpdatedAt() {
        $this->updatedAt = new \Datetime("now");
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * @param \DateTime $inicio
     * @return Cronograma
     */
    public function setInicio($inicio)
    {
        $this->inicio = new \Datetime($inicio);
        return $this;
    }

    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'modulo'        => $this->getModulo()->getId(),
            'professor'     => $this->getProfessor(),
            'aula'          => $this->getAula(),
            'turma'         => $this->getTurma(),
            'inicio'        => $this->getInicio(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),

        );

    }
}
