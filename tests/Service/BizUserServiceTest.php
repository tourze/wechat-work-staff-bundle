<?php

namespace WechatWorkStaffBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Service\BizUserService;

class BizUserServiceTest extends TestCase
{
    private UserManagerInterface $userManager;
    private UserRepository $userRepository;
    private BizUserService $bizUserService;
    
    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->bizUserService = new BizUserService($this->userManager, $this->userRepository);
    }
    
    public function testTransformFromExternalUser_WhenUserExists(): void
    {
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->once())
            ->method('getExternalUserId')
            ->willReturn('ext-user-123');
            
        $externalContact->expects($this->never())
            ->method('getNickname');
            
        $externalContact->expects($this->never())
            ->method('getAvatar');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('ext-user-123')
            ->willReturn($bizUser);
            
        $this->userManager->expects($this->never())
            ->method('createUser');
            
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($bizUser, $result);
    }
    
    public function testTransformFromExternalUser_WhenUserDoesNotExist_WithNickname(): void
    {
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->atLeastOnce())
            ->method('getExternalUserId')
            ->willReturn('ext-user-123');
            
        $externalContact->expects($this->atLeastOnce())
            ->method('getNickname')
            ->willReturn('External User');
            
        $externalContact->expects($this->once())
            ->method('getAvatar')
            ->willReturn('http://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('ext-user-123')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('ext-user-123', 'External User', 'http://example.com/avatar.jpg')
            ->willReturn($bizUser);
            
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($bizUser, $result);
    }
    
    public function testTransformFromExternalUser_WhenUserDoesNotExist_WithoutNickname(): void
    {
        $_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] = '默认名称';
        
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->atLeastOnce())
            ->method('getExternalUserId')
            ->willReturn('ext-user-123');
            
        $externalContact->expects($this->once())
            ->method('getNickname')
            ->willReturn('');
            
        $externalContact->expects($this->once())
            ->method('getAvatar')
            ->willReturn('http://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('ext-user-123')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('ext-user-123', '默认名称', 'http://example.com/avatar.jpg')
            ->willReturn($bizUser);
            
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($bizUser, $result);
        
        // 清理环境变量
        unset($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME']);
    }
    
    public function testTransformFromExternalUser_WithDefaultNickname(): void
    {
        // 不设置环境变量，测试默认值
        
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->atLeastOnce())
            ->method('getExternalUserId')
            ->willReturn('ext-user-123');
            
        $externalContact->expects($this->once())
            ->method('getNickname')
            ->willReturn('');
            
        $externalContact->expects($this->once())
            ->method('getAvatar')
            ->willReturn('http://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('ext-user-123')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('ext-user-123', '企微外部联系人', 'http://example.com/avatar.jpg')
            ->willReturn($bizUser);
            
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($bizUser, $result);
    }
    
    public function testTransformFromWorkUser_WhenUserExists(): void
    {
        $workUser = $this->createMock(User::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $workUser->expects($this->once())
            ->method('getUserId')
            ->willReturn('work-user-123');
            
        $workUser->expects($this->never())
            ->method('getName');
            
        $workUser->expects($this->never())
            ->method('getAvatarUrl');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work-user-123')
            ->willReturn($bizUser);
            
        $this->userManager->expects($this->never())
            ->method('createUser');
            
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($bizUser, $result);
    }
    
    public function testTransformFromWorkUser_WhenUserDoesNotExist_WithName(): void
    {
        $workUser = $this->createMock(User::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $workUser->expects($this->atLeastOnce())
            ->method('getUserId')
            ->willReturn('work-user-123');
            
        $workUser->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('Work User');
            
        $workUser->expects($this->once())
            ->method('getAvatarUrl')
            ->willReturn('http://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work-user-123')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('work-user-123', 'Work User', 'http://example.com/avatar.jpg')
            ->willReturn($bizUser);
            
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($bizUser, $result);
    }
    
    public function testTransformFromWorkUser_WhenUserDoesNotExist_WithoutName(): void
    {
        $_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] = '默认名称';
        
        $workUser = $this->createMock(User::class);
        $bizUser = $this->createMock(UserInterface::class);
        
        $workUser->expects($this->atLeastOnce())
            ->method('getUserId')
            ->willReturn('work-user-123');
            
        $workUser->expects($this->once())
            ->method('getName')
            ->willReturn('');
            
        $workUser->expects($this->once())
            ->method('getAvatarUrl')
            ->willReturn('http://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work-user-123')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('work-user-123', '默认名称', 'http://example.com/avatar.jpg')
            ->willReturn($bizUser);
            
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($bizUser, $result);
        
        // 清理环境变量
        unset($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME']);
    }
    
    public function testTransformToWorkUser(): void
    {
        $bizUser = $this->createMock(UserInterface::class);
        $workUser = $this->createMock(User::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user-123');
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => 'user-123'])
            ->willReturn($workUser);
            
        $result = $this->bizUserService->transformToWorkUser($bizUser);
        
        $this->assertSame($workUser, $result);
    }
    
    public function testTransformToWorkUser_NotFound(): void
    {
        $bizUser = $this->createMock(UserInterface::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user-123');
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => 'user-123'])
            ->willReturn(null);
            
        $result = $this->bizUserService->transformToWorkUser($bizUser);
        
        $this->assertNull($result);
    }
} 