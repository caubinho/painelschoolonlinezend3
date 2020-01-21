<?php
namespace User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;

/**
 * @ORM\Entity
 * This class represents a single post in a blog.
 * @ORM\Entity(repositoryClass="\User\Repository\TurmaRepository")
 * @ORM\Table(name="turma")
 * @ORM\HasLifecycleCallbacks
 */
class Turma
{

    // User status constants.
    const STATUS_ACTIVE         = 1; // Ativo.
    const STATUS_RETIRED        = 2; // Inativo.
    const STATUS_FORMAD         = 3; //FORMADA
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="titulo")
     */
    protected $titulo;

    /**
     * @ORM\Column(name="file")
     */
    protected $file;

    /**
     * @ORM\Column(name="slug")
     */
    protected $slug;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="inicio", type="datetime")
     */
    protected $inicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termino", type="datetime")
     */
    protected $termino;

    /**
     * @ORM\Column(name="texto")
     */
    protected $texto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;


    /**
     * @ORM\Column(name="status")
     */
    protected $status;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime")
     */
    protected $dateUpdated;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Polo", inversedBy="turma")
     * @ORM\JoinColumn(name="polo", referencedColumnName="id")
     */
    protected $polo;


    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="turma")
     * @ORM\JoinColumn(name="coordenador", referencedColumnName="id")
     */
    protected $coordenador;

    /**
     * @ORM\Column(name="telefone")
     */
    protected $telefone;



    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=7, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(name="endereco")
     */
    protected $endereco;



    public function __construct($options = [])
    {
        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->user = new ArrayCollection();


        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

        srand((double)microtime()*1000000);
        $numero = rand(100000,999999);

        $this->code = $numero;
    }

    /**
     * @return mixed
     */
    public function getPolo()
    {
        return $this->polo;
    }

    /**
     * @param mixed $polo
     * @return Turma
     */
    public function setPolo($polo)
    {
        $this->polo = $polo;
        return $this;
    }



    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
            self::STATUS_RETIRED => 'Inativo',
            self::STATUS_FORMAD => 'Formada',
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

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param mixed $titulo
     * @return Turma
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return Turma
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     * @return Turma
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return Turma
     */
    public function setInicio($inicio)
    {
            $this->inicio = new \Datetime($inicio);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTermino()
    {
        return $this->termino;
    }

    /**
     * @param \DateTime $termino
     * @return Turma
     */
    public function setTermino($termino)
    {
        $this->termino = new \Datetime($termino);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param mixed $texto
     * @return Turma
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getCoordenador()
    {
        return $this->coordenador;
    }

    /**
     * @param mixed $coordenador
     * @return Turma
     */
    public function setCoordenador($coordenador)
    {
        $this->coordenador = $coordenador;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * @param mixed $telefone
     * @return Turma
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }

    public function __toString() {
        return $this->titulo;
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
     * @return Turma
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param mixed $endereco
     * @return Turma
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
        return $this;
    }





    
    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'titulo'        => $this->getTitulo(),
            'inicio'        => $this->getInicio(),
            'termino'       => $this->getTermino(),
            'texto'         => $this->getTexto(),
            'file'          => $this->getFile(),
            'slug'          => $this->getSlug(),
            'status'        => $this->getStatus(),
            'date_created'  => $this->getDateCreated(),
            'date_updated'  => $this->getDateUpdated(),
            'coordenador'   => $this->getCoordenador()->getId(),
            'telefone'      => $this->getTelefone(),
            'polo'          => $this->getPolo()->getId(),
            'code'          => $this->getCode(),
            'endereco'      => $this->getEndereco(),

        );

    }







}

