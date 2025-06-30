<?php

namespace WechatWorkStaffBundle\Tests\Integration\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use WechatWorkStaffBundle\Controller\AuthCallbackController;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkUserByAuthCode;
use WeuiBundle\Service\NoticeService;

class AuthCallbackControllerTest extends TestCase
{
    /**
     * @var GetWechatWorkUserByAuthCode&MockObject
     */
    private GetWechatWorkUserByAuthCode $codeApi;
    
    /**
     * @var NoticeService&MockObject
     */
    private NoticeService $noticeService;
    
    /**
     * @var Environment&MockObject
     */
    private Environment $twig;
    
    private AuthCallbackController $controller;

    protected function setUp(): void
    {
        $this->codeApi = $this->createMock(GetWechatWorkUserByAuthCode::class);
        $this->noticeService = $this->createMock(NoticeService::class);
        $this->twig = $this->createMock(Environment::class);
        
        $this->controller = new AuthCallbackController(
            $this->codeApi,
            $this->noticeService,
            $this->twig
        );
    }

    public function testInvoke_withCallbackUrl_redirectsToRenderedUrl(): void
    {
        $request = new Request();
        $request->query->set('code', 'test-code');
        $request->query->set('callbackUrl', 'https://example.com/callback?jwt={{ jwt }}');
        
        $userResult = [
            'name' => 'Test User',
            'jwt' => 'test-jwt-token',
            'id' => '123',
        ];
        
        $this->codeApi->expects($this->once())
            ->method('getResult')
            ->with('corp123', 'agent456', 'test-code')
            ->willReturn($userResult);
        
        // 使用真实的 Twig 环境，但加载测试模板
        $twig = new Environment(new \Twig\Loader\ArrayLoader());
        
        $controller = new AuthCallbackController(
            $this->codeApi,
            $this->noticeService,
            $twig
        );
        
        $response = $controller->__invoke('corp123', 'agent456', $request);
        
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('https://example.com/callback?jwt=test-jwt-token', $response->getTargetUrl());
    }

    public function testInvoke_withoutCallbackUrl_returnsSuccessNotice(): void
    {
        $request = new Request();
        $request->query->set('code', 'test-code');
        
        $userResult = [
            'name' => 'Test User',
            'jwt' => 'test-jwt-token',
            'id' => '123',
        ];
        
        $this->codeApi->expects($this->once())
            ->method('getResult')
            ->with('corp123', 'agent456', 'test-code')
            ->willReturn($userResult);
            
        $successResponse = new Response('Success');
        
        $this->noticeService->expects($this->once())
            ->method('weuiSuccess')
            ->with('授权成功', '欢迎你，Test User')
            ->willReturn($successResponse);
        
        $response = $this->controller->__invoke('corp123', 'agent456', $request);
        
        $this->assertSame($successResponse, $response);
    }
}