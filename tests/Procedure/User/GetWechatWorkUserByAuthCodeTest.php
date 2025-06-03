<?php

namespace WechatWorkStaffBundle\Tests\Procedure\User;

use AccessTokenBundle\Service\AccessTokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WechatWorkStaffBundle\Service\BizUserService;

class GetWechatWorkUserByAuthCodeTest extends TestCase
{
    public function testConstructor(): void
    {
        $corpRepository = $this->createMock(CorpRepository::class);
        $agentRepository = $this->createMock(AgentRepository::class);
        $userLoader = $this->createMock(UserLoaderInterface::class);
        $bizUserService = $this->createMock(BizUserService::class);
        $accessTokenService = $this->createMock(AccessTokenService::class);
        $workService = $this->createMock(WorkService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $procedure = new GetWechatWorkUserByAuthCode(
            $corpRepository,
            $agentRepository,
            $userLoader,
            $bizUserService,
            $accessTokenService,
            $workService,
            $logger,
            $entityManager
        );
        
        $this->assertInstanceOf(GetWechatWorkUserByAuthCode::class, $procedure);
        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }
    
    public function testPublicPropertiesExist(): void
    {
        $corpRepository = $this->createMock(CorpRepository::class);
        $agentRepository = $this->createMock(AgentRepository::class);
        $userLoader = $this->createMock(UserLoaderInterface::class);
        $bizUserService = $this->createMock(BizUserService::class);
        $accessTokenService = $this->createMock(AccessTokenService::class);
        $workService = $this->createMock(WorkService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $procedure = new GetWechatWorkUserByAuthCode(
            $corpRepository,
            $agentRepository,
            $userLoader,
            $bizUserService,
            $accessTokenService,
            $workService,
            $logger,
            $entityManager
        );
        
        // 测试类的公共属性是否存在（参数接收属性）
        $reflection = new \ReflectionClass($procedure);
        
        $this->assertTrue($reflection->hasProperty('corpId'));
        $this->assertTrue($reflection->hasProperty('agentId'));
        $this->assertTrue($reflection->hasProperty('code'));
        
        $corpIdProperty = $reflection->getProperty('corpId');
        $agentIdProperty = $reflection->getProperty('agentId');
        $codeProperty = $reflection->getProperty('code');
        
        $this->assertTrue($corpIdProperty->isPublic());
        $this->assertTrue($agentIdProperty->isPublic());
        $this->assertTrue($codeProperty->isPublic());
    }
    
    public function testParameterAssignment(): void
    {
        $corpRepository = $this->createMock(CorpRepository::class);
        $agentRepository = $this->createMock(AgentRepository::class);
        $userLoader = $this->createMock(UserLoaderInterface::class);
        $bizUserService = $this->createMock(BizUserService::class);
        $accessTokenService = $this->createMock(AccessTokenService::class);
        $workService = $this->createMock(WorkService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $procedure = new GetWechatWorkUserByAuthCode(
            $corpRepository,
            $agentRepository,
            $userLoader,
            $bizUserService,
            $accessTokenService,
            $workService,
            $logger,
            $entityManager
        );
        
        $corpId = 'test_corp_123';
        $agentId = 'test_agent_456';
        $code = 'test_code_789';
        
        $procedure->corpId = $corpId;
        $procedure->agentId = $agentId;
        $procedure->code = $code;
        
        $this->assertSame($corpId, $procedure->corpId);
        $this->assertSame($agentId, $procedure->agentId);
        $this->assertSame($code, $procedure->code);
    }
    
    public function testExecuteMethodExists(): void
    {
        $corpRepository = $this->createMock(CorpRepository::class);
        $agentRepository = $this->createMock(AgentRepository::class);
        $userLoader = $this->createMock(UserLoaderInterface::class);
        $bizUserService = $this->createMock(BizUserService::class);
        $accessTokenService = $this->createMock(AccessTokenService::class);
        $workService = $this->createMock(WorkService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $procedure = new GetWechatWorkUserByAuthCode(
            $corpRepository,
            $agentRepository,
            $userLoader,
            $bizUserService,
            $accessTokenService,
            $workService,
            $logger,
            $entityManager
        );
        
        $this->assertTrue(method_exists($procedure, 'execute'));
        $this->assertTrue(method_exists($procedure, 'getResult'));
    }
} 