<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 删除成员
 *
 * @see https://developer.work.weixin.qq.com/document/path/90198
 */
class DeleteUserRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 成员UserID。对应管理端的帐号
     */
    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/delete';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'userid' => $this->getUserId(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
