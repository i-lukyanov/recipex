<?php
/**
 * Author: Ivan Lukyanov
 * Date: 20.03.2016
 */

namespace Recipex\CoreBundle\Tests\Controller;

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

        $response = $this->client->request('POST', 'auth/register', ['json' => $requestData]);
        $content = json_decode($response->getBody()->getContents(), true);

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

        $response = $this->client->request('POST', 'auth/register', ['json' => $requestData]);
        $content = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertNotEmpty($content['errors']['email']);
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }

    public function testRegisterInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "username": "test"
    "name": "Тест",
    "plainPassword": {
        "first": "test",
        "second": "test"
    }
}
EOF;

        $response = $this->client->request('POST', 'auth/register', ['body' => $invalidBody]);
        $content = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('body', $content['errors']);
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
    }

    public function test404Exception()
    {
        $response = $this->client->request('POST', 'auth/fake', []);
        $content = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('about:blank', $content['type']);
        $this->assertEquals('Not Found', $content['title']);
        $this->assertArrayHasKey('detail', $content);
    }
}
