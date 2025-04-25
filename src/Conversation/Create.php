<?php

namespace Coze\Conversation;

use Coze\EnterMessage;

/**
 * 创建会话
 */
class Create
{
    private string $url = ' https://api.coze.cn/v1/conversation/create';

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * EnterMessage constructor.
     * @var []EnterMessage
     */
    public array $messages = [];

    public function __construct(public string $uuid, EnterMessage ...$messages)
    {
        $this->messages = $messages;
    }

    public function asJson(): string
    {
        $result = [
            'messages' => array_map(fn($message) => $message->asArray(), $this->messages)
        ];
        if ($this->uuid) {
            $result['meta_data'] = [
                'uuid' => $this->uuid
            ];
        }
        return json_encode($result);
    }
}