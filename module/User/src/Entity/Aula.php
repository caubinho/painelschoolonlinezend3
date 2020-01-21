<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Aula
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="\User\Repository\AulaRepository")
 * @ORM\Table(name="aula")
 * @ORM\HasLifecycleCallbacks
 */
class Aula
{

       // User status constants.
    const STATUS_ACTIVE       = 1; // Ativo.
    const STATUS_RETIRED      = 2; // Inativo.

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     */
    protected $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    protected $file;


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
     * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
     */
    protected $tipo;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $texto;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=7, nullable=true)
     */
    private $code;


    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Cronograma", mappedBy="aula")
     */
    protected $cronograma;


    public function __construct($options = array())
    {
        (new Hydrator\ClassMethods)->hydrate($options, $this);

        $this->cronograma = new ArrayCollection();

         $this->video = new ArrayCollection();

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
     * @return Aula
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
     * @return Aula
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
     * @return Aula
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
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
     * @return Aula
     */
    public function setFile($file)
    {
        $this->file = $file;
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
     * @return Aula
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
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     * @return Aula
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
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
     * @return Aula
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
    public function getCronograma()
    {
        return $this->cronograma;
    }

    

    

    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'titulo'        => $this->getTitulo(),
            'slug'          => $this->getSlug(),
            'status'        => $this->getStatus(),
            'tipo'          => $this->getTipo(),
            'texto'         => $this->getTexto(),
            'created_at'    => $this->getCreatedAt(),
            'updated_at'    => $this->getUpdatedAt(),
            'code'          => $this->getCode(),
            'file'          => $this->getFile(),
        
        );

    }






}

