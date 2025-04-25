<?php

namespace Coze\Auth;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class OAuthClientTest extends TestCase
{
    // 测试生成token
    public function testGenerateJwtToken()
    {
        $clientId = '83529616230818544471052255166193.app.coze';
        $clientSecret = 'VxO6dShIvq4jdPW7m0BXWzAtAuUI54ie8R0HBEJeW43QCJjP';
        $client = new OAuthClient($clientId, $clientSecret, new Client());
        $token = $client->generateJwtToken();
        var_dump($token);
        $this->assertIsString($token);
    }
}
