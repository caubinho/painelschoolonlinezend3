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
 * @ORM\Entity(repositoryClass="\User\Repository\UserRepository")
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\HasLifecycleCallbacks
 */
class User 
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.

    
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="isteacher", type="integer",  nullable=false)
     */
    protected $isteacher;

    /**
     * @var \Acl\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Acl\Entity\Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=false)
     */
    private $codigo;

    /** 
     * @ORM\Column(name="email")  
     */
    protected $email;
    
    /** 
     * @ORM\Column(name="full_name")  
     */
    protected $fullName;

    /**
     * @ORM\Column(name="profissao")
     */
    protected $profissao;

    /**
     * @ORM\Column(name="contrato")
     */
    protected $contrato;


    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Boleto", mappedBy="user")
     */
    protected $boleto;

    /**
     * @ORM\OneToMany(targetEntity="User\Entity\Cronograma", mappedBy="user")
     */
    protected $cronograma;

    /** 
     * @ORM\Column(name="password")  
     */
    protected $password;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=false)
     */
    private $salt;


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
        
    /**
     * @ORM\Column(name="pwd_reset_token")  
     */
    protected $passwordResetToken;
    
    /**
     * @ORM\Column(name="pwd_reset_token_creation_date")  
     */
    protected $passwordResetTokenCreationDate;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_key", type="string", length=255, nullable=false)
     */
    protected $activationKey;

    /**
     * @var string
     *
     * @ORM\Column(name="file")
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="thumb")
     */
    protected $thumb;


    /**
     * @var string
     *
     * @ORM\Column(name="cpf", type="string", length=255, nullable=false)
     */
    protected $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="rua", type="string", length=255, nullable=false)
     */
    protected $rua;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero", type="integer",  nullable=false)
     */
    protected $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="bairro", type="string", length=255, nullable=false)
     */
    protected $bairro;

    /**
     * @var string
     *
     * @ORM\Column(name="complemento", type="string", length=255, nullable=false)
     */
    protected $complemento;

    /**
     * @var string
     *
     * @ORM\Column(name="cidade", type="string", length=255, nullable=false)
     */
    protected $cidade;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=false)
     */
    protected $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="cep", type="string", length=255, nullable=false)
     */
    protected $cep;

    /**
     * @var string
     *
     * @ORM\Column(name="rg", type="string", length=255, nullable=false)
     */
    protected $rg;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255, nullable=false)
     */
    protected $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="celular2", type="string", length=255, nullable=false)
     */
    protected $celular2;

    /**
     * @var string
     *
     * @ORM\Column(name="telefone", type="string", length=255, nullable=false)
     */
    protected $telefone;

    /**
     * @var \User\Entity\Polo
     *
     * @ORM\ManyToOne(targetEntity="User\Entity\Polo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="polo", referencedColumnName="id", nullable=false)
     * })
     */
    private $polo;

    /**
     * @var string
     *
     * @ORM\Column(name="sexo", type="string", length=255, nullable=false)
     */
    protected $sexo;

    /**
     * @var string
     *
     * @ORM\Column(name="pai", type="string", length=255, nullable=false)
     */
    protected $pai;

    /**
     * @var string
     *
     * @ORM\Column(name="mae", type="string", length=255, nullable=false)
     */
    protected $mae;

    /**
     * @var string
     *
     * @ORM\Column(name="naturalidade", type="string", length=255, nullable=false)
     */
    protected $naturalidade;


    /**
     * @var string
     *
     * @ORM\Column(name="nascimento", type="text")
     */
    private $nascimento;


    /**
     * @var string
     *
     * @ORM\Column(name="emissor", type="string")
     */
    protected $emissor;

    /**
     * @var string
     *
     * @ORM\Column(name="recado", type="string")
     */
    protected $recado;
    /**
     * @var string
     *
         * @ORM\Column(name="formacao", type="text")
     */
    protected $formacao;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string")
     */
    protected $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_endereco", type="string")
     */
    protected $empresaendereco;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_numero", type="string")
     */
    protected $empresanumero;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_complemento", type="string")
     */
    protected $empresacomplemento;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_bairro", type="string")
     */
    protected $empresabairro;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_cidade", type="string")
     */
    protected $empresacidade;

    /**
     * @var string
     *
     * @ORM\Column(name="empresaestado", type="string")
     */
    protected $empresaestado;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_cep", type="string")
     */
    protected $empresacep;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_tel", type="string")
     */
    protected $empresatel;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_ramal", type="string")
     */
    protected $empresaramal;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_cel", type="string")
     */
    protected $empresacel;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string")
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa_email", type="string")
     */
    protected $empresaemail;




    public function __construct($options = [])
    {
        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

        $this->salt = base64_encode(Rand::getBytes(8, true));
        $this->activationKey = md5($this->email . $this->salt);

        $this->boleto = new ArrayCollection();



        srand((double)microtime()*1000000);
        $numero = rand(100000,999999);

        $this->codigo = $numero;


    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }



    /**
     * Returns user ID.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets user ID. 
     * @param int $id    
     */
    public function setId($id) 
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContrato()
    {
        return $this->contrato;
    }

    /**
     * @param mixed $contrato
     * @return User
     */
    public function setContrato($contrato)
    {
        $this->contrato = $contrato;
        return $this;
    }



    /**
     * @return int
     */
    public function getIsteacher()
    {
        return $this->isteacher;
    }

    /**
     * @param int $isteacher
     * @return User
     */
    public function setIsteacher($isteacher)
    {
        $this->isteacher = $isteacher;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     * @return User
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }



    /**
     * Returns email.     
     * @return string
     */
    public function getEmail() 
    {
        return $this->email;
    }



    /**
     * Sets email.     
     * @param string $email
     */
    public function setEmail($email) 
    {
        $this->email = $email;
    }
    
    /**
     * Returns full name.
     * @return string     
     */
    public function getFullName() 
    {
        return $this->fullName;
    }       

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setFullName($fullName) 
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    public function getProfissao()
    {
        return $this->profissao;
    }

    /**
     * @param mixed $profissao
     * @return User
     */
    public function setProfissao($profissao)
    {
        $this->profissao = $profissao;
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
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
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
     * Returns password.
     * @return string
     */
    public function getPassword() 
    {
       return $this->password; 
    }
    
    /**
     * Sets password.     
     * @param string $password
     */
    public function setPassword($password) 
    {
        $this->password = $password;
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
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * @param mixed $passwordResetToken
     * @return User
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
        return $this;
    }
    

    
    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }
    
    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date) 
    {
        $this->passwordResetTokenCreationDate = $date;
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
     * @return string
     */
    public function getActivationKey()
    {
        return $this->activationKey;
    }

    /**
     * @param string $activationKey
     * @return User
     */
    public function setActivationKey($activationKey)
    {
        $this->activationKey = $activationKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getNascimento()
    {
        return $this->nascimento;
    }

    public function setNascimento($nascimento)
    {
        $this->nascimento = $nascimento;
        return $this;
    }

    /**
     * @return string
     */
    public function getRua()
    {
        return $this->rua;
    }

    /**
     * @param string $rua
     * @return User
     */
    public function setRua($rua)
    {
        $this->rua = $rua;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     * @return User
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * @return string
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param string $bairro
     * @return User
     */
    public function setBairro($bairro)
    {
        $this->bairro = $bairro;
        return $this;
    }

    /**
     * @return string
     */
    public function getComplemento()
    {
        return $this->complemento;
    }

    /**
     * @param string $complemento
     * @return User
     */
    public function setComplemento($complemento)
    {
        $this->complemento = $complemento;
        return $this;
    }

    /**
     * @return string
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * @param string $cidade
     * @return User
     */
    public function setCidade($cidade)
    {
        $this->cidade = $cidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     * @return User
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return string
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param string $cep
     * @return User
     */
    public function setCep($cep)
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     * @return User
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return string
     */
    public function getRg()
    {
        return $this->rg;
    }

    /**
     * @param string $rg
     * @return User
     */
    public function setRg($rg)
    {
        $this->rg = $rg;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param mixed $celular
     * @return User
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * @param string $telefone
     * @return User
     */
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
        return $this;
    }

    /**
     * @return Polo
     */
    public function getPolo()
    {
        return $this->polo;
    }

    /**
     * @param Polo $polo
     * @return User
     */
    public function setPolo($polo)
    {
        $this->polo = $polo;
        return $this;
    }



    /**
     * @return string
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param string $sexo
     * @return User
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }

    /**
     * @return string
     */
    public function getPai()
    {
        return $this->pai;
    }

    /**
     * @param string $pai
     * @return User
     */
    public function setPai($pai)
    {
        $this->pai = $pai;
        return $this;
    }

    /**
     * @return string
     */
    public function getMae()
    {
        return $this->mae;
    }

    /**
     * @param string $mae
     * @return User
     */
    public function setMae($mae)
    {
        $this->mae = $mae;
        return $this;
    }

    /**
     * @return string
     */
    public function getNaturalidade()
    {
        return $this->naturalidade;
    }

    /**
     * @param string $naturalidade
     * @return User
     */
    public function setNaturalidade($naturalidade)
    {
        $this->naturalidade = $naturalidade;
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
     * @return User
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getCelular2()
    {
        return $this->celular2;
    }

    /**
     * @param string $celular2
     * @return User
     */
    public function setCelular2($celular2)
    {
        $this->celular2 = $celular2;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmissor()
    {
        return $this->emissor;
    }

    /**
     * @param string $emissor
     * @return User
     */
    public function setEmissor($emissor)
    {
        $this->emissor = $emissor;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecado()
    {
        return $this->recado;
    }

    /**
     * @param string $recado
     * @return User
     */
    public function setRecado($recado)
    {
        $this->recado = $recado;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormacao()
    {
        return $this->formacao;
    }

    /**
     * @param string $formacao
     * @return User
     */
    public function setFormacao($formacao)
    {
        $this->formacao = $formacao;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param string $empresa
     * @return User
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresaendereco()
    {
        return $this->empresaendereco;
    }

    /**
     * @param string $empresaendereco
     * @return User
     */
    public function setEmpresaendereco($empresaendereco)
    {
        $this->empresaendereco = $empresaendereco;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresanumero()
    {
        return $this->empresanumero;
    }

    /**
     * @param string $empresanumero
     * @return User
     */
    public function setEmpresanumero($empresanumero)
    {
        $this->empresanumero = $empresanumero;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresacomplemento()
    {
        return $this->empresacomplemento;
    }

    /**
     * @param string $empresacomplemento
     * @return User
     */
    public function setEmpresacomplemento($empresacomplemento)
    {
        $this->empresacomplemento = $empresacomplemento;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresabairro()
    {
        return $this->empresabairro;
    }

    /**
     * @param string $empresabairro
     * @return User
     */
    public function setEmpresabairro($empresabairro)
    {
        $this->empresabairro = $empresabairro;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresacidade()
    {
        return $this->empresacidade;
    }

    /**
     * @param string $empresacidade
     * @return User
     */
    public function setEmpresacidade($empresacidade)
    {
        $this->empresacidade = $empresacidade;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresacep()
    {
        return $this->empresacep;
    }

    /**
     * @param string $empresacep
     * @return User
     */
    public function setEmpresacep($empresacep)
    {
        $this->empresacep = $empresacep;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresatel()
    {
        return $this->empresatel;
    }

    /**
     * @param string $empresatel
     * @return User
     */
    public function setEmpresatel($empresatel)
    {
        $this->empresatel = $empresatel;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresaramal()
    {
        return $this->empresaramal;
    }

    /**
     * @param string $empresaramal
     * @return User
     */
    public function setEmpresaramal($empresaramal)
    {
        $this->empresaramal = $empresaramal;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresacel()
    {
        return $this->empresacel;
    }

    /**
     * @param string $empresacel
     * @return User
     */
    public function setEmpresacel($empresacel)
    {
        $this->empresacel = $empresacel;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return User
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresaemail()
    {
        return $this->empresaemail;
    }

    /**
     * @param string $empresaemail
     * @return User
     */
    public function setEmpresaemail($empresaemail)
    {
        $this->empresaemail = $empresaemail;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmpresaestado()
    {
        return $this->empresaestado;
    }

    /**
     * @param string $empresaestado
     * @return User
     */
    public function setEmpresaestado($empresaestado)
    {
        $this->empresaestado = $empresaestado;
        return $this;
    }

    /**
     * @return string
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * @param string $thumb
     * @return User
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }



    public function __toString() {
        return $this->fullName;
    }

    public function getBoleto(){
        return $this->boleto;
    }

    public function getCronograma(){
        return $this->cronograma;
    }



    public function toArray()
    {
        return [
            'id'                => $this->getId(),
            'isteacher'         => $this->getIsteacher(),
            'codigo'            => $this->getCodigo(),
            'full_name'         => $this->getFullName(),
            'email'             => $this->getEmail(),
            'password'          => $this->getPassword(),
            'status'            => $this->getStatus(),
            'date_created'      => $this->getDateCreated(),
            'date_updated'      => $this->getDateUpdated(),
            'file'               => $this->getFile(),
            'pwd_reset_token_creation_date' => $this->getResetPasswordToken(),
            'pwd_reset_token'   => $this->getPasswordResetToken(),
            'activation_key'    => $this->getActivationKey(),
            'nascimento'        => $this->getNascimento(),
            'rua'               => $this->getRua(),
            'numero'            => $this->getNumero(),
            'bairro'            => $this->getBairro(),
            'complemento'       => $this->getComplemento(),
            'cidade'            => $this->getCidade(),
            'estado'            => $this->getEstado(),
            'cep'               => $this->getCep(),
            'cpf'               => $this->getCpf(),
            'rg'                => $this->getRg(),
            'celular'           => $this->getCelular(),
            'telefone'          => $this->getTelefone(),
            'sexo'              => $this->getSexo(),
            'polo'              => $this->getPolo()->getId(),
            'pai'               => $this->getPai(),
            'mae'               => $this->getMae(),
            'naturalidade'      => $this->getNaturalidade(),
            'salt'              => $this->getSalt(),
            'role_id'           => $this->getRole()->getId(),
            'profissao'         => $this->getProfissao(),
            'emissor'           => $this->getEmissor(),
            'recado'            => $this->getRecado(),
            'formacao'          => $this->getFormacao(),
            'empresa'           => $this->getEmpresa(),
            'empresaendereco'   => $this->getEmpresaendereco(),
            'empresanumero'     => $this->getEmpresanumero(),
            'empresacomplemento' => $this->getEmpresacomplemento(),
            'empresabairro'     => $this->getEmpresabairro(),
            'empresacidade'     => $this->getEmpresacidade(),
            'empresacep'        => $this->getEmpresacep(),
            'empresatel'        => $this->getTelefone(),
            'empresaramal'      => $this->getEmpresaramal(),
            'empresacel'        => $this->getEmpresacel(),
            'website'           => $this->getWebsite(),
            'empresaemail'      => $this->getEmpresaemail(),
            'empresaestado'     => $this->getEmpresaestado(),
            'thumb'             => $this->getThumb(),
            'contrato'          => $this->getContrato(),
        ];
    }


}



