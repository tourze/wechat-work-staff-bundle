<?php

namespace WechatWorkStaffBundle\Tests\Request\Auth;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;

class GetUserInfoByCodeRequestTest extends TestCase
{
    private GetUserInfoByCodeRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetUserInfoByCodeRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetUserInfoByCodeRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testCodeGetterAndSetter(): void
    {
        $code = 'test_auth_code_123';
        
        $this->request->setCode($code);
        $this->assertSame($code, $this->request->getCode());
    }
    
    public function testCodeWithDifferentValues(): void
    {
        $testCases = [
            'short_code',
            'very_long_authorization_code_with_special_characters_123456789',
            'code_with_numbers_123',
            'code-with-dashes',
            'code_with_underscores'
        ];
        
        foreach ($testCases as $code) {
            $this->request->setCode($code);
            $this->assertSame($code, $this->request->getCode());
        }
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/auth/getuserinfo', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
    }
    
    public function testGetRequestOptions(): void
    {
        $code = 'test_code_for_options';
        $this->request->setCode($code);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('code', $options['query']);
        $this->assertSame($code, $options['query']['code']);
    }
    
    public function testGetRequestOptionsWithEmptyCode(): void
    {
        $this->request->setCode('');
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('code', $options['query']);
        $this->assertSame('', $options['query']['code']);
    }
    
    public function testAgentAwareTrait(): void
    {
        // 测试是否使用了AgentAware trait
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
    
    public function testSetAndGetAgent(): void
    {
        $agent = $this->createMock(Agent::class);
        
        $this->request->setAgent($agent);
        $this->assertSame($agent, $this->request->getAgent());
    }
} 