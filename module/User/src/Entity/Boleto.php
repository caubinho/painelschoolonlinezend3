<?php
namespace User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;

/**
 * @ORM\Entity
 * This class represents a single post in a blog.
 * @ORM\Table(name="boleto")
 * @ORM\HasLifecycleCallbacks
 */
class Boleto
{

    // User status constants.
    const STATUS_ACTIVE       = 1; // Pendente.
    const STATUS_RETIRED      = 2; // Inativo.

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;


    /**
     * @ORM\Column(name="file")
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\User", inversedBy="boleto")
     * @ORM\JoinColumn(name="aluno", referencedColumnName="id")
     */
    protected $aluno;

    /**
     * @ORM\ManyToOne(targetEntity="User\Entity\Turma", inversedBy="boleto")
     * @ORM\JoinColumn(name="turma", referencedColumnName="id")
     */
    protected $turma;

    /**
     * @ORM\Column(name="download")
     */
    protected $download;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_download", type="datetime")
     */
    protected $dataDownload;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime")
     */
    protected $dateUpdated;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="vencimento", type="datetime")
     */
    protected $vencimento;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="envio")
     */
    protected $envio;

    /**
     * @ORM\Column(name="texto")
     */
    protected $texto;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=7, nullable=true)
     */
    private $codigo;





    public function __construct($options = [])
    {
        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->user = new ArrayCollection();

        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

        srand((double)microtime()*1000000);
        $numero = rand(100000,999999);

        $this->codigo = $numero;
    }

    /**
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     * @return Boleto
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }


    /**
     * Returns ID of this post.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets ID of this post.
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEnvio()
    {
        return $this->envio;
    }

    /**
     * @param mixed $envio
     * @return Boleto
     */
    public function setEnvio($envio)
    {
        $this->envio = $envio;
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
            self::STATUS_ACTIVE => 'Pendente',
            self::STATUS_RETIRED => 'Pago'
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return Boleto
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAluno()
    {
        return $this->aluno;
    }

    /**
     * @param mixed $aluno
     * @return Boleto
     */
    public function setAluno($aluno)
    {
        $this->aluno = $aluno;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * @param mixed $download
     * @return Boleto
     */
    public function setDownload($download)
    {
        $this->download = $download;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataDownload()
    {
        return $this->dataDownload;
    }

    /**
     * @param \DateTime $dataDownload
     * @return Boleto
     */
    public function setDataDownload($dataDownload)
    {
        $this->dataDownload = new \Datetime($dataDownload);
        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getVencimento()
    {
        return $this->vencimento;
    }

    /**
     * @param \DateTime $vencimento
     * @return Boleto
     */
    public function setVencimento($vencimento)
    {
        $this->vencimento = new \Datetime($vencimento);
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
     * @return Boleto
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
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
     * @return Boleto
     */
    public function setTurma($turma)
    {
        $this->turma = $turma;
        return $this;
    }





    public function getUser(){
        return $this->user;
    }




    
    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'aluno'         => $this->getAluno()->getId(),
            'turma'         => $this->getTurma()->getId(),
            'texto'         => $this->getTexto(),
            'download'      => $this->getDownload(),
            'vencimento'    => $this->getVencimento(),
            'file'          => $this->getFile(),
            'status'        => $this->getStatus(),
            'date_created'  => $this->getDateCreated(),
            'date_updated'  => $this->getDateUpdated(),
            'codigo'        => $this->getCodigo(),
            'envio'         => $this->getEnvio(),

        );

    }

}

