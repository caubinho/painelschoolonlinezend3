<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trabalho
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\User\Repository\TrabalhoRepository")
 * @ORM\Table(name="trabalho")
 * @ORM\HasLifecycleCallbacks
 */

class Trabalho
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
     * @ORM\ManyToOne(targetEntity="User\Entity\Aula", inversedBy="trabalho")
     * @ORM\JoinColumn(name="aula", referencedColumnName="id")
     */
    protected $aula;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="trabalho")
     * @ORM\JoinColumn(name="aluno", referencedColumnName="id")
     */
    protected $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="trabalho")
     * @ORM\JoinColumn(name="professor", referencedColumnName="id")
     */
    protected $professor;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Turma", inversedBy="trabalho")
     * @ORM\JoinColumn(name="turma", referencedColumnName="id")
     */
    protected $turma;

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
     * @var integer
     *
     * @ORM\Column(name="corrigido")
     */
    protected $corrigido;

    /**
     * @var string
     *
     * @ORM\Column(name="nota", type="decimal")
     */
    protected $nota;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string", nullable=true)
     */
    protected $texto;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    protected $file;

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
     * @return Trabalho
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Trabalho
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
     * @return int
     */
    public function getAluno()
    {
        return $this->aluno;
    }

    /**
     * @param int $aluno
     * @return Trabalho
     */
    public function setAluno($aluno)
    {
        $this->aluno = $aluno;
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
     * @return Trabalho
     */
    public function setTurma($turma)
    {
        $this->turma = $turma;
        return $this;
    }

    /**
     * @return int
     */
    public function getCorrigido()
    {
        return $this->corrigido;
    }

    /**
     * @param int $corrigido
     * @return Trabalho
     */
    public function setCorrigido($corrigido)
    {
        $this->corrigido = $corrigido;
        return $this;
    }

    /**
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * @param string $nota
     * @return Trabalho
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return Trabalho
     */
    public function setFile($file)
    {
        $this->file = $file;
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
     * @return Trabalho
     */
    public function setProfessor($professor)
    {
        $this->professor = $professor;
        return $this;
    }

    /**
     * @return string
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     * @return Trabalho
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
        return $this;
    }






    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'aula'          => $this->getAula()->getId(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),
            'aluno'         => $this->getAluno()->getId(),
            'file'          => $this->getFile(),
            'turma'         => $this->getTurma()->getId(),
            'corrigido'     => $this->getCorrigido(),
            'nota'          => $this->getNota(),
            'professor'     => $this->getProfessor()->getId(),
            'texto'         => $this->getTexto(),

        );

    }

}

