<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\MockObject;

use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\MockObject\MockWorkService;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

/**
 * @internal
 */
#[CoversClass(MockWorkService::class)]
#[RunTestsInSeparateProcesses]
final class MockWorkServiceTest extends AbstractIntegrationTestCase
{
    private MockWorkService $mockWorkService;

    protected function onSetUp(): void
    {
        // 从容器获取 MockWorkService 服务
        $service = self::getContainer()->get(WorkServiceInterface::class);
        $this->assertInstanceOf(MockWorkService::class, $service);
        /** @var MockWorkService $service */
        $this->mockWorkService = $service;
    }

    public function testGetUserInfoByCodeRequest(): void
    {
        // 通过反射创建请求对象并设置属性
        $request = $this->createRequestWithProperty(GetUserInfoByCodeRequest::class, 'code', 'test-code');
        $this->assertInstanceOf(RequestInterface::class, $request);
        $result = $this->mockWorkService->request($request);

        $this->assertSame(0, $result['errcode']);
        $this->assertSame('ok', $result['errmsg']);
        $this->assertSame('test-user-id', $result['userid']);
        $this->assertSame('test-user-ticket', $result['user_ticket']);
    }

    public function testGetUserRequest(): void
    {
        // 通过反射创建请求对象并设置属性
        $request = $this->createRequestWithProperty(GetUserRequest::class, 'userId', 'test-user-id');
        $this->assertInstanceOf(RequestInterface::class, $request);
        $result = $this->mockWorkService->request($request);

        $this->assertSame(0, $result['errcode']);
        $this->assertSame('ok', $result['errmsg']);
        $this->assertSame('test-user-id', $result['userid']);
        $this->assertSame('测试用户', $result['name']);
        $this->assertSame([1, 2], $result['department']);
        $this->assertSame('13800138000', $result['mobile']);
        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame('https://example.com/avatar.jpg', $result['avatar']);
    }

    public function testGetUserDetailByTicketRequest(): void
    {
        // 通过反射创建请求对象并设置属性
        $request = $this->createRequestWithProperty(GetUserDetailByTicketRequest::class, 'userTicket', 'test-ticket');
        $this->assertInstanceOf(RequestInterface::class, $request);
        $result = $this->mockWorkService->request($request);

        $this->assertSame(0, $result['errcode']);
        $this->assertSame('ok', $result['errmsg']);
        $this->assertSame('test-user-id', $result['userid']);
        $this->assertSame('测试用户', $result['name']);
        $this->assertSame('13800138000', $result['mobile']);
        $this->assertSame('test@example.com', $result['email']);
        $this->assertSame('https://example.com/avatar.jpg', $result['avatar']);
    }

    /**
     * 通过反射创建请求对象并设置私有属性
     */
    private function createRequestWithProperty(string $className, string $propertyName, mixed $value): RequestInterface
    {
        /** @var class-string $className */
        $reflection = new \ReflectionClass($className);
        $request = $reflection->newInstanceWithoutConstructor();

        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($request, $value);

        $this->assertInstanceOf(RequestInterface::class, $request);

        return $request;
    }

    public function testGetDepartmentListRequest(): void
    {
        $request = new GetDepartmentListRequest();
        $result = $this->mockWorkService->request($request);

        $this->assertSame(0, $result['errcode']);
        $this->assertSame('ok', $result['errmsg']);
        $this->assertIsArray($result['department']);
        $this->assertCount(2, $result['department']);

        $departments = $result['department'];
        $this->assertIsArray($departments);

        $firstDept = $departments[0];
        $this->assertIsArray($firstDept);
        $this->assertSame(1, $firstDept['id']);
        $this->assertSame('总部门', $firstDept['name']);
        $this->assertSame('General Department', $firstDept['name_en']);
        $this->assertSame(0, $firstDept['parentid']);
        $this->assertSame(1, $firstDept['order']);

        $secondDept = $departments[1];
        $this->assertIsArray($secondDept);
        $this->assertSame(2, $secondDept['id']);
        $this->assertSame('技术部', $secondDept['name']);
        $this->assertSame('Technology Department', $secondDept['name_en']);
        $this->assertSame(1, $secondDept['parentid']);
        $this->assertSame(2, $secondDept['order']);
    }

    public function testRequestMethodDirectly(): void
    {
        // 测试 request() 方法本身
        $request = new GetDepartmentListRequest();
        $result = $this->mockWorkService->request($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('errcode', $result);
        $this->assertArrayHasKey('errmsg', $result);
    }

    public function testUnknownRequestReturnsDefaultResponse(): void
    {
        // 创建一个不支持的请求类型
        $unknownRequest = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return '/test';
            }

            /**
             * @return array<string, mixed>
             */
            public function getRequestOptions(): array
            {
                return [];
            }

            public function getRequestMethod(): string
            {
                return 'POST';
            }
        };

        $result = $this->mockWorkService->request($unknownRequest);

        $this->assertSame(0, $result['errcode']);
        $this->assertSame('ok', $result['errmsg']);
    }
}
