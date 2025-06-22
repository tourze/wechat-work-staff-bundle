<?php

namespace WechatWorkStaffBundle\Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Controller\TestController;

class TestControllerTest extends TestCase
{
    private TestController $controller;
    private CorpRepository&MockObject $corpRepository;
    private AgentRepository&MockObject $agentRepository;
    private WorkService&MockObject $workService;
    
    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new TestController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(TestController::class, $this->controller);
    }
    
    public function testControllerExtendsAbstractController(): void
    {
        $reflection = new \ReflectionClass(TestController::class);
        $this->assertTrue($reflection->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\AbstractController'));
    }
    
    public function testControllerHasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(TestController::class);
        $methods = $reflection->getMethods();
        $methodNames = array_map(fn($method) => $method->getName(), $methods);
        
        $expectedMethods = [
            'departmentList',
            'simpleUserList', 
            'userIdList',
            'userDetail',
            'getAgent'
        ];
        
        foreach ($expectedMethods as $method) {
            $this->assertContains($method, $methodNames, "方法 {$method} 应该存在");
        }
    }
    
    public function testControllerMethodsArePublicOrProtected(): void
    {
        $reflection = new \ReflectionClass(TestController::class);
        
        $methods = [
            'departmentList' => 'public',
            'simpleUserList' => 'public',
            'userIdList' => 'public', 
            'userDetail' => 'public',
            'getAgent' => 'protected'
        ];
        
        foreach ($methods as $methodName => $expectedVisibility) {
            $method = $reflection->getMethod($methodName);
            
            if ($expectedVisibility === 'public') {
                $this->assertTrue($method->isPublic(), "方法 {$methodName} 应该是 public");
            } elseif ($expectedVisibility === 'protected') {
                $this->assertTrue($method->isProtected(), "方法 {$methodName} 应该是 protected");
            }
        }
    }
    
    public function testControllerHasRequiredDependencies(): void
    {
        $reflection = new \ReflectionClass(TestController::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(3, $parameters, '构造函数应该有3个参数');
        
        $parameterNames = array_map(fn($param) => $param->getName(), $parameters);
        $this->assertContains('corpRepository', $parameterNames);
        $this->assertContains('agentRepository', $parameterNames);
        $this->assertContains('workService', $parameterNames);
    }
}
