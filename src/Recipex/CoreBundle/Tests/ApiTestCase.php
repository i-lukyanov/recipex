<?php
/**
 * Author: Ivan Lukyanov
 * Date: 20.03.2016
 */

namespace Recipex\CoreBundle\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Recipex\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    private static $staticClient;

    protected static $baseUri;

    /**
     * @var Client Веб-клиент
     */
    protected $client;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::bootKernel();
        self::$baseUri = getenv('TEST_BASE_URL');
        self::$staticClient = new Client(['base_uri' =>  self::$baseUri, 'exceptions' => false]);
    }
    
    protected function setUp()
    {
        $this->purgeDatabase();
        $this->client = self::$staticClient;
    }

    public function tearDown()
    {
    }

    protected function getService($id)
    {
        return static::$kernel->getContainer()->get($id);
    }

    /**
     * Создание пользователя перед тестом
     *
     * @param string $username
     * @param string $plainPassword
     * @return User
     */
    protected function createUser($username, $plainPassword = 'test')
    {
        $user = new User();
        $user->setUsername($username);
        $user->setName($username);
        $user->setEmail($username.'@test.te');
        $password = $this->getService('security.password_encoder')->encodePassword($user, $plainPassword);
        $user->setPassword($password);
        $user->setEnabled(true);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }
    
    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }
}