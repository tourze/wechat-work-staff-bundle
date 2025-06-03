<?php

namespace WechatWorkStaffBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Service\SmartHttpClient;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatWorkStaffBundle\Command\CheckUserAvatarCommand;
use WechatWorkStaffBundle\Repository\UserRepository;

class CheckUserAvatarCommandTest extends TestCase
{
    private CheckUserAvatarCommand $command;
    private UserRepository&MockObject $userRepository;
    private SmartHttpClient&MockObject $httpClient;
    private FilesystemOperator&MockObject $mountManager;
    private LoggerInterface&MockObject $logger;
    private EntityManagerInterface&MockObject $entityManager;
    
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->httpClient = $this->createMock(SmartHttpClient::class);
        $this->mountManager = $this->createMock(FilesystemOperator::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->command = new CheckUserAvatarCommand(
            $this->userRepository,
            $this->httpClient,
            $this->mountManager,
            $this->logger,
            $this->entityManager
        );
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(CheckUserAvatarCommand::class, $this->command);
    }
    
    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:check-user-avatar', $this->command->getName());
        $this->assertSame('检查用户头像并保存', $this->command->getDescription());
    }
    
    public function testCommandIsInstantiable(): void
    {
        // 简单验证命令可以被实例化
        $this->assertNotNull($this->command);
        $this->assertInstanceOf(CheckUserAvatarCommand::class, $this->command);
    }
} 