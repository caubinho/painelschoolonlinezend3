<?php
namespace User\Entity;

use Acl\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Zend\Math\Rand;

/**
 * This class represents a registered user.
/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\User\Repository\AlunosRepository")
 * @ORM\Table(name="turma_alunos")
 * @ORM\HasLifecycleCallbacks
 */

//alunos das turmas
class Alunos
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="alunos")
     * @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Turma", inversedBy="alunos")
     * @ORM\JoinColumn(name="turma", referencedColumnName="id")
     */
    protected $turma;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=false)
     */

    protected $dateUpdated;
        


    public function __construct($options = [])
    {
        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Alunos
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     * @return Alunos
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTurma()
    {
        return $this->turma;
    }

    /**
     * @param mixed $turma
     * @return Alunos
     */
    public function setTurma($turma)
    {
        $this->turma = $turma;
        return $this;
    }


    
    /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }

    public function setDateCreated()
    {
        $this->dateCreated = new \Datetime("now");
    }



    /**
     * @return mixed
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateUpdated()
    {
        $this->dateUpdated =  new \Datetime("now");
        return $this;
    }



    public function toArray()
    {
        return [
            'id'                => $this->getId(),
            'usuario'           => $this->getUsuario()->getId(),
            'turma'             => $this->getTurma()->getId(),
            'date_created'      => $this->getDateCreated(),
            'date_updated'      => $this->getDateUpdated(),

        ];
    }


}



