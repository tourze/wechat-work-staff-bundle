<?php

namespace WechatWorkStaffBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;
use WechatWorkStaffBundle\Service\BizUserService;

class BizUserServiceTest extends TestCase
{
    private BizUserService $bizUserService;
    private UserManagerInterface&MockObject $userManager;
    private UserRepository&MockObject $userRepository;
    
    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->bizUserService = new BizUserService($this->userManager, $this->userRepository);
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(BizUserService::class, $this->bizUserService);
    }
    
    public function testTransformFromExternalUserWithExistingUser(): void
    {
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $existingBizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->once())
            ->method('getExternalUserId')
            ->willReturn('external_123');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('external_123')
            ->willReturn($existingBizUser);
            
        $this->userManager->expects($this->never())
            ->method('createUser');
        
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($existingBizUser, $result);
    }
    
    public function testTransformFromExternalUserWithNewUser(): void
    {
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $newBizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->exactly(2))
            ->method('getExternalUserId')
            ->willReturn('external_456');
            
        $externalContact->expects($this->exactly(2))
            ->method('getNickname')
            ->willReturn('外部用户');
            
        $externalContact->expects($this->once())
            ->method('getAvatar')
            ->willReturn('https://example.com/avatar.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('external_456')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('external_456', '外部用户', 'https://example.com/avatar.jpg')
            ->willReturn($newBizUser);
        
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($newBizUser, $result);
    }
    
    public function testTransformFromExternalUserWithEmptyNickname(): void
    {
        $externalContact = $this->createMock(ExternalContactInterface::class);
        $newBizUser = $this->createMock(UserInterface::class);
        
        $externalContact->expects($this->exactly(2))
            ->method('getExternalUserId')
            ->willReturn('external_789');
            
        $externalContact->expects($this->once())
            ->method('getNickname')
            ->willReturn('');
            
        $externalContact->expects($this->once())
            ->method('getAvatar')
            ->willReturn('https://example.com/avatar2.jpg');
            
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('external_789')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('external_789', '企微外部联系人', 'https://example.com/avatar2.jpg')
            ->willReturn($newBizUser);
        
        $result = $this->bizUserService->transformFromExternalUser($externalContact);
        
        $this->assertSame($newBizUser, $result);
    }
    
    public function testTransformFromWorkUserWithExistingUser(): void
    {
        $workUser = new User();
        $workUser->setUserId('work_123');
        $workUser->setName('企微员工');
        
        $existingBizUser = $this->createMock(UserInterface::class);
        
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work_123')
            ->willReturn($existingBizUser);
            
        $this->userManager->expects($this->never())
            ->method('createUser');
        
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($existingBizUser, $result);
    }
    
    public function testTransformFromWorkUserWithNewUser(): void
    {
        $workUser = new User();
        $workUser->setUserId('work_456');
        $workUser->setName('新企微员工');
        $workUser->setAvatarUrl('https://work.example.com/avatar.jpg');
        
        $newBizUser = $this->createMock(UserInterface::class);
        
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work_456')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('work_456', '新企微员工', 'https://work.example.com/avatar.jpg')
            ->willReturn($newBizUser);
        
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($newBizUser, $result);
    }
    
    public function testTransformFromWorkUserWithEmptyName(): void
    {
        $workUser = new User();
        $workUser->setUserId('work_789');
        $workUser->setName('');
        $workUser->setAvatarUrl('https://work.example.com/avatar2.jpg');
        
        $newBizUser = $this->createMock(UserInterface::class);
        
        $this->userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('work_789')
            ->willReturn(null);
            
        $this->userManager->expects($this->once())
            ->method('createUser')
            ->with('work_789', '企业微信用户', 'https://work.example.com/avatar2.jpg')
            ->willReturn($newBizUser);
        
        $result = $this->bizUserService->transformFromWorkUser($workUser);
        
        $this->assertSame($newBizUser, $result);
    }
    
    public function testTransformToWorkUserFound(): void
    {
        $bizUser = $this->createMock(UserInterface::class);
        $workUser = new User();
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user_123');
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => 'user_123'])
            ->willReturn($workUser);
        
        $result = $this->bizUserService->transformToWorkUser($bizUser);
        
        $this->assertSame($workUser, $result);
    }
    
    public function testTransformToWorkUserNotFound(): void
    {
        $bizUser = $this->createMock(UserInterface::class);
        
        $bizUser->expects($this->once())
            ->method('getUserIdentifier')
            ->willReturn('user_404');
            
        $this->userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['userId' => 'user_404'])
            ->willReturn(null);
        
        $result = $this->bizUserService->transformToWorkUser($bizUser);
        
        $this->assertNull($result);
    }
} 