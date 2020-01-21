<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\User\Repository\ModuloRepository")
 * @ORM\Table(name="modulo")
 * @ORM\HasLifecycleCallbacks
 */
class Modulo
{

    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Aula", mappedBy="modulo")
     */
    protected $aula;

    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Cronograma", mappedBy="modulo")
     */
    protected $cronograma;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    protected $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;
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
     * @ORM\Column(name="texto", type="text", length=65535, nullable=true)
     */
    protected $texto;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=7, nullable=true)
     */
    private $code;


    public function __construct($options = array())
    {
        (new Hydrator\ClassMethods)->hydrate($options, $this);

        $this->aula = new ArrayCollection();

        $this->createdAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");

        srand((double)microtime()*1000000);
        $numero = rand(100000,999999);

        $this->code = $numero;
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
     * @return Modulo
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Modulo
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param string $titulo
     * @return Modulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Modulo
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Returns status.
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }



    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_RETIRED => 'Inativo'
        ];
    }

    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Unknown';
    }

    /**
     * Sets status.
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     * @return Modulo
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
        return $this;
    }

    public function __toString() {
        return $this->titulo;
    }

    /**
     * @return mixed
     */
    public function getAula()
    {
        return $this->aula;
    }

    public function getCronograma(){
        return $this->cronograma;
    }


    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'titulo'        => $this->getTitulo(),
            'slug'          => $this->getSlug(),
            'texto'         => $this->getTexto(),
            'status'        => $this->getStatus(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),
            'code'          => $this->getCode(),

        );

    }






}

