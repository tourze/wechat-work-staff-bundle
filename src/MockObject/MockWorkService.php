<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\MockObject;

use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;
use WechatWorkStaffBundle\Request\Tag\GetTagListRequest;
use WechatWorkStaffBundle\Request\User\GetTagUsersRequest;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

/**
 * WorkService Mock 类
 */
#[WithMonologChannel(channel: 'wechat_work_staff')]
class MockWorkService implements WorkServiceInterface
{
    /**
     * @param RequestInterface $apiRequest
     * @return array<string, mixed>
     */
    public function request(RequestInterface $apiRequest): array
    {
        // 根据不同的请求类型返回不同的模拟数据
        if ($apiRequest instanceof GetUserInfoByCodeRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'userid' => 'test-user-id',
                'user_ticket' => 'test-user-ticket',
            ];
        }

        if ($apiRequest instanceof GetUserRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'userid' => 'test-user-id',
                'name' => '测试用户',
                'department' => [1, 2],
                'mobile' => '13800138000',
                'email' => 'test@example.com',
                'avatar' => 'https://example.com/avatar.jpg',
            ];
        }

        if ($apiRequest instanceof GetUserDetailByTicketRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'userid' => 'test-user-id',
                'name' => '测试用户',
                'mobile' => '13800138000',
                'email' => 'test@example.com',
                'avatar' => 'https://example.com/avatar.jpg',
            ];
        }

        if ($apiRequest instanceof GetDepartmentListRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'department' => [
                    [
                        'id' => 1,
                        'name' => '总部门',
                        'name_en' => 'General Department',
                        'parentid' => 0,
                        'order' => 1,
                    ],
                    [
                        'id' => 2,
                        'name' => '技术部',
                        'name_en' => 'Technology Department',
                        'parentid' => 1,
                        'order' => 2,
                    ],
                ],
            ];
        }

        if ($apiRequest instanceof GetTagListRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'taglist' => [
                    [
                        'tagid' => 'test-tag-1',
                        'tagname' => '测试标签1',
                    ],
                    [
                        'tagid' => 'test-tag-2',
                        'tagname' => '测试标签2',
                    ],
                ],
            ];
        }

        if ($apiRequest instanceof GetTagUsersRequest) {
            return [
                'errcode' => 0,
                'errmsg' => 'ok',
                'tagname' => '测试标签',
                'userlist' => [
                    [
                        'userid' => 'test-user-1',
                        'name' => '测试用户1',
                    ],
                    [
                        'userid' => 'test-user-2',
                        'name' => '测试用户2',
                    ],
                ],
            ];
        }

        // 默认返回成功的响应
        return [
            'errcode' => 0,
            'errmsg' => 'ok',
        ];
    }
}
