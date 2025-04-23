<?php

namespace WechatWorkStaffBundle\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 登录二次验证
 * 此接口可以满足安全性要求高的企业进行成员验证。开启二次验证后，当且仅当成员登录时，需跳转至企业自定义的页面进行验证。验证频率可在设置页面选择。
 *
 * @see https://developer.work.weixin.qq.com/document/path/90203
 */
class AuthSuccessConfirmRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 成员UserID。对应管理端的账号
     */
    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/authsucc';
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
