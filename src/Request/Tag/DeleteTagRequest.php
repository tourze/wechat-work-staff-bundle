<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 删除标签（成员标签）
 *
 * @see https://developer.work.weixin.qq.com/document/path/90212
 */
class DeleteTagRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int 标签ID
     */
    private int $id;

    public function getRequestPath(): string
    {
        return '/cgi-bin/tag/delete';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'tagid' => $this->getId(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
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
