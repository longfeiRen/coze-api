<?php

namespace Coze\Workflow;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * 正常请求
 */
class Run
{
    private $url = 'https://api.coze.cn/v1/workflow/run';

    public function __construct(
        public Client $client,
        public string $accessToken,
        public string $workflowId,
        public array  $parameters = [],
        public string $botId = '',
        public array  $ext = [],
        public string $appId = ''
    )
    {

    }

    public function handle($parameters = []): array
    {
        $data = [
            'workflow_id' => $this->workflowId,
            'parameters' => array_merge($this->parameters, $parameters),
        ];
        if (!empty($this->ext)) {
            $data['ext'] = $this->ext;
        }
        if (!empty($this->botId)) {
            $data['bot_id'] = $this->botId;
        }
        if (!empty($this->appId)) {
            $data['app_id'] = $this->appId;
        }
        $res = $this->client->post($this->url, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            RequestOptions::JSON => $data
        ]);
        // 获取返回的流式数据
        $body = $res->getBody()->getContents();
        $body = json_decode($body, true);
        if ($body['code']) {
            throw new \Exception($body['message']);
        }

        return json_decode($body['data'], true);
    }
}