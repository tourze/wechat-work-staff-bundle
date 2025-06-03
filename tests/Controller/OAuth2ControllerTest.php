<?php

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkStaffBundle\Controller\OAuth2Controller;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WeuiBundle\Service\NoticeService;

class OAuth2ControllerTest extends TestCase
{
    private OAuth2Controller $controller;
    private GetWechatWorkUserByAuthCode&MockObject $codeApi;
    private NoticeService&MockObject $noticeService;
    
    protected function setUp(): void
    {
        $this->codeApi = $this->createMock(GetWechatWorkUserByAuthCode::class);
        $this->noticeService = $this->createMock(NoticeService::class);
        
        $this->controller = new OAuth2Controller(
            $this->codeApi,
            $this->noticeService
        );
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(OAuth2Controller::class, $this->controller);
    }
    
    public function testControllerExtendsAbstractController(): void
    {
        $reflection = new \ReflectionClass(OAuth2Controller::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\AbstractController'));
    }
    
    public function testControllerHasRequiredMethods(): void
    {
        $methods = get_class_methods(OAuth2Controller::class);
        
        $expectedMethods = [
            'authRedirect',
            'authCallback',
            'connectRedirect',
            'connectCallback'
        ];
        
        foreach ($expectedMethods as $method) {
            $this->assertContains($method, $methods, "方法 {$method} 应该存在");
        }
    }
    
    public function testControllerMethodsArePublic(): void
    {
        $reflection = new \ReflectionClass(OAuth2Controller::class);
        
        $publicMethods = [
            'authRedirect',
            'authCallback', 
            'connectRedirect',
            'connectCallback'
        ];
        
        foreach ($publicMethods as $methodName) {
            $method = $reflection->getMethod($methodName);
            $this->assertTrue($method->isPublic(), "方法 {$methodName} 应该是 public");
        }
    }
    
    public function testControllerHasRequiredDependencies(): void
    {
        $reflection = new \ReflectionClass(OAuth2Controller::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(2, $parameters, '构造函数应该有2个参数');
        
        $parameterNames = array_map(fn($param) => $param->getName(), $parameters);
        $this->assertContains('codeApi', $parameterNames);
        $this->assertContains('noticeService', $parameterNames);
    }
    
    public function testControllerMethodParameters(): void
    {
        $reflection = new \ReflectionClass(OAuth2Controller::class);
        
        // 检查authRedirect方法的参数
        $authRedirectMethod = $reflection->getMethod('authRedirect');
        $authRedirectParams = $authRedirectMethod->getParameters();
        
        $this->assertGreaterThanOrEqual(3, count($authRedirectParams), 'authRedirect方法应该有至少3个参数');
        
        // 检查第一个参数是string $corpId
        $this->assertEquals('corpId', $authRedirectParams[0]->getName());
        $this->assertEquals('string', $authRedirectParams[0]->getType()?->getName());
        
        // 检查第二个参数是string $agentId
        $this->assertEquals('agentId', $authRedirectParams[1]->getName());
        $this->assertEquals('string', $authRedirectParams[1]->getType()?->getName());
    }
} 