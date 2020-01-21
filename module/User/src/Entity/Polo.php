<?php
namespace User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;

/**
 * This class represents a single post in a blog.
 * @ORM\Entity(repositoryClass="\User\Repository\PoloRepository")
 * @ORM\Table(name="polo")
 * @ORM\HasLifecycleCallbacks
 */
class Polo
{


    // User status constants.
    const STATUS_ACTIVE       = 1; // Ativo.
    const STATUS_RETIRED      = 2; // Inativo.

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="cidade")
     */
    protected $cidade;

    /**
     * @ORM\Column(name="estado")
     */
    protected $estado;


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
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", length=7, nullable=true)
     */
    private $code;



    public function __construct($options = [])
    {

        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->turma = new ArrayCollection();


        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

        srand((double)microtime()*1000000);
        $numero = rand(1000000,9999999);

        $this->code = $numero;

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
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * @param mixed $cidade
     * @return Polo
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
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
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     * @return Polo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }



    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Polo
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }



    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'cidade'        => $this->getCidade(),
            'estado'        => $this->getEstado(),
            'status'        => $this->getStatus(),
            'date_created'  => $this->getDateCreated(),
            'date_updated'  => $this->getDateUpdated(),
            'code'          => $this->getCode(),

        );

    }







}

