<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取标签成员
 *
 * @see https://developer.work.weixin.qq.com/document/path/90213
 */
class GetTagUsersRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 标签ID
     */
    private string $tagId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/tag/get';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'tagid' => $this->getTagId(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getTagId(): string
    {
        return $this->tagId;
    }

    public function setTagId(string $tagId): void
    {
        $this->tagId = $tagId;
    }
}
