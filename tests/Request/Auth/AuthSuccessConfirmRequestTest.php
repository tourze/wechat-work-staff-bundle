<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkStaffBundle\Request\Auth\AuthSuccessConfirmRequest;

/**
 * @internal
 */
#[CoversClass(AuthSuccessConfirmRequest::class)]
#[RunTestsInSeparateProcesses]
final class AuthSuccessConfirmRequestTest extends AbstractIntegrationTestCase
{
    private AuthSuccessConfirmRequest $request;

    protected function onSetUp(): void
    {
        // 通过反射创建实例来避免直接实例化
        $reflection = new \ReflectionClass(AuthSuccessConfirmRequest::class);
        $this->request = $reflection->newInstance();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/user/authsucc', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }

    public function testSetAndGetUserId(): void
    {
        $userId = 'test_user_id';
        $this->request->setUserId($userId);

        $this->assertEquals($userId, $this->request->getUserId());
    }

    public function testGetRequestOptions(): void
    {
        $userId = 'test_user_id';
        $this->request->setUserId($userId);

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertArrayHasKey('userid', $options['query']);
        $this->assertEquals($userId, $options['query']['userid']);
    }
}
