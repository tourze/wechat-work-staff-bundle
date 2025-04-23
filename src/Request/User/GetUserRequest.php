<?php

namespace WechatWorkStaffBundle\Request\User;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 读取成员
 *
 * 应用只能获取可见范围内的成员信息，且每种应用获取的字段有所不同，在返回结果说明中会逐个说明。企业通讯录安全特别重要，企业微信将持续升级加固通讯录接口的安全机制，以下是关键的变更点：
 * 1. 从2022年6月20号20点开始，除通讯录同步以外的基础应用（如客户联系、微信客服、会话存档、日程等），以及新创建的自建应用与代开发应用，调用该接口时，不再返回以下字段：头像、性别、手机、邮箱、企业邮箱、员工个人二维码、地址，应用需要通过oauth2手工授权的方式获取管理员与员工本人授权的字段。
 * 2. 【重要】从2022年8月15日10点开始，“企业管理后台 - 管理工具 - 通讯录同步”的新增IP将不能再调用此接口，企业可通过「获取成员ID列表」和「获取部门ID列表」接口获取userid和部门ID列表。查看调整详情。
 *
 * @see https://developer.work.weixin.qq.com/document/path/90196
 */
class GetUserRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var string 成员UserID。对应管理端的帐号
     */
    private string $userId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/user/get';
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
