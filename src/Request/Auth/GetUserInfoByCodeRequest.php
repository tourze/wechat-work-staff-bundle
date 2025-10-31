<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 根据授权回调的code来获取用户信息
 *
 * @see https://developer.work.weixin.qq.com/document/path/98177
 */
class GetUserInfoByCodeRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 通过成员授权获取到的code，最大为512字节。每次成员授权带上的code将不一样，code只能使用一次，5分钟未被使用自动过期。
     */
    private string $code;

    public function getRequestPath(): string
    {
        return '/cgi-bin/auth/getuserinfo';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'code' => $this->getCode(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
