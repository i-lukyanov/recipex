<?php
/**
 * Author: Ivan Lukyanov
 * Date: 20.03.2016
 */

namespace Recipex\CoreBundle\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::bootKernel();
    }
    
    public function setUp()
    {
        $this->purgeDatabase();
    }

    public function tearDown()
    {
    }

    protected function getService($id)
    {
        return static::$kernel->getContainer()->get($id);
    }
    
    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }
}