<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取成员ID列表
 *
 * 这个接口，我们使用来替代 GetUserSimpleListRequest
 *
 * @see https://developer.work.weixin.qq.com/document/path/96021
 */
class ListIdRequest extends ApiRequest
{
    use AgentAware;

    /**
     * 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用不填
     */
    private ?string $cursor = null;

    /**
     * 分页，预期请求的数据量，取值范围 1 ~ 10000
     */
    private ?int $limit = 1000;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/list_id';
    }

    public function getRequestOptions(): ?array
    {
        $json = [];

        if (null !== $this->getCursor()) {
            $json['cursor'] = $this->getCursor();
        }
        if (null !== $this->getLimit()) {
            $json['limit'] = $this->getLimit();
        }

        return [
            'json' => $json,
        ];
    }

    /**
     * Get the value of cursor
     */
    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    /**
     * Set the value of cursor
     */
    public function setCursor(?string $cursor): self
    {
        $this->cursor = $cursor;

        return $this;
    }

    /**
     * Get the value of limit
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}
