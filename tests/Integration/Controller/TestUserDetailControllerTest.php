<?php

namespace WechatWorkStaffBundle\Tests\Integration\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Controller\TestUserDetailController;
use WechatWorkStaffBundle\Request\User\GetUserRequest;

class TestUserDetailControllerTest extends TestCase
{
    /**
     * @var CorpRepository&MockObject
     */
    private CorpRepository $corpRepository;
    
    /**
     * @var AgentRepository&MockObject
     */
    private AgentRepository $agentRepository;
    
    /**
     * @var WorkService&MockObject
     */
    private WorkService $workService;
    
    private TestUserDetailController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new TestUserDetailController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
        
        $container = new Container();
        $this->controller->setContainer($container);
    }

    public function testInvoke_withUserId_returnsUserDetail(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        $request->query->set('agentId', 'agent456');
        $request->query->set('userId', 'user123');
        
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('corp123')
            ->willReturn(null);
            
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent456',
            ])
            ->willReturn($agent);
        
        $userDetailData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'userid' => 'user123',
            'name' => '张三',
            'department' => [1, 2],
            'position' => '工程师',
            'mobile' => '13800138000',
            'email' => 'zhangsan@example.com',
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) use ($agent) {
                return $request instanceof GetUserRequest
                    && $request->getAgent() === $agent
                    && $request->getUserId() === 'user123';
            }))
            ->willReturn($userDetailData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($userDetailData), $response->getContent());
    }

    public function testInvoke_withCorpIdOnly_usesFirstAgent(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        $request->query->set('userId', 'user456');
        
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        
        $this->corpRepository->expects($this->once())
            ->method('find')
            ->with('corp123')
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corp' => $corp], ['id' => 'ASC'])
            ->willReturn($agent);
        
        $userDetailData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'userid' => 'user456',
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof GetUserRequest
                    && $request->getUserId() === 'user456';
            }))
            ->willReturn($userDetailData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}