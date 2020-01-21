<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator;

/**
 * This class represents a single post in a blog.
 * @ORM\Entity(repositoryClass="\User\Repository\PostRepository")
 * @ORM\Table(name="post")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    // Post status constants.
    const STATUS_DRAFT       = 1; // Draft.
    const STATUS_PUBLISHED   = 2; // Published.

    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="title")
     */
    protected $title;

    /**
     * @ORM\Column(name="content")
     */
    protected $content;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="img")
     */
    protected $img;

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
     * @ORM\ManyToOne(targetEntity="User\Entity\Category", inversedBy="post")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    protected $category;

    public function __construct($options = [])
    {
        (new Hydrator\ClassMethods())->hydrate($options, $this);

        $this->dateCreated = new \DateTime("now");
        $this->dateUpdated = new \DateTime("now");

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
     * Returns title.
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets title.
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns status.
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets status.
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns post content.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets post content.
     * @param type $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     * @return Post
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     * @return Post
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }



    public function toArray(){
        return array(
            'id'            => $this->getId(),
            'category'      => $this->getCategory()->getId(),
            'title'         => $this->getTitle(),
            'content'       => $this->getContent(),
            'status'        => $this->getStatus(),
            'datecreated'   => $this->getDateCreated(),
            'dateupdated'   => $this->getDateUpdated(),
            'img'           => $this->getImg(),

        );

    }







}

