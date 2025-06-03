<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\ConvertToUserIdRequest;

class ConvertToUserIdRequestTest extends TestCase
{
    private ConvertToUserIdRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new ConvertToUserIdRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ConvertToUserIdRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testOpenIdGetterAndSetter(): void
    {
        $openId = 'openid_test_123';
        
        $this->request->setOpenId($openId);
        $this->assertSame($openId, $this->request->getOpenId());
    }
    
    public function testOpenIdWithDifferentValues(): void
    {
        $testCases = [
            'ox1234567890abcdef',
            'openid-with-dashes',
            'openid_with_underscores',
            'OPENID_UPPERCASE',
            'openid123456789012345'
        ];
        
        foreach ($testCases as $openId) {
            $this->request->setOpenId($openId);
            $this->assertSame($openId, $this->request->getOpenId());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/convert_to_userid', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertNull($this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $openId = 'test_openid_convert';
        $this->request->setOpenId($openId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('openid', $options['json']);
        $this->assertSame($openId, $options['json']['openid']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 