<?php

namespace Coze\Workflow;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class StreamRun
{
    private $url = 'https://api.coze.cn/v1/workflow/stream_run';

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

    /**
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $data = [
            'workflow_id' => $this->workflowId,
            'parameters' => $this->parameters,
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
        

        $res = $this->client->request('POST', $this->url, [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            RequestOptions::JSON => $data,
            RequestOptions::STREAM => true,
            RequestOptions::READ_TIMEOUT => 60,
        ]);
        //echo $this->accessToken,PHP_EOL;
        //echo json_encode($data, JSON_UNESCAPED_UNICODE),PHP_EOL;
        $body = $res->getBody();
        while (!$body->eof()) {
            $line = $body->read(1024);
            echo $line;
            //fwrite($stream, $line);
            ob_flush();
            flush();
        }
    }
}