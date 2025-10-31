<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Department;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取部门列表
 *
 * @see https://developer.work.weixin.qq.com/document/path/90208
 */
class GetDepartmentListRequest extends ApiRequest
{
    use AgentAware;

    private ?int $id = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/department/list';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $query = [];
        if (null !== $this->getId()) {
            $query['id'] = $this->getId();
        }

        return [
            'query' => $query,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
