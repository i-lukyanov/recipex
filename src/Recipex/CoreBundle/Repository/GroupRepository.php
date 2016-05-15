<?php

namespace Recipex\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Recipex\CoreBundle\Entity\Group;

class GroupRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return Group
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
