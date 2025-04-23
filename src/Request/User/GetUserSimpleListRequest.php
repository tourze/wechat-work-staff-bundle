<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 获取部门成员
 *
 * @deprecated 【重要】从2022年8月15日10点开始，“企业管理后台 - 管理工具 - 通讯录同步”的新增IP将不能再调用此接口，企业可通过「获取成员ID列表」和「获取部门ID列表」接口获取userid和部门ID列表
 * @see https://developer.work.weixin.qq.com/document/path/90200
 * @see https://qydev.weixin.qq.com/wiki/index.php?title=%E7%AE%A1%E7%90%86%E6%88%90%E5%91%98
 */
class GetUserSimpleListRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var int 获取的部门ID
     */
    private int $departmentId;

    /**
     * @var bool|null 1/0：是否递归获取子部门下面的成员
     */
    private ?bool $fetchChild = null;

    /**
     * @var string|null 0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加，未填写则默认为4
     */
    private ?string $status = null;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/simplelist';
    }

    public function getRequestOptions(): ?array
    {
        $query = [
            'department_id' => $this->getDepartmentId(),
        ];
        if (null !== $this->getFetchChild()) {
            $query['fetch_child'] = $this->getFetchChild() ? 1 : 0;
        }

        if (null !== $this->getStatus()) {
            $query['status'] = $this->getStatus();
        }

        return [
            'query' => $query,
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getDepartmentId(): int
    {
        return $this->departmentId;
    }

    public function setDepartmentId(int $departmentId): void
    {
        $this->departmentId = $departmentId;
    }

    public function getFetchChild(): ?bool
    {
        return $this->fetchChild;
    }

    public function setFetchChild(?bool $fetchChild): void
    {
        $this->fetchChild = $fetchChild;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}
