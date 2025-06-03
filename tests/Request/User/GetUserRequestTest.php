<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

class GetUserRequestTest extends TestCase
{
    private GetUserRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetUserRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testUserIdGetterAndSetter(): void
    {
        $userId = 'get_user_123';
        
        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }
    
    public function testUserIdWithDifferentValues(): void
    {
        $testCases = [
            'admin',
            'test-user-001',
            'user_department_manager',
            'USER_MANAGER',
            'zhangsan'
        ];
        
        foreach ($testCases as $userId) {
            $this->request->setUserId($userId);
            $this->assertSame($userId, $this->request->getUserId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/get', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $userId = 'get_user_test';
        $this->request->setUserId($userId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('userid', $options['query']);
        $this->assertSame($userId, $options['query']['userid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 