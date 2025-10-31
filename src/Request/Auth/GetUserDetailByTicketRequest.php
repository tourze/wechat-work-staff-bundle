<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * oauth2获取访问用户敏感信息
 *
 * @see https://developer.work.weixin.qq.com/document/path/96443
 */
class GetUserDetailByTicketRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 成员票据，最大为512字节，有效期为1800s。scope为snsapi_privateinfo，且用户在应用可见范围之内时返回此参数。后续利用该参数可以获取用户信息或敏感信息，参见"获取访问用户敏感信息"。暂时不支持上下游或/企业互联场景
     */
    private string $userTicket;

    public function getRequestPath(): string
    {
        return '/cgi-bin/auth/getuserdetail';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'user_ticket' => $this->getUserTicket(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getUserTicket(): string
    {
        return $this->userTicket;
    }

    public function setUserTicket(string $userTicket): void
    {
        $this->userTicket = $userTicket;
    }
}
