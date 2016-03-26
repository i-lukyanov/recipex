<?php

namespace Recipex\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $displayName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $color;

    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="Recipex\CoreBundle\Entity\File")
     * @ORM\JoinColumn(name="icon_id", referencedColumnName="id", nullable=true)
     */
    protected $icon;


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
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param File $icon
     * @return Group
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }
}

