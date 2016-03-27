<?php

namespace Recipex\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="articles")
 * @ORM\HasLifecycleCallbacks()
 */
class Article extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={ "unsigned": true })
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArticleType
     * @ORM\ManyToOne(targetEntity="Recipex\CoreBundle\Entity\ArticleType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Recipex\CoreBundle\Entity\Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $group;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Recipex\CoreBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $externalComments;

    /**
     * @var int
     * @ORM\Column(type="decimal", nullable=true, precision=1, scale=0, options={ "unsigned": true })
     */
    protected $difficulty;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $tags;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Recipex\CoreBundle\Entity\File")
     * @ORM\JoinTable(
     *     name="article_file",
     *     joinColumns={ @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="cascade") },
     *     inverseJoinColumns={ @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="cascade") }
     * )
     */
    protected $files;


    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
    }

    /**
     * @return ArticleType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param ArticleType $type
     * @return Article
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     * @return Article
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Article
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Article
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalComments()
    {
        return $this->externalComments;
    }

    /**
     * @param string $externalComments
     * @return Article
     */
    public function setExternalComments($externalComments)
    {
        $this->externalComments = $externalComments;
        return $this;
    }

    /**
     * @return int
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * @param int $difficulty
     * @return Article
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return Article
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param File $file
     * @return bool
     */
    public function hasFile(File $file)
    {
        return $this->getFiles()->contains($file);
    }

    /**
     * @param File $file
     * @return bool
     */
    public function addFile(File $file)
    {
        return $this->getFiles()->add($file);
    }
}

