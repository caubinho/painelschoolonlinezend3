<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Anexo
 * @ORM\Entity
 * @ORM\Table(name="anexo_aula")
 * @ORM\HasLifecycleCallbacks
 */

class Anexo
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
     * @ORM\ManyToOne(targetEntity="User\Entity\Aula", inversedBy="anexo")
     * @ORM\JoinColumn(name="aula_id", referencedColumnName="id")
     */
    protected $aula;

    /**
     * @var integer
     *
     * @ORM\Column(name="anexo_id", type="integer", nullable=true)
     */
    protected $anexo;

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
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=255, nullable=true)
     */
    protected $controller;

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
     * @return Anexo
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
     * @return Anexo
     */
    public function setAula($aula)
    {
        $this->aula = $aula;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnexo()
    {
        return $this->anexo;
    }

    /**
     * @param int $anexo
     * @return Anexo
     */
    public function setAnexo($anexo)
    {
        $this->anexo = $anexo;
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
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     * @return Anexo
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }



    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'aula'          => $this->getAula()->getId(),
            'controller'    => $this->getController(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),
            'anexo'         => $this->getAnexo(),

        );

    }

}

