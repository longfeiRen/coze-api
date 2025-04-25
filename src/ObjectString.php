<?php

namespace Coze;

class ObjectString
{
    /**
     * 多模态消息内容类型，支持设置为：
     * -text: 文本类型
     * -file: 文件类型
     * -image: 图片类型。
     * @var string
     */
    public string $type;

    /**
     * 文本内容。
     * @var string
     */
    public string $text;

    /**
     * 文件或图片内容的 ID。
     * @var string 文件id
     */
    public string $fileId;

    /**
     * 文件或图片内容的 URL。
     * @var string
     */
    public string $fileUrl;

    public function __construct(string $type, string $text, string $fileId, string $fileUrl)
    {
        $this->type = $type;
        $this->text = $text;
        $this->fileId = $fileId;
        $this->fileUrl = $fileUrl;
    }

    public function asJson(): string
    {
        return json_encode([
            'type' => $this->type,
            'text' => $this->text,
            'fileId' => $this->fileId,
            'fileUrl' => $this->fileUrl
        ]);
    }
}