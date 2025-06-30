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
use WechatWorkStaffBundle\Controller\TestDepartmentListController;
use WechatWorkStaffBundle\Request\Department\GetDepartmentListRequest;

class TestDepartmentListControllerTest extends TestCase
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
    
    private TestDepartmentListController $controller;

    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->workService = $this->createMock(WorkService::class);
        
        $this->controller = new TestDepartmentListController(
            $this->corpRepository,
            $this->agentRepository,
            $this->workService
        );
        
        $container = new Container();
        $this->controller->setContainer($container);
    }

    public function testInvoke_withCorpIdAndAgentId_returnsDepartmentList(): void
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
        
        $departmentData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'department' => [
                ['id' => 1, 'name' => '总部', 'parentid' => 0],
                ['id' => 2, 'name' => '技术部', 'parentid' => 1],
            ],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->with($this->callback(function ($request) use ($agent) {
                return $request instanceof GetDepartmentListRequest
                    && $request->getAgent() === $agent;
            }))
            ->willReturn($departmentData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(json_encode($departmentData), $response->getContent());
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
        
        $departmentData = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'department' => [],
        ];
        
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn($departmentData);
        
        $response = $this->controller->__invoke($request);
        
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}