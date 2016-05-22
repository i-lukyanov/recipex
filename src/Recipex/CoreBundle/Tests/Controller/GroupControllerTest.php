<?php

namespace Recipex\CoreBundle\Tests\Controller;

use Recipex\CoreBundle\Tests\ApiTestCase;

class GroupControllerTest extends ApiTestCase
{
    protected $authToken;


    protected function setUp()
    {
        parent::setUp();

        $login = 'ivan';
        $plainPassword = 'test';
        $this->createUser($login, $plainPassword);

        $requestData = ['_username' => $login, '_password' => $plainPassword];
        $response = $this->client->request('POST', 'auth/login_check', ['form_params' => $requestData]);
        $content = json_decode($response->getBody()->getContents(), true);
        $this->authToken = $content['token'];
    }

    public function testCreateGroupSuccess()
    {
        $displayName = 'Проверочная';
        $groupData = [
            ['name' => 'displayName', 'contents' => $displayName],
            ['name' => 'color', 'contents' => '#123455'],
            ['name' => 'description', 'contents' => 'Группа для проверки'],
            ['name' => 'image', 'contents' => fopen('d:\#Downloads\settings\tor_logo.png', 'r')],
        ];

        $response = $this->client->request(
            'POST',
            'groups',
            [
                'headers' => ['Authorization' => 'Bearer ' . $this->authToken],
                'multipart' => $groupData,
            ]
        );
        $content = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayNotHasKey('deletable', $content);
        $this->assertEquals($displayName, $content['displayName']);
    }

}
