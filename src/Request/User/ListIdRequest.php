<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取成员ID列表
 *
 * 替代已废弃的 GetUserSimpleListRequest API (自2022年8月15日起废弃)
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
    private int $limit = 1000;

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
        $json['limit'] = $this->getLimit();

        return [
            'json' => $json,
        ];
    }

    /**
     * 获取游标值
     */
    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    /**
     * 设置游标值
     */
    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }

    /**
     * 获取限制数量
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * 设置限制数量
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}
