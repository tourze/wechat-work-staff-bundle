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
use WechatWorkStaffBundle\Controller\TestSimpleUserListController;
use WechatWorkStaffBundle\Request\User\GetUserSimpleListRequest;

class TestSimpleUserListControllerTest extends TestCase
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
    
    private TestSimpleUserListController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new TestSimpleUserListController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
        
        $container = new Container();
        $this->controller->setContainer($container);
    }

    public function testInvoke_withDepartmentId_returnsUserList(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        $request->query->set('agentId', 'agent456');
        $request->query->set('departmentId', '1');
        
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
        
        $userListData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'userlist' => [
                ['userid' => 'user1', 'name' => '张三', 'department' => [1]],
                ['userid' => 'user2', 'name' => '李四', 'department' => [1]],
            ],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) use ($agent) {
                return $request instanceof GetUserSimpleListRequest
                    && $request->getAgent() === $agent
                    && $request->getDepartmentId() === 1;
            }))
            ->willReturn($userListData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($userListData), $response->getContent());
    }

    public function testInvoke_withoutAgentId_usesFirstAgent(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        $request->query->set('departmentId', '2');
        
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
        
        $userListData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'userlist' => [],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) {
                return $request instanceof GetUserSimpleListRequest
                    && $request->getDepartmentId() === 2;
            }))
            ->willReturn($userListData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}