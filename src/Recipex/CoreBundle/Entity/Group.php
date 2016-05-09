<?php

namespace Recipex\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(name="groups")
 * @ORM\HasLifecycleCallbacks()
 */
class Group extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="smallint", options={ "unsigned": true })
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({ "get", "list" })
     *
     * @Assert\NotBlank(message="field.not_blank")
     * @Assert\Length(
     *     max="100",
     *     maxMessage="field.max"
     * )
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     * 
     * @Groups({ "get", "list" })
     *
     * @Assert\NotBlank(message="field.not_blank")
     * @Assert\Length(
     *     max="100",
     *     maxMessage="field.max"
     * )
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({ "get", "list" })
     *
     * @Assert\Length(
     *     max="255",
     *     maxMessage="field.max"
     * )
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({ "get" })
     *
     * @Assert\Regex(
     *     pattern="/^\#[0-9a-fA-F]{3,6}$/",
     *     match=true,
     *     message="color.wrong"
     * )
     */
    protected $color;

    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="Recipex\CoreBundle\Entity\File", cascade={ "persist" })
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     *
     * @Groups({ "get" })
     *
     * @Assert\Type(type="Recipex\CoreBundle\Entity\File")
     * @Assert\Valid()
     */
    protected $image;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={ "default": true })
     */
    protected $deletable;


    public function __construct()
    {
        parent::__construct();
        $this->deletable = true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return Group
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Group
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return File
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param File $image
     * @return Group
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @param boolean $deletable
     * @return Group
     */
    public function setDeletable($deletable)
    {
        $this->deletable = $deletable;
        return $this;
    }
}

