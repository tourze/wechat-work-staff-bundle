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
use WechatWorkStaffBundle\Controller\TestUserIdListController;
use WechatWorkStaffBundle\Request\User\ListIdRequest;

class TestUserIdListControllerTest extends TestCase
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
    
    private TestUserIdListController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new TestUserIdListController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
        
        $container = new Container();
        $this->controller->setContainer($container);
    }

    public function testInvoke_withCorpAndAgent_returnsUserIdList(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        $request->query->set('agentId', 'agent456');
        
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
        
        $userIdListData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'dept_user' => [
                [
                    'department' => 1,
                    'userid' => ['user1', 'user2', 'user3'],
                ],
                [
                    'department' => 2,
                    'userid' => ['user4', 'user5'],
                ],
            ],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) use ($agent) {
                return $request instanceof ListIdRequest
                    && $request->getAgent() === $agent;
            }))
            ->willReturn($userIdListData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($userIdListData), $response->getContent());
    }

    public function testInvoke_withOnlyCorpId_usesFirstAgent(): void
    {
        $request = new Request();
        $request->query->set('corpId', 'corp123');
        
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
        
        $userIdListData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'dept_user' => [],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($userIdListData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}