<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\DeleteUserRequest;

class DeleteUserRequestTest extends TestCase
{
    private DeleteUserRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new DeleteUserRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(DeleteUserRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testUserIdGetterAndSetter(): void
    {
        $userId = 'test_user_123';
        
        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }
    
    public function testUserIdWithDifferentValues(): void
    {
        $testCases = [
            'user1',
            'test-user-with-dashes',
            'user_with_underscores',
            'UPPERCASE_USER',
            'user123456789'
        ];
        
        foreach ($testCases as $userId) {
            $this->request->setUserId($userId);
            $this->assertSame($userId, $this->request->getUserId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/delete', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $userId = 'delete_user_test';
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