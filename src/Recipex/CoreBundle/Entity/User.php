<?php

namespace Recipex\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
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
     * @Assert\NotBlank(message="Please enter your name")
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="The name is too short",
     *     maxMessage="The name is too long"
     * )
     */
    protected $name;

    /**
     * @Groups({ "get", "list" })
     */
    protected $username;

    /**
     * @Groups({ "get", "list" })
     */
    protected $email;

    /**
     * @Groups({ "get" })
     */
    protected $enabled = true;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
