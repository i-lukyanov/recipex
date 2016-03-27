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
            'email' => 'test@test.te',
            'name' => 'Тест',
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test'
            ]
        ];

        $this->client->request('POST', '/api/v1/auth/register', [], [], ['Content-Type' => 'application/json'], json_encode($requestData));
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('username', $content);
        $this->assertArrayNotHasKey('password', $content);
        $this->assertEquals('test', $content['username']);
    }

    public function testRegisterValidationErrors()
    {
        $requestData = [
            'username' => 'test',
            'name' => 'Тест',
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test'
            ]
        ];

        $this->client->request('POST', '/api/v1/auth/register', [], [], ['Content-Type' => 'application/json'], json_encode($requestData));
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertNotEmpty($content['errors']['email']);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function testRegisterInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "avatarNumber" : "2
    "tagLine": "I'm from a test!"
}
EOF;

        $this->client->request('POST', '/api/v1/auth/register', [], [], ['Content-Type' => 'application/json'], $invalidBody);
        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertNotEmpty($content['errors']['body']);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }
}
