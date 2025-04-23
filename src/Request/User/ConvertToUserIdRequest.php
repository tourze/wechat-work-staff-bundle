<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * openid转userid
 *
 * 该接口主要应用于使用企业支付之后的结果查询。
 * 开发者需要知道某个结果事件的openid对应企业微信内成员的信息时，可以通过调用该接口进行转换查询。
 *
 * @see https://developer.work.weixin.qq.com/document/path/90202
 */
class ConvertToUserIdRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 在使用企业支付之后，返回结果的openid
     */
    private string $openId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/convert_to_userid';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'openid' => $this->getOpenId(),
            ],
        ];
    }

    public function getOpenId(): string
    {
        return $this->openId;
    }

    public function setOpenId(string $openId): void
    {
        $this->openId = $openId;
    }
}
