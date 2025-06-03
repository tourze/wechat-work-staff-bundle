<?php

namespace WechatWorkStaffBundle\Tests\Request\User;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Request\User\UpdateUserRequest;

class UpdateUserRequestTest extends TestCase
{
    private UpdateUserRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new UpdateUserRequest();
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(UpdateUserRequest::class, $this->request);
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }
    
    public function testUserIdGetterAndSetter(): void
    {
        $userId = 'update_user_123';
        
        $this->request->setUserId($userId);
        $this->assertSame($userId, $this->request->getUserId());
    }
    
    public function testNameGetterAndSetter(): void
    {
        $name = '李四更新';
        
        $this->request->setName($name);
        $this->assertSame($name, $this->request->getName());
    }
    
    public function testNameWithNullValue(): void
    {
        $this->request->setName(null);
        $this->assertNull($this->request->getName());
    }
    
    public function testAliasGetterAndSetter(): void
    {
        $alias = '小李更新';
        
        $this->request->setAlias($alias);
        $this->assertSame($alias, $this->request->getAlias());
    }
    
    public function testAliasWithNullValue(): void
    {
        $this->request->setAlias(null);
        $this->assertNull($this->request->getAlias());
    }
    
    public function testMobileGetterAndSetter(): void
    {
        $mobile = '13900139001';
        
        $this->request->setMobile($mobile);
        $this->assertSame($mobile, $this->request->getMobile());
    }
    
    public function testMobileWithNullValue(): void
    {
        $this->request->setMobile(null);
        $this->assertNull($this->request->getMobile());
    }
    
    public function testPositionGetterAndSetter(): void
    {
        $position = '资深工程师';
        
        $this->request->setPosition($position);
        $this->assertSame($position, $this->request->getPosition());
    }
    
    public function testPositionWithNullValue(): void
    {
        $this->request->setPosition(null);
        $this->assertNull($this->request->getPosition());
    }
    
    public function testGenderGetterAndSetter(): void
    {
        $gender = 2; // 女性
        
        $this->request->setGender($gender);
        $this->assertSame($gender, $this->request->getGender());
    }
    
    public function testGenderWithNullValue(): void
    {
        $this->request->setGender(null);
        $this->assertNull($this->request->getGender());
    }
    
    public function testEmailGetterAndSetter(): void
    {
        $email = 'updated@company.com';
        
        $this->request->setEmail($email);
        $this->assertSame($email, $this->request->getEmail());
    }
    
    public function testEmailWithNullValue(): void
    {
        $this->request->setEmail(null);
        $this->assertNull($this->request->getEmail());
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/user/update', $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        // UpdateUserRequest 没有实现getRequestMethod，应该返回null
        $this->assertNull($this->request->getRequestMethod());
    }
    
    public function testGetRequestOptionsWithUserIdOnly(): void
    {
        $userId = 'update_test_user';
        
        $this->request->setUserId($userId);
        // 初始化所有可选属性为null以避免未初始化错误
        $this->request->setName(null);
        $this->request->setAlias(null);
        $this->request->setMobile(null);
        $this->request->setPosition(null);
        $this->request->setGender(null);
        $this->request->setEmail(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('userid', $options['json']);
        $this->assertSame($userId, $options['json']['userid']);
        $this->assertArrayNotHasKey('name', $options['json']);
    }
    
    public function testGetRequestOptionsWithAllFields(): void
    {
        $userId = 'full_update_user';
        $name = '完整更新用户';
        $alias = '全更新';
        $mobile = '13800138001';
        $position = '高级经理';
        $gender = 2;
        $email = 'fullupdate@company.com';
        
        $this->request->setUserId($userId);
        $this->request->setName($name);
        $this->request->setAlias($alias);
        $this->request->setMobile($mobile);
        $this->request->setPosition($position);
        $this->request->setGender($gender);
        $this->request->setEmail($email);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        
        // 验证所有字段都存在
        $expectedFields = ['userid', 'name', 'alias', 'mobile', 'position', 'gender', 'email'];
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $options['json'], "字段 {$field} 应该存在");
        }
        
        // 验证字段值
        $this->assertSame($userId, $options['json']['userid']);
        $this->assertSame($name, $options['json']['name']);
        $this->assertSame($alias, $options['json']['alias']);
        $this->assertSame($mobile, $options['json']['mobile']);
        $this->assertSame($position, $options['json']['position']);
        $this->assertSame($gender, $options['json']['gender']);
        $this->assertSame($email, $options['json']['email']);
    }
    
    public function testGetRequestOptionsWithOptionalFieldsNull(): void
    {
        $userId = 'minimal_update_user';
        
        $this->request->setUserId($userId);
        $this->request->setName(null);
        $this->request->setAlias(null);
        $this->request->setMobile(null);
        $this->request->setPosition(null);
        $this->request->setGender(null);
        $this->request->setEmail(null);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        
        // 验证必须字段存在
        $this->assertArrayHasKey('userid', $options['json']);
        
        // 验证可选字段不存在
        $this->assertArrayNotHasKey('name', $options['json']);
        $this->assertArrayNotHasKey('alias', $options['json']);
        $this->assertArrayNotHasKey('mobile', $options['json']);
        $this->assertArrayNotHasKey('position', $options['json']);
        $this->assertArrayNotHasKey('gender', $options['json']);
        $this->assertArrayNotHasKey('email', $options['json']);
    }
    
    public function testAgentAwareTrait(): void
    {
        $this->assertTrue(method_exists($this->request, 'setAgent'));
        $this->assertTrue(method_exists($this->request, 'getAgent'));
    }
} 