<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * userid转openid
 *
 * 该接口使用场景为企业支付，在使用企业红包和向员工付款时，需要自行将企业微信的userid转成openid。
 * 注：需要成员使用微信登录企业微信或者关注微信插件（原企业号）才能转成openid;
 *
 * @see https://developer.work.weixin.qq.com/document/path/90202
 */
class ConvertToOpenIdRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 企业内的成员id
     */
    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/convert_to_openid';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'userid' => $this->getUserId(),
            ],
        ];
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
