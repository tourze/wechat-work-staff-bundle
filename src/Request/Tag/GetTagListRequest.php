<?php

namespace WechatWorkStaffBundle\Request\Tag;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取标签列表（成员标签）
 *
 * @see https://developer.work.weixin.qq.com/document/path/90216
 */
class GetTagListRequest extends ApiRequest
{
    use AgentAware;

    public function getRequestPath(): string
    {
        return '/cgi-bin/tag/list';
    }

    public function getRequestOptions(): ?array
    {
        return [];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }
}
