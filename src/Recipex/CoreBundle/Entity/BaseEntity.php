<?php
/**
 * Author: Ivan Lukyanov
 * Date: 11.03.2016
 */

namespace Bankon\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseEntity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={ "default": true })
     */
    protected $isActive = true;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetimetz")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    protected $updatedAt;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     *  @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     *  @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
