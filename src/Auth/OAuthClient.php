<?php
namespace Coze\Auth;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class OAuthClient
{

    public function __construct(public string $appId,public string $publicKey, public string $clientSecret, public Client $client)
    {
    }

    // 生成token
    public function generateJwtToken(): string
    {
        $payload = [
            'iss' => $this->appId,
            'aud' => 'api.coze.cn',
            'iat' => time(),
            'exp' => time() + 86400,
            'jti' => uniqid('req-coze')
        ];

        return JWT::encode($payload, $this->clientSecret, 'RS256', $this->publicKey);
    }

    public function getAccessToken(): array
    {
        $resp = $this->client->post('/api/permission/oauth2/token', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->generateJwtToken()
            ],
            RequestOptions::JSON => [
                'duration_seconds' => 86399,
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer'
            ]
        ]);

        return json_decode($resp->getBody()->getContents(), true);
    }
}