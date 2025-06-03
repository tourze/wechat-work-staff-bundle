<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToOpenIdRequest;

class ConvertToOpenIdRequestTest extends TestCase
{
    private ConvertToOpenIdRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new ConvertToOpenIdRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ConvertToOpenIdRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testUserIdGetterAndSetter(): void
    {
        $userId = 'convert_user_123';
        
        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }
    
    public function testUserIdWithDifferentValues(): void
    {
        $testCases = [
            'employee1',
            'manager-001',
            'sales_rep_zhang',
            'ADMIN_USER',
            'user123'
        ];
        
        foreach ($testCases as $userId) {
            $this->request->setUserId($userId);
            $this->assertSame($userId, $this->request->getUserId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/convert_to_openid', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertNull($this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $userId = 'convert_test_user';
        $this->request->setUserId($userId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertSame($userId, $options['json']['userid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
}
