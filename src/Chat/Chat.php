<?php

namespace Coze\Chat;

use Coze\EnterMessage;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Chat
{
    private string $url = 'https://api.coze.cn/v3/chat';

    /**
     * @return string
     */
    public function getUrl(): string
    {
        if (empty($this->conversationId))
        return $this->url;

        return $this->url . '?conversation_id=' . $this->conversationId;
    }

    /**
     * bot_id
     * 要进行会话聊天的智能体ID。
     * 进入智能体的 开发页面，开发页面 URL 中 bot 参数后的数字就是智能体ID。
     * @var string $botId
     */
    //public string $botId;

    /**
     * user_id
     * 标识当前与智能体的用户，由使用方自行定义、生成与维护。user_id 用于标识对话中的不同用户，不同的 user_id，其对话的上下文消息、数据库等对话记忆数据互相隔离。如果不需要用户数据隔离，可将此参数固定为一个任意字符串，例如 123，abc 等。
     */
    //public string $userId;

    /**
     * additional_messages
     * 对话的附加信息。你可以通过此字段传入历史消息和本次对话中用户的问题。数组长度限制为 100，即最多传入 100 条消息。
     * 若未设置 additional_messages，智能体收到的消息只有会话中已有的消息内容，其中最后一条作为本次对话的用户输入，其他内容均为本次对话的上下文。
     * 若设置了 additional_messages，智能体收到的消息包括会话中已有的消息和 additional_messages 中添加的消息，其中 additional_messages 最后一条消息会作为本次对话的用户输入，其他内容均为本次对话的上下文。
     * @var []EnterMessage
     */
    public array $additionalMessages = [];

    /**
     * uuid
     * 唯一标识一次会话，用于标识一次会话，在一次会话中，uuid 保持不变。
     * 若未设置 uuid，系统会自动生成一个 uuid。
     * 若设置了 uuid，系统会根据 uuid 查找历史会话，若找到历史会话，则使用历史会话作为本次对话的上下文。
     * 若未找到历史会话，则系统会根据 uuid 创建一个新的会话。
     * @var string
     */
    public string $uuid;

    /**
     * stream
     * 是否启用流式返回。
     * @var bool
     */
    public bool $stream = false;

    /**
     * auto_save_history
     * 是否保存本次对话记录。默认为 true，即保存本次对话记录。
     */
    public bool $autoSaveHistory = true;

    /**
     * custom_variables
     * 智能体中定义的变量。在智能体prompt 中设置变量 {{key}} 后，可以通过该参数传入变量值，同时支持 Jinja2 语法。
     * @var array
     */
    public array $customVariables = [];

    /**
     * meta_data
     * 附加信息，通常用于封装一些业务相关的字段。查看对话消息详情时，系统会透传此附加信息。
     */
    public array $metaData = [];

    /**
     * extra_params
     * 附加参数，通常用于特殊场景下指定一些必要参数供模型判断，例如指定经纬度，并询问智能体此位置的天气。
     * 自定义键值对格式，其中键（key）仅支持设置为：
     * -latitude：纬度，此时值（Value）为纬度值，例如 39.9800718。
     * -longitude：经度，此时值（Value）为经度值，例如 116.309314。
     */
    public array $extraParams = [];

    /**
     * shortcut_command
     * 快捷指令信息。你可以通过此参数指定此次对话执行的快捷指令，必须是智能体已绑定的快捷指令。
     */
    public array $shortcutCommand = [];


    /**
     * EnterMessage constructor.
     * @var []EnterMessage
     */
    public array $messages = [];

    public function __construct(public string $conversationId, public string $botId, public string $userId, public Client $client, public string $token)
    {
    }


    private function toArray(): array
    {
        $result = [
            'bot_id' => $this->botId,
            'user_id' => $this->userId,
            'stream' => $this->stream,
            'additional_messages' => $this->additionalMessages,
            'auto_save_history' => $this->autoSaveHistory,
        ];

        if ($this->customVariables) {
            $result['custom_variables'] = $this->customVariables;
        }

        if ($this->additionalMessages) {
            $result['additional_messages'] = array_map(fn($message) => $message->toArray(), $this->additionalMessages);
        }

        if ($this->uuid) {
            $result['meta_data'] = [
                'uuid' => $this->uuid
            ];
        }
        return $result;
    }

    public function addAdditionalMessage(EnterMessage $message)
    {
        $this->additionalMessages[] = $message;
    }
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }
    public function setStream(Bool $stream)
    {
        $this->stream = $stream;
    }

    public function send()
    {
        $data = $this->toArray();
        $res = $this->client->post($this->getUrl(), [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            RequestOptions::JSON => $data,
            RequestOptions::STREAM => $this->stream,
        ]);

        if ($this->stream) {
            $line = '';
            $body = $res->getBody();
            while (!$body->eof()) {
                $text = $body->read(1024);
                if (empty($text)) {
                    break;
                }

                if (empty($this->conversationId)) {
                    $line .= $text;
                    if (str_contains($line, "\n")) {
                        $line = explode("\n", $line);
                        foreach ($line as $lineItem) {
                            if (str_starts_with($lineItem, "data:")) {
                                // 去掉 data: 前缀
                                $lineItem = substr($lineItem, 5);
                                $lineItem = json_decode($lineItem, true);
                                $this->conversationId = $lineItem['conversation_id'] ?? '';
                                break;
                            }
                        }

                    }
                }

                echo $text;

                ob_flush();
                flush();
            }

        } else {
            return json_decode($res->getBody()->getContents(), true);
        }
    }
}