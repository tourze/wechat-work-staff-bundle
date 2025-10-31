<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 更新标签（成员标签）
 *
 * @see https://developer.work.weixin.qq.com/document/path/90211
 */
class UpdateTagRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名
     */
    private string $name;

    /**
     * @var int 标签id
     */
    private int $id;

    public function getRequestPath(): string
    {
        return '/cgi-bin/tag/update';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'tagname' => $this->getName(),
            'tagid' => $this->getId(),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
