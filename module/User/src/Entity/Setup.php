<?php

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;

/**
 * EntitySetup
 *
 * @ORM\Table(name="setup")
 * @ORM\Entity
 */
class Setup
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
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="fone", type="string", length=10, nullable=true)
     */
    protected $fone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    protected $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="background", type="string", length=255, nullable=true)
     */
    protected $background;
    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, nullable=true)
     */
    protected $host;

    /**
     * @var string
     *
     * @ORM\Column(name="port", type="string", length=255, nullable=true)
     */
    protected $port;

    /**
     * @var string
     *
     * @ORM\Column(name="emailhost", type="string", length=255, nullable=true)
     */
    protected $emailhost;

    /**
     * @var string
     *
     * @ORM\Column(name="passhost", type="string", length=255, nullable=true)
     */
    protected $passhost;

    /**
     * @var string
     *
     * @ORM\Column(name="security", type="string", length=255, nullable=true)
     */
    protected $security;


    /**
     * @var string
     *
     * @ORM\Column(name="debug", type="string", length=255, nullable=true)
     */
    protected $debug;

    /**
     * @var string
     *
     * @ORM\Column(name="contrato", type="string", length=255, nullable=true)
     */
    protected $contrato;


    public function __construct($options = array())
    {
        (new Hydrator\ClassMethods)->hydrate($options, $this);

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Setup
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Setup
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
     * @return Setup
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getFone()
    {
        return $this->fone;
    }

    /**
     * @param string $fone
     * @return Setup
     */
    public function setFone($fone)
    {
        $this->fone = $fone;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Setup
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return Setup
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param string $background
     * @return Setup
     */
    public function setBackground($background)
    {
        $this->background = $background;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Setup
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param string $port
     * @return Setup
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailhost()
    {
        return $this->emailhost;
    }

    /**
     * @param string $emailhost
     * @return Setup
     */
    public function setEmailhost($emailhost)
    {
        $this->emailhost = $emailhost;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasshost()
    {
        return $this->passhost;
    }

    /**
     * @param string $passhost
     * @return Setup
     */
    public function setPasshost($passhost)
    {
        $this->passhost = $passhost;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @param string $security
     * @return Setup
     */
    public function setSecurity($security)
    {
        $this->security = $security;
        return $this;
    }

    /**
     * @return string
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param string $debug
     * @return Setup
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return string
     */
    public function getContrato()
    {
        return $this->contrato;
    }

    /**
     * @param string $contrato
     * @return Setup
     */
    public function setContrato($contrato)
    {
        $this->contrato = $contrato;
        return $this;
    }





    public function toArray(){

        return (new Hydrator\ClassMethods())->extract($this);

    }
    
    


}

