<?php
/**
 * Author: Ivan Lukyanov
 * Date: 20.03.2016
 */

namespace Recipex\AuthBundle\Tests\Controller;

use Recipex\CoreBundle\Tests\ApiTestCase;

class RegistrationControllerTest extends ApiTestCase
{
    public function testRegisterSuccess()
    {
        $requestData = [
            'username' => 'test',
            'email' => 'test',
            'name' => 'Тест',
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test'
            ]
        ];
        $client = static::createClient();

        $crawler = $client->request('POST', '/api/v1/auth/register', [], [], ['Content-Type' => 'application/json'], json_encode($requestData));

        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('username', $content);
        $this->assertArrayNotHasKey('password', $content);
        $this->assertEquals('test', $content['username']);
    }
}
