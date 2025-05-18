<?php

namespace WechatWorkStaffBundle\Tests\Procedure\User;

use AccessTokenBundle\Entity\AccessToken;
use AccessTokenBundle\Service\AccessTokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Service\BizUserService;

class GetWechatWorkUserByAuthCodeTest extends TestCase
{
    private MockObject|CorpRepository $corpRepository;
    private MockObject|AgentRepository $agentRepository;
    private MockObject|UserRepository $userRepository;
    private MockObject|BizUserService $bizUserService;
    private MockObject|AccessTokenService $accessTokenService;
    private MockObject|WorkService $workService;
    private MockObject|LoggerInterface $logger;
    private MockObject|EntityManagerInterface $entityManager;
    private GetWechatWorkUserByAuthCode $procedure;
    
    protected function setUp(): void
    {
        $this->corpRepository = $this->createMock(CorpRepository::class);
        $this->agentRepository = $this->createMock(AgentRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->bizUserService = $this->createMock(BizUserService::class);
        $this->accessTokenService = $this->createMock(AccessTokenService::class);
        $this->workService = $this->createMock(WorkService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->procedure = new GetWechatWorkUserByAuthCode(
            $this->corpRepository,
            $this->agentRepository,
            $this->userRepository,
            $this->bizUserService,
            $this->accessTokenService,
            $this->workService,
            $this->logger,
            $this->entityManager
        );
        
        $this->procedure->corpId = 'corp-123';
        $this->procedure->agentId = 'agent-123';
        $this->procedure->code = 'auth-code-123';
    }
    
    public function testExecute(): void
    {
        $expectedResult = ['key' => 'value'];
        
        $this->procedure = $this->getMockBuilder(GetWechatWorkUserByAuthCode::class)
            ->setConstructorArgs([
                $this->corpRepository,
                $this->agentRepository,
                $this->userRepository,
                $this->bizUserService,
                $this->accessTokenService,
                $this->workService,
                $this->logger,
                $this->entityManager
            ])
            ->onlyMethods(['getResult'])
            ->getMock();
        
        $this->procedure->corpId = 'corp-123';
        $this->procedure->agentId = 'agent-123';
        $this->procedure->code = 'auth-code-123';
        
        $this->procedure->expects($this->once())
            ->method('getResult')
            ->with('corp-123', 'agent-123', 'auth-code-123')
            ->willReturn($expectedResult);
            
        $result = $this->procedure->execute();
        
        $this->assertSame($expectedResult, $result);
    }
    
    public function testGetResult_ThrowsNotFoundHttpException_WhenAgentNotFound(): void
    {
        $corp = $this->createMock(Corp::class);
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp-123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent-123',
            ])
            ->willReturn(null);
            
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('找不到指定应用');
        
        $this->procedure->getResult('corp-123', 'agent-123', 'auth-code-123');
    }
    
    public function testGetResult_ThrowsBadRequestException_WhenUserIdNotFound(): void
    {
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp-123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent-123',
            ])
            ->willReturn($agent);
            
        $this->workService->expects($this->once())
            ->method('request')
            ->willReturn([
                'errcode' => 0,
                'errmsg' => 'ok',
                // 没有 userid 字段
            ]);
            
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('找不到userId');
        
        $this->procedure->getResult('corp-123', 'agent-123', 'auth-code-123');
    }
    
    public function testGetResult_CreatesNewUser_WhenUserNotFound(): void
    {
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        $bizUser = $this->createMock(\Symfony\Component\Security\Core\User\UserInterface::class);
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp-123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent-123',
            ])
            ->willReturn($agent);
            
        // 模拟多次请求的返回值
        $this->workService->expects($this->exactly(3))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                [
                    'userid' => 'user-123',
                    'user_ticket' => 'ticket-123'
                ],
                [
                    'userid' => 'user-123',
                    'name' => 'Test User',
                    'avatar' => 'http://example.com/avatar.jpg',
                    'mobile' => '13800138000',
                    'email' => 'test@example.com'
                ],
                [
                    'avatar' => 'http://example.com/avatar_sensitive.jpg',
                    'mobile' => '13900139000',
                    'email' => 'sensitive@example.com'
                ]
            );
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'userId' => 'user-123',
                'corp' => $corp,
            ])
            ->willReturn(null);
            
        $this->bizUserService->expects($this->once())
            ->method('transformFromWorkUser')
            ->willReturn($bizUser);
            
        $accessToken = $this->createMock(AccessToken::class);
        $accessToken->method('__toString')
            ->willReturn('jwt-token-123');
        
        $this->accessTokenService->expects($this->once())
            ->method('createToken')
            ->with($bizUser)
            ->willReturn($accessToken);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($user) use ($corp, $agent) {
                $this->assertInstanceOf(User::class, $user);
                $this->assertSame($corp, $user->getCorp());
                $this->assertSame($agent, $user->getAgent());
                $this->assertEquals('user-123', $user->getUserId());
                $this->assertEquals('Test User', $user->getName());
                $this->assertEquals('http://example.com/avatar.jpg', $user->getAvatarUrl());
                $this->assertEquals('test@example.com', $user->getEmail());
                $this->assertEquals('13800138000', $user->getMobile());
                return true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $result = $this->procedure->getResult('corp-123', 'agent-123', 'auth-code-123');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('jwt', $result);
        $this->assertEquals('jwt-token-123', $result['jwt']);
    }
    
    public function testGetResult_UpdatesExistingUser(): void
    {
        $corp = $this->createMock(Corp::class);
        $agent = $this->createMock(Agent::class);
        $existingUser = $this->createMock(User::class);
        $bizUser = $this->createMock(\Symfony\Component\Security\Core\User\UserInterface::class);
        
        $this->corpRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['corpId' => 'corp-123'])
            ->willReturn($corp);
            
        $this->agentRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'corp' => $corp,
                'agentId' => 'agent-123',
            ])
            ->willReturn($agent);
            
        // 模拟第一次请求 - 获取用户ID
        $this->workService->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                [
                    'userid' => 'user-123'
                ],
                [
                    'userid' => 'user-123',
                    'name' => 'Updated User',
                    'avatar' => 'http://example.com/avatar.jpg',
                    'mobile' => '13800138000',
                    'email' => 'test@example.com'
                ]
            );
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'userId' => 'user-123',
                'corp' => $corp,
            ])
            ->willReturn($existingUser);
            
        $existingUser->expects($this->once())
            ->method('setName')
            ->with('Updated User');
            
        $existingUser->expects($this->once())
            ->method('setAvatarUrl')
            ->with('http://example.com/avatar.jpg');
            
        $existingUser->expects($this->once())
            ->method('setEmail')
            ->with('test@example.com');
            
        $existingUser->expects($this->once())
            ->method('setMobile')
            ->with('13800138000');
            
        $this->bizUserService->expects($this->once())
            ->method('transformFromWorkUser')
            ->with($existingUser)
            ->willReturn($bizUser);
            
        $accessToken = $this->createMock(AccessToken::class);
        $accessToken->method('__toString')
            ->willReturn('jwt-token-123');
        
        $this->accessTokenService->expects($this->once())
            ->method('createToken')
            ->with($bizUser)
            ->willReturn($accessToken);
            
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($existingUser);
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        $result = $this->procedure->getResult('corp-123', 'agent-123', 'auth-code-123');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('jwt', $result);
        $this->assertEquals('jwt-token-123', $result['jwt']);
    }
} 